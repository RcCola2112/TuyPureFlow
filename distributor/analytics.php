<?php
session_start();
include '../db.php';
$currentPage = 'analytics';

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

// Total Orders
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)");
$stmt->execute([$distributor_id]);
$total_orders = $stmt->fetchColumn();

// Delivered Orders
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?) AND status = 'Completed'");
$stmt->execute([$distributor_id]);
$delivered_orders = $stmt->fetchColumn();

// Pending Orders
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?) AND status = 'Pending'");
$stmt->execute([$distributor_id]);
$pending_orders = $stmt->fetchColumn();

// Monthly Revenue
$stmt = $conn->prepare("SELECT SUM(total_price) FROM orders WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?) AND MONTH(created_at) = MONTH(CURRENT_DATE())");
$stmt->execute([$distributor_id]);
$monthly_revenue = $stmt->fetchColumn() ?: 0;

// Top Product
$stmt = $conn->prepare(
  "SELECT ci.type, COUNT(*) AS cnt
   FROM orders o
   JOIN order_items oi ON o.order_id = oi.order_id
   JOIN container ci ON oi.container_id = ci.container_id
   WHERE o.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
   GROUP BY ci.type
   ORDER BY cnt DESC LIMIT 1"
);
$stmt->execute([$distributor_id]);
$top_product = $stmt->fetchColumn() ?: 'N/A';

// Peak Delivery Day
$stmt = $conn->prepare(
  "SELECT DAYNAME(created_at) AS day, COUNT(*) AS cnt
   FROM orders
   WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
   GROUP BY day
   ORDER BY cnt DESC LIMIT 1"
);
$stmt->execute([$distributor_id]);
$peak_day = $stmt->fetchColumn() ?: 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Total Orders</h2>
          <p class="text-2xl font-bold text-blue-600 mt-2"><?= $total_orders ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Delivered Orders</h2>
          <p class="text-2xl font-bold text-green-500 mt-2"><?= $delivered_orders ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Pending Orders</h2>
          <p class="text-2xl font-bold text-yellow-500 mt-2"><?= $pending_orders ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Monthly Revenue</h2>
          <p class="text-2xl font-bold text-indigo-600 mt-2">1<?= number_format($monthly_revenue, 2) ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Top Product</h2>
          <p class="text-lg font-semibold text-gray-700 mt-2"><?= htmlspecialchars($top_product) ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Peak Delivery Day</h2>
          <p class="text-lg font-semibold text-gray-700 mt-2"><?= htmlspecialchars($peak_day) ?></p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow mt-8 text-center text-gray-500">
        cca Charts and visualizations coming soon!
      </div>
    </main>
  </div>
</body>
</html>
