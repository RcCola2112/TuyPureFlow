<?php
$currentPage = 'customers';
$username = 'Juan Dela Cruz';
$shopName = 'Dela Cruz Water Station';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 mt-16 p-8">
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
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">Maria Santos</td>
            <td class="px-6 py-4 whitespace-nowrap">09171234567</td>
            <td class="px-6 py-4 whitespace-nowrap">8</td>
            <td class="px-6 py-4 whitespace-nowrap">June 5, 2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">Jose Rizal</td>
            <td class="px-6 py-4 whitespace-nowrap">09181234567</td>
            <td class="px-6 py-4 whitespace-nowrap">10</td>
            <td class="px-6 py-4 whitespace-nowrap">June 3, 2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">Andres Bonifacio</td>
            <td class="px-6 py-4 whitespace-nowrap">09201234567</td>
            <td class="px-6 py-4 whitespace-nowrap">6</td>
            <td class="px-6 py-4 whitespace-nowrap">June 2, 2025</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
