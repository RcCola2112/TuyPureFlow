<?php
session_start();
if (!isset($_SESSION['distributor_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';
$currentPage = 'inventory';

// Get distributor info from session or database
$distributor_id = $_SESSION['distributor_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch();

$username = $distributor['name'] ?? '';
$stmtShop = $conn->prepare("SELECT name FROM shop WHERE distributor_id = ? LIMIT 1");
$stmtShop->execute([$distributor_id]);
$shopname = $stmtShop->fetchColumn() ?: '';
$profilePic = isset($distributor['profile_pic']) && $distributor['profile_pic'] ? $distributor['profile_pic'] : "images/profile.jpg";

// Handle add item form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_type = trim($_POST['item_type'] ?? '');
    $price_new = floatval($_POST['price_new'] ?? 0);
    $price_refill = floatval($_POST['price_refill'] ?? 0);
    $item_stock = intval($_POST['item_stock'] ?? 0);
    $damaged_container = intval($_POST['damaged_container'] ?? 0);
    $missing_container = intval($_POST['missing_container'] ?? 0);
    // Get the shop_id for this distributor (assuming one shop per distributor)
    $shop_stmt = $conn->prepare("SELECT shop_id FROM shop WHERE distributor_id = ? LIMIT 1");
    $shop_stmt->execute([$distributor_id]);
    $shop_id = $shop_stmt->fetchColumn();
    if ($shop_id && $item_type && $price_new >= 0 && $price_refill >= 0 && $item_stock >= 0 && $damaged_container >= 0 && $missing_container >= 0) {
        $insert_stmt = $conn->prepare("INSERT INTO container (shop_id, type, price_new, price_refill, stock_quantity, damaged_container, missing_container) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([$shop_id, $item_type, $price_new, $price_refill, $item_stock, $damaged_container, $missing_container]);
        header('Location: inventory.php');
        exit;
    }
}
$edit_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $edit_id = intval($_POST['edit_container_id'] ?? 0);
    $edit_type = trim($_POST['edit_item_type'] ?? '');
    $edit_price_new = floatval($_POST['edit_price_new'] ?? 0);
    $edit_price_refill = floatval($_POST['edit_price_refill'] ?? 0);
    $edit_stock = intval($_POST['edit_item_stock'] ?? 0);
    $edit_damaged = intval($_POST['edit_damaged_container'] ?? 0);
    $edit_missing = intval($_POST['edit_missing_container'] ?? 0);
    if ($edit_id && $edit_type && $edit_price_new >= 0 && $edit_price_refill >= 0 && $edit_stock >= 0 && $edit_damaged >= 0 && $edit_missing >= 0) {
        $update_stmt = $conn->prepare("UPDATE container SET type=?, price_new=?, price_refill=?, stock_quantity=?, damaged_container=?, missing_container=? WHERE container_id=?");
        $update_stmt->execute([$edit_type, $edit_price_new, $edit_price_refill, $edit_stock, $edit_damaged, $edit_missing, $edit_id]);
        header('Location: inventory.php');
        exit;
    } else {
        $edit_error = 'Invalid input. Please check your values.';
    }
}

// Fetch inventory for this distributor's shop(s)
$stmt = $conn->prepare(
  "SELECT ci.container_id, ci.type AS item, ci.stock_quantity AS available, ci.price_new, ci.price_refill, ci.damaged_container, ci.missing_container
   FROM container ci
   WHERE ci.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
   ORDER BY ci.type"
);
$stmt->execute([$distributor_id]);
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CDN -->
</head>
<body class="flex bg-gray-100">

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main class="p-6">
      <h1 class="text-2xl font-semibold text-gray-700 mb-6">Inventory</h1>

      <!-- Add Item Form -->
      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold mb-4">Add New Item</h2>
        <form method="POST" class="flex flex-wrap gap-4 items-end">
          <div>
            <label class="block text-sm font-medium text-gray-700">Item Type</label>
            <input type="text" name="item_type" required class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Price (₱) With Container</label>
            <input type="number" name="price_new" step="0.01" min="0" required class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Price (₱) Refill Only</label>
            <input type="number" name="price_refill" step="0.01" min="0" required class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Initial Stock</label>
            <input type="number" name="item_stock" min="0" required class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Damaged</label>
            <input type="number" name="damaged_container" min="0" value="0" required class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Missing</label>
            <input type="number" name="missing_container" min="0" value="0" required class="border rounded px-3 py-2">
          </div>
          <button type="submit" name="add_item" class="bg-blue-600 text-white px-4 py-2 rounded">Add Item</button>
        </form>
      </div>
      <!-- Inventory Table -->
      <div class="bg-white shadow rounded-lg p-4 mb-6 overflow-x-auto">
      <?php if ($edit_error): ?>
        <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($edit_error) ?></div>
      <?php endif; ?>
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-blue-100 text-blue-800">
          <tr>
            <th class="px-4 py-2">Item</th>
            <th class="px-4 py-2">Available</th>
            <th class="px-4 py-2">Price (With Container)</th>
            <th class="px-4 py-2">Price (Refill Only)</th>
            <th class="px-4 py-2">Damaged</th>
            <th class="px-4 py-2">Missing</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($inventory as $idx => $inv): ?>
          <tr class="hover:bg-gray-50 border-t">
            <form method="POST" class="contents">
              <input type="hidden" name="edit_container_id" value="<?= htmlspecialchars($inv['container_id'] ?? $idx) ?>">
              <td class="px-4 py-2">
                <input type="text" name="edit_item_type" value="<?= htmlspecialchars($inv['item']) ?>" class="border rounded px-2 py-1 w-24" required>
              </td>
              <td class="px-4 py-2">
                <input type="number" name="edit_item_stock" value="<?= $inv['available'] ?>" min="0" class="border rounded px-2 py-1 w-16" required>
              </td>
              <td class="px-4 py-2">
                <input type="number" name="edit_price_new" value="<?= $inv['price_new'] ?>" min="0" step="0.01" class="border rounded px-2 py-1 w-20" required>
              </td>
              <td class="px-4 py-2">
                <input type="number" name="edit_price_refill" value="<?= $inv['price_refill'] ?>" min="0" step="0.01" class="border rounded px-2 py-1 w-20" required>
              </td>
              <td class="px-4 py-2">
                <input type="number" name="edit_damaged_container" value="<?= $inv['damaged_container'] ?>" min="0" class="border rounded px-2 py-1 w-16" required>
              </td>
              <td class="px-4 py-2">
                <input type="number" name="edit_missing_container" value="<?= $inv['missing_container'] ?>" min="0" class="border rounded px-2 py-1 w-16" required>
              </td>
              <td class="px-4 py-2 text-blue-600">
                <button type="submit" name="update_item" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Edit Detail Panel removed: replaced by inline edit forms -->
  </div>

  <!-- JavaScript -->
  <script>
    function showDetail(name, available, transit, reserved, damaged, threshold) {
      document.getElementById('detail-panel').classList.remove('hidden');
      document.getElementById('item-name').value = name;
      document.getElementById('available').value = available;
      document.getElementById('in-transit').value = transit;
      document.getElementById('reserved').value = reserved;
      document.getElementById('damaged').value = damaged;
      document.getElementById('threshold').value = threshold;
    }

    function hideDetail() {
      document.getElementById('detail-panel').classList.add('hidden');
    }
  </script>

</body>
</html>
</body>
</html>
