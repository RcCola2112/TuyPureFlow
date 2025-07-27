<?php
session_start();
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

// Fetch inventory for this distributor's shop(s)
$stmt = $conn->prepare(
  "SELECT ci.type AS item, ci.stock_quantity AS available, 0 AS in_transit, 0 AS reserved, 0 AS damaged, 10 AS threshold
   FROM container ci
   WHERE ci.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)"
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

      <!-- Inventory Table -->
      <div class="bg-white shadow rounded-lg p-4 mb-6 overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-blue-100 text-blue-800">
          <tr>
            <th class="px-4 py-2">Item</th>
            <th class="px-4 py-2">Available</th>
            <th class="px-4 py-2">In Transit</th>
            <th class="px-4 py-2">Reserved</th>
            <th class="px-4 py-2">Damaged</th>
            <th class="px-4 py-2">Low Stock Threshold</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>          <?php foreach ($inventory as $inv): ?>
          <tr class="hover:bg-gray-50 border-t cursor-pointer" onclick="showDetail('<?= htmlspecialchars($inv['item']) ?>', <?= $inv['available'] ?>, <?= $inv['in_transit'] ?>, <?= $inv['reserved'] ?>, <?= $inv['damaged'] ?>, <?= $inv['threshold'] ?>)">
            <td class="px-4 py-2"><?= htmlspecialchars($inv['item']) ?></td>
            <td class="px-4 py-2"><?= $inv['available'] ?></td>
            <td class="px-4 py-2"><?= $inv['in_transit'] ?></td>
            <td class="px-4 py-2"><?= $inv['reserved'] ?></td>
            <td class="px-4 py-2"><?= $inv['damaged'] ?></td>
            <td class="px-4 py-2"><?= $inv['threshold'] ?></td>
            <td class="px-4 py-2 text-blue-600">Edit</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Edit Detail Panel -->
    <div id="detail-panel" class="hidden bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-bold mb-4">Edit Inventory Item</h2>
      <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-600">Item Name</label>
          <input id="item-name" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Available</label>
          <input id="available" type="number" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">In Transit</label>
          <input id="in-transit" type="number" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Reserved</label>
          <input id="reserved" type="number" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Damaged</label>
          <input id="damaged" type="number" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Low Stock Threshold</label>
          <input id="threshold" type="number" class="w-full border rounded p-2">
        </div>
      </form>
      <div class="mt-4 flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="hideDetail()">Cancel</button>
      </div>
    </div>
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
    }
  </script>

</body>
</html>
