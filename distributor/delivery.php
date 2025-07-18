<?php
session_start();
include '../db.php';
$currentPage = 'delivery';

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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delivery_id'], $_POST['action'])) {
    $delivery_id = intval($_POST['delivery_id']);
    $action = $_POST['action'];
    if ($action === 'deliver') {
        $stmt = $conn->prepare("UPDATE delivery_record SET status = 'In Transit' WHERE delivery_id = ?");
        $stmt->execute([$delivery_id]);
        // Also update the order status to 'Out for Delivery'
        $stmt = $conn->prepare("UPDATE orders SET status = 'Out for Delivery' WHERE order_id = (SELECT order_id FROM delivery_record WHERE delivery_id = ?)");
        $stmt->execute([$delivery_id]);
    }
    // Optionally handle other actions (Delivered, Failed, etc.)
}

// Fetch waiting deliveries (approved orders not yet delivered)
$stmt = $conn->prepare(
    "SELECT dr.*, o.order_id, o.status AS order_status, c.name AS customer_name, s.name AS shop_name
     FROM delivery_record dr
     JOIN orders o ON dr.order_id = o.order_id
     JOIN consumer c ON o.consumer_id = c.consumer_id
     JOIN shop s ON o.shop_id = s.shop_id
     WHERE o.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
       AND o.status = 'Approved'
       AND dr.status = 'Scheduled'
     ORDER BY dr.delivery_date DESC"
);
$stmt->execute([$distributor_id]);
$deliveries = $stmt->fetchAll();

// Fetch ongoing deliveries (In Transit)
$stmt2 = $conn->prepare(
    "SELECT dr.*, o.order_id, o.status AS order_status, c.name AS customer_name, s.name AS shop_name
     FROM delivery_record dr
     JOIN orders o ON dr.order_id = o.order_id
     JOIN consumer c ON o.consumer_id = c.consumer_id
     JOIN shop s ON o.shop_id = s.shop_id
     WHERE o.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
       AND dr.status = 'In Transit'
     ORDER BY dr.delivery_date DESC"
);
$stmt2->execute([$distributor_id]);
$ongoing_deliveries = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delivery Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Add your map library here if needed -->
  <script src="https://cdn.blightmap.com/js/blightmap.min.js"></script>
</head>
<body class="flex bg-gray-100">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>
  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Page Content -->
    <main class="p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Delivery Management</h1>
      <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Waiting to be Delivered</h2>
      <?php if ($deliveries): ?>
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-blue-100 text-blue-800">
          <tr>
            <th class="px-4 py-2">Delivery ID</th>
            <th class="px-4 py-2">Order ID</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">Shop</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($deliveries as $d): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2"><?= htmlspecialchars($d['delivery_id']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['order_id']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['customer_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['shop_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['delivery_date']) ?></td>
            <td class="px-4 py-2"><span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($d['status']) ?></span></td>
            <td class="px-4 py-2">
              <form method="POST" class="inline">
                <input type="hidden" name="delivery_id" value="<?= $d['delivery_id'] ?>">
                <input type="hidden" name="action" value="deliver">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Set to Deliver</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="text-gray-500">No deliveries waiting to be delivered.</p>
      <?php endif; ?>
    </div>

    <!-- Map Section (shown after Set to Deliver is clicked) -->
    <div id="mapSection" class="bg-white shadow rounded-lg p-6 mb-8 hidden">
      <h2 class="text-lg font-semibold mb-4">Delivery Route Map</h2>
      <div id="deliveryMap" style="height: 350px; width: 100%;" class="mb-4"></div>
      <form method="POST" id="sendToDeliveryForm">
        <input type="hidden" name="delivery_id" id="map_delivery_id" value="">
        <input type="hidden" name="action" value="send_to_delivery">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Sent to Delivery</button>
      </form>
    </div>

    <!-- On-Going Delivery Section -->
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold mb-4">On-Going Delivery</h2>
      <?php if ($ongoing_deliveries): ?>
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-blue-100 text-blue-800">
          <tr>
            <th class="px-4 py-2">Delivery ID</th>
            <th class="px-4 py-2">Order ID</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">Shop</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ongoing_deliveries as $d): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2"><?= htmlspecialchars($d['delivery_id']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['order_id']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['customer_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['shop_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($d['delivery_date']) ?></td>
            <td class="px-4 py-2"><span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($d['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="text-gray-500">No ongoing deliveries.</p>
      <?php endif; ?>
    </div>
    </main>
  </div>
  <script>
    // Show map section when Set to Deliver is clicked
    document.querySelectorAll('form[action=""][method="POST"] input[name="action"][value="deliver"]').forEach(function(input) {
      input.closest('form').addEventListener('submit', function(e) {
        e.preventDefault();
        var deliveryId = this.querySelector('input[name="delivery_id"]').value;
        document.getElementById('mapSection').classList.remove('hidden');
        document.getElementById('map_delivery_id').value = deliveryId;
        // Initialize map (replace with your map logic)
        if (window.deliveryMapInstance) return;
        window.deliveryMapInstance = new BlightMap.Map({
          apiKey: "YOUR_API_KEY",
          container: "deliveryMap",
          center: [13.8602, 120.7335], // Default Tuy, Batangas
          zoom: 13
        });
        // Optionally add route, marker, etc.
      });
    });

    // Handle Sent to Delivery button
    document.getElementById('sendToDeliveryForm').addEventListener('submit', function(e) {
      // Optionally handle AJAX or let form submit normally
      // For now, let it submit
    });
  </script>
</body>
</html>
