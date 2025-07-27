<?php
session_start();
include '../db.php';
$currentPage = 'customers';

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

// Fetch customers who ordered from this distributor's shop(s)
$stmt = $conn->prepare(
  "SELECT c.name, c.phone, c.email,
    COUNT(o.order_id) AS total_orders,
    MAX(o.created_at) AS last_order
   FROM consumer c
   JOIN orders o ON c.consumer_id = o.consumer_id
   WHERE o.shop_id IN (SELECT shop_id FROM shop WHERE distributor_id = ?)
   GROUP BY c.consumer_id
   ORDER BY last_order DESC"
);
$stmt->execute([$distributor_id]);
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers</title>
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
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Customers</h1>
      <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($customers as $c): ?>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($c['name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($c['phone']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($c['total_orders']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y', strtotime($c['last_order'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    </main>
  </div>
</body>
</html>
