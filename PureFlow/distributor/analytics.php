<?php
$currentPage = 'analytics';
$username = 'Juan Dela Cruz';
$shopName = 'Dela Cruz Water Station';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 mt-16 p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
      <!-- Total Orders -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Total Orders</h2>
        <p class="text-2xl font-bold text-blue-600 mt-2">182</p>
      </div>

      <!-- Delivered Orders -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Delivered Orders</h2>
        <p class="text-2xl font-bold text-green-500 mt-2">163</p>
      </div>

      <!-- Pending Orders -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Pending Orders</h2>
        <p class="text-2xl font-bold text-yellow-500 mt-2">19</p>
      </div>

      <!-- Monthly Revenue -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Monthly Revenue</h2>
        <p class="text-2xl font-bold text-indigo-600 mt-2">₱52,300</p>
      </div>

      <!-- Top Product -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Top Product</h2>
        <p class="text-lg font-semibold text-gray-700 mt-2">5-Gallon Container</p>
      </div>

      <!-- Peak Delivery Day -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-sm font-medium text-gray-500">Peak Delivery Day</h2>
        <p class="text-lg font-semibold text-gray-700 mt-2">Wednesday</p>
      </div>
    </div>

    <!-- Placeholder for future charts -->
    <div class="bg-white p-6 rounded-lg shadow mt-8 text-center text-gray-500">
      📊 Charts and visualizations coming soon!
    </div>

  </div>

</body>
</html>
