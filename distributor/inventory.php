<?php
$currentPage = 'inventory';
$username = 'Juan Dela Cruz';
$shopName = 'Dela Cruz Water Station';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CDN -->
</head>
<body class="bg-gray-50">

  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 mt-16 p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Inventory</h1>

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
        <tbody>
          <!-- Example Row -->
          <tr class="hover:bg-gray-50 border-t cursor-pointer" onclick="showDetail('5 Gallon Containers', 120, 15, 0, 2, 20)">
            <td class="px-4 py-2">5 Gallon Containers</td>
            <td class="px-4 py-2">120</td>
            <td class="px-4 py-2">15</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">2</td>
            <td class="px-4 py-2">20</td>
            <td class="px-4 py-2 text-blue-600">Edit</td>
          </tr>
          <tr class="hover:bg-gray-50 border-t cursor-pointer" onclick="showDetail('Refillable Seals', 300, 0, 0, 0, 100)">
            <td class="px-4 py-2">Refillable Seals</td>
            <td class="px-4 py-2">300</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">100</td>
            <td class="px-4 py-2 text-blue-600">Edit</td>
          </tr>
          <tr class="hover:bg-gray-50 border-t cursor-pointer" onclick="showDetail('Delivery Bottles', 75, 0, 10, 0, 30)">
            <td class="px-4 py-2">Delivery Bottles</td>
            <td class="px-4 py-2">75</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">10</td>
            <td class="px-4 py-2">0</td>
            <td class="px-4 py-2">30</td>
            <td class="px-4 py-2 text-blue-600">Edit</td>
          </tr>
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
