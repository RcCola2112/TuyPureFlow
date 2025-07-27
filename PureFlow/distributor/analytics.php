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
$stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?) AND MONTH(order_date) = MONTH(CURRENT_DATE())");
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
  "SELECT DAYNAME(order_date) AS day, COUNT(*) AS cnt
   FROM orders
   WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
   GROUP BY day
   ORDER BY cnt DESC LIMIT 1"
);
$stmt->execute([$distributor_id]);
$peak_day = $stmt->fetchColumn() ?: 'N/A';

// Monthly Sales Chart Data
$sales_stmt = $conn->prepare("
  SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_amount) AS total_sales
  FROM orders o
  JOIN shop s ON o.shop_id = s.shop_id
  WHERE s.distributor_id = ?
  GROUP BY month
  ORDER BY month ASC
");
$sales_stmt->execute([$distributor_id]);
$sales_data = $sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Shop Ratings Table
$rating_stmt = $conn->prepare("
  SELECT s.name, AVG(r.rating) AS avg_rating, COUNT(r.rating_id) AS num_ratings
  FROM shop s
  LEFT JOIN ratings r ON s.shop_id = r.shop_id
  WHERE s.distributor_id = ?
  GROUP BY s.shop_id
");
$rating_stmt->execute([$distributor_id]);
$rating_data = $rating_stmt->fetchAll(PDO::FETCH_ASSOC);

// Top 5 Products Sold Chart
$product_stmt = $conn->prepare("
  SELECT c.type, SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN container c ON oi.container_id = c.container_id
  JOIN shop s ON c.shop_id = s.shop_id
  WHERE s.distributor_id = ?
  GROUP BY c.type
  ORDER BY total_sold DESC
  LIMIT 5
");
$product_stmt->execute([$distributor_id]);
$product_data = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Revenue Growth (last month vs this month)
$revenue_stmt = $conn->prepare("
  SELECT
    SUM(CASE WHEN MONTH(order_date) = MONTH(CURRENT_DATE()) THEN total_amount ELSE 0 END) AS this_month,
    SUM(CASE WHEN MONTH(order_date) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) THEN total_amount ELSE 0 END) AS last_month
  FROM orders
  WHERE shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
");
$revenue_stmt->execute([$distributor_id]);
$revenue_growth = $revenue_stmt->fetch(PDO::FETCH_ASSOC);
$growth_percent = ($revenue_growth['last_month'] > 0)
    ? round((($revenue_growth['this_month'] - $revenue_growth['last_month']) / $revenue_growth['last_month']) * 100, 2)
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <p class="text-2xl font-bold text-indigo-600 mt-2">₱<?= number_format($monthly_revenue, 2) ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Top Product</h2>
          <p class="text-lg font-semibold text-gray-700 mt-2"><?= htmlspecialchars($top_product) ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Peak Delivery Day</h2>
          <p class="text-lg font-semibold text-gray-700 mt-2"><?= htmlspecialchars($peak_day) ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-sm font-medium text-gray-500">Revenue Growth</h2>
          <p class="text-lg font-semibold mt-2 <?= $growth_percent >= 0 ? 'text-green-600' : 'text-red-600' ?>">
            <?= $growth_percent ?>%
            <span class="text-xs text-gray-400">(vs last month)</span>
          </p>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Sales Chart -->
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-lg font-semibold mb-4">Monthly Sales</h2>
          <canvas id="salesChart" height="120"></canvas>
        </div>
        <!-- Top Products Chart -->
        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-lg font-semibold mb-4">Top 5 Products Sold</h2>
          <canvas id="productChart" height="120"></canvas>
        </div>
      </div>
      <!-- Shop Ratings Table -->
      <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">Shop Ratings</h2>
        <table class="min-w-full text-sm">
          <thead>
            <tr>
              <th class="py-2 px-4 border-b">Shop</th>
              <th class="py-2 px-4 border-b">Avg. Rating</th>
              <th class="py-2 px-4 border-b"># Ratings</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rating_data as $row): ?>
              <tr>
                <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['name']) ?></td>
                <td class="py-2 px-4 border-b"><?= $row['avg_rating'] ? number_format($row['avg_rating'], 2) : 'N/A' ?></td>
                <td class="py-2 px-4 border-b"><?= $row['num_ratings'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <script>
    // Monthly Sales Chart
    const salesLabels = <?= json_encode(array_column($sales_data, 'month')) ?>;
    const salesValues = <?= json_encode(array_map('floatval', array_column($sales_data, 'total_sales'))) ?>;
    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: {
        labels: salesLabels,
        datasets: [{
          label: 'Total Sales (₱)',
          data: salesValues,
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37,99,235,0.1)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });

    // Top Products Chart
    const productLabels = <?= json_encode(array_column($product_data, 'type')) ?>;
    const productValues = <?= json_encode(array_map('intval', array_column($product_data, 'total_sold'))) ?>;
    new Chart(document.getElementById('productChart'), {
      type: 'bar',
      data: {
        labels: productLabels,
        datasets: [{
          label: 'Units Sold',
          data: productValues,
          backgroundColor: '#22c55e'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });
  </script>
</body>
</html>
