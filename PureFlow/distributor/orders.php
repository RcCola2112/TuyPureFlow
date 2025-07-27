<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['distributor_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';
$currentPage = 'orders';

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

// Handle order status update (single or bulk)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bulk update
    if (!empty($_POST['bulk_status']) && !empty($_POST['selected_orders']) && is_array($_POST['selected_orders'])) {
        $new_status = $_POST['bulk_status'];
        $order_ids = $_POST['selected_orders'];
        $in = str_repeat('?,', count($order_ids) - 1) . '?';
        $stmt2 = $conn->prepare("UPDATE orders SET status = ? WHERE order_id IN ($in)");
        $stmt2->execute(array_merge([$new_status], $order_ids));

        // If status is set to Approved, create delivery_record for each order
        if ($new_status === 'Approved') {
            foreach ($order_ids as $oid) {
                $check = $conn->prepare("SELECT delivery_id FROM delivery_record WHERE order_id = ?");
                $check->execute([$oid]);
                if (!$check->fetch()) {
                    $ins = $conn->prepare("INSERT INTO delivery_record (order_id, delivery_status) VALUES (?, 'Scheduled')");
                    $ins->execute([$oid]);
                }
            }
        }
    }
    // Single update
    if (!empty($_POST['order_id']) && !empty($_POST['single_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['single_status'];
        $stmt2 = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt2->execute([$new_status, $order_id]);

        // If status is set to Approved, create delivery_record for this order
        if ($new_status === 'Approved') {
            $check = $conn->prepare("SELECT delivery_id FROM delivery_record WHERE order_id = ?");
            $check->execute([$order_id]);
            if (!$check->fetch()) {
                $ins = $conn->prepare("INSERT INTO delivery_record (order_id, delivery_status) VALUES (?, 'Scheduled')");
                $ins->execute([$order_id]);
            }
        }
    }
    header('Location: orders.php');
    exit;
}

// Fetch all orders for this distributor's shop(s) except those with status 'Approved'
// Use full_name from consumer table instead of first_name/last_name
// Use correct column name for province (region) in address table
$stmt = $conn->prepare("SELECT o.*, c.full_name AS customer_name, c.contact_number, c.email, a.street, a.city, a.region AS province, a.zip_code
  FROM orders o
  JOIN consumer c ON o.consumer_id = c.consumer_id
  LEFT JOIN address a ON c.consumer_id = a.consumer_id
  WHERE o.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
    AND o.status != 'Approved'
  ORDER BY o.order_date DESC");
$stmt->execute([$distributor_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Distributor Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 min-h-screen">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col min-h-screen ml-64">

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main class="flex-1 p-6 overflow-x-auto">
      <h1 class="text-2xl font-semibold text-gray-700 mb-4">Order Management</h1>
      <form method="POST">
        <table class="min-w-full bg-white rounded shadow overflow-hidden">
          <thead class="bg-blue-100 text-blue-800">
            <tr>
              <th class="px-4 py-2"><input type="checkbox" id="selectAllOrders"></th>
              <th class="px-4 py-2 text-left">Order ID</th>
              <th class="px-4 py-2 text-left">Customer Name</th>
              <th class="px-4 py-2 text-left">Phone Number</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Address</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">
                <input type="checkbox" class="order-checkbox" name="selected_orders[]" value="<?= $order['order_id'] ?>">
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['order_id']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['customer_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['contact_number']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['email']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['street'] . ', ' . $order['city'] . ', ' . $order['region'] . ' ' . $order['zip_code']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($order['status']) ?></td>
              <td class="px-4 py-2">
                <form method="POST" class="inline">
                  <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                  <select name="single_status" class="border rounded p-1">
                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Out for Delivery" <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                    <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                  </select>
                  <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded ml-2">Update</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="mt-4 bg-white p-4 rounded shadow border-t">
          <h2 class="text-lg font-semibold mb-2">Bulk Actions</h2>
          <div class="flex items-center gap-4">
            <select name="bulk_status" class="border rounded p-2">
              <option value="">Change Status</option>
              <option value="Approved">Approve</option>
              <option value="Cancelled">Cancel</option>
              <option value="Completed">Mark as Delivered</option>
              <option value="Processing">Processing</option>
              <option value="Out for Delivery">Out for Delivery</option>
              <option value="Pending">Pending</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Apply Changes</button>
          </div>
        </div>
      </form>

      <!-- Status Modal -->
      <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 relative">
          <button onclick="closeStatusModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
          <h2 class="text-lg font-semibold mb-4">Update Order Status</h2>
          <form method="POST" id="statusForm">
            <input type="hidden" name="order_id" id="modal_order_id">
            <select name="single_status" id="modal_status" class="border rounded p-2 w-full mb-4">
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Processing">Processing</option>
              <option value="Out for Delivery">Out for Delivery</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Update</button>
          </form>
        </div>
      </div>
    </main>
  </div>
  <script>
    document.getElementById('selectAllOrders').addEventListener('change', function() {
      document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
    });
    function openStatusModal(orderId, status) {
      document.getElementById('modal_order_id').value = orderId;
      document.getElementById('modal_status').value = status;
      document.getElementById('statusModal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
    function closeStatusModal() {
      document.getElementById('statusModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>

      <!-- Single Detail Card -->
      <div id="detail-card" class="mt-4 hidden bg-white p-4 rounded shadow border-t">
        <h2 class="text-lg font-semibold mb-2">Order Details</h2>
        <div class="grid grid-cols-2 gap-4">
          <div><strong>Order ID:</strong> ORD001</div>
          <div><strong>Date:</strong> June 4, 2025</div>
          <div><strong>Customer:</strong> Maria Santos</div>
          <div><strong>Phone:</strong> 09171234567</div>
          <div><strong>Email:</strong> maria@example.com</div>
          <div><strong>Address:</strong> Brgy. 1, Tuy, Batangas</div>
          <div class="col-span-2">
            <strong>Status:</strong>
            <select class="border rounded p-1 ml-2">
              <option>Pending</option>
              <option>Approved</option>
              <option>Delivered</option>
              <option>Cancelled</option>
            </select>
          </div>
          <div class="col-span-2 mt-4 flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            <button class="bg-gray-600 text-white px-4 py-2 rounded">Print</button>
            <button class="bg-red-600 text-white px-4 py-2 rounded">Cancel Order</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
