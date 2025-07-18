<?php
session_start();
include '../db.php';
$currentPage = 'dashboard';

// Get distributor info from session or database
$distributor_id = $_SESSION['distributor_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch();

$username = $distributor['name'] ?? "Juan Dela Cruz";
$shopname = $_SESSION['shop_name'] ?? "Tuy Aqua Station";
$profilePic = "images/profile.jpg"; // Placeholder

include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Distributor Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Dashboard Content -->
    <main class="p-6">
      <h1 class="text-2xl font-semibold text-gray-700 mb-6">Dashboard Overview</h1>

<!-- Top Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
          <div class="flex justify-between">
            <div>
              <h3 class="text-sm text-gray-500">Orders</h3>
              <p class="text-xl font-bold">1,456</p>
              <p class="text-green-500 text-sm">↑ 6.7% Since last week</p>
            </div>
            <span class="text-2xl">🛒</span>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
          <div class="flex justify-between">
            <div>
              <h3 class="text-sm text-gray-500">Loans</h3>
          <p class="text-xl font-bold">₱2,000</p>
              <p class="text-green-500 text-sm">↑ 3.1% Since last week</p>
            </div>
            <span class="text-2xl">👥</span>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
          <div class="flex justify-between">
            <div>
              <h3 class="text-sm text-gray-500">Estimated Sale</h3>
              <p class="text-xl font-bold">₱23,456</p>
              <p class="text-green-500 text-sm">↑ 1.1% Since last month</p>
            </div>
            <span class="text-2xl">📈</span>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
          <div class="flex justify-between">
            <div>
              <h3 class="text-sm text-gray-500">Revenue Today</h3>
              <p class="text-xl font-bold">₱838</p>
              <p class="text-green-500 text-sm">↑ 1.1% Since yesterday</p>
            </div>
            <span class="text-2xl">📊</span>
          </div>
          </div>
        </div>

        <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold mb-2">Delivery Breakdown</h3>
          <div class="w-full h-56 flex items-center justify-center bg-gray-100 text-gray-500">Pie Chart Placeholder</div>
            </div>
        <div class="bg-white p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold mb-2">Total Sales</h3>
          <div class="w-full h-56 flex items-center justify-center bg-gray-100 text-gray-500">Line Chart Placeholder</div>
          </div>
        </div>

        <!-- Bottom Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow text-center">
          <p class="text-sm text-gray-500">On-Going Delivery</p>
          <p class="text-xl font-bold">2</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
          <p class="text-sm text-gray-500">Available Rider</p>
          <p class="text-xl font-bold">0</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
          <p class="text-sm text-gray-500">Customer Complaint</p>
          <p class="text-xl font-bold">0</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
          <p class="text-sm text-gray-500">Damaged Containers</p>
          <p class="text-xl font-bold">4          </p>
        </div>
      </div>

      <!-- Container Count -->
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Remaining Containers</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="text-center border-r md:border-r border-gray-200">
            <p class="text-sm text-gray-500">Container 1</p>
            <p class="text-2xl font-bold">45</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-500">Container 2</p>
            <p class="text-2xl font-bold">30</p>
          </div>
        </div>
      </div>

    </main>

  </div>

</body>
</html>