<?php
$currentPage = 'orders';
$username = "Juan Dela Cruz";
$shopname = "Tuy Aqua Station";
$profilePic = "images/profile.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Distributor Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const checkboxes = document.querySelectorAll('.order-checkbox');
      const bulkPanel = document.getElementById('bulk-panel');
      const detailCard = document.getElementById('detail-card');

      checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
          const selected = Array.from(checkboxes).filter(c => c.checked);

          if (selected.length > 1) {
            bulkPanel.classList.remove('hidden');
            detailCard.classList.add('hidden');
          } else if (selected.length === 1) {
            bulkPanel.classList.add('hidden');
            detailCard.classList.remove('hidden');
            // Optionally populate detailCard with data here
          } else {
            bulkPanel.classList.add('hidden');
            detailCard.classList.add('hidden');
          }
        });
      });
    });
  </script>
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

      <table class="min-w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-blue-100 text-blue-800">
          <tr>
            <th class="px-4 py-2"><input type="checkbox"></th>
            <th class="px-4 py-2 text-left">Order ID</th>
            <th class="px-4 py-2 text-left">Customer Name</th>
            <th class="px-4 py-2 text-left">Phone Number</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left">Address</th>
            <th class="px-4 py-2 text-left">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2"><input type="checkbox" class="order-checkbox"></td>
            <td class="px-4 py-2">ORD001</td>
            <td class="px-4 py-2">Maria Santos</td>
            <td class="px-4 py-2">09171234567</td>
            <td class="px-4 py-2">maria@example.com</td>
            <td class="px-4 py-2">Brgy. 1, Tuy, Batangas</td>
            <td class="px-4 py-2"><span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pending</span></td>
          </tr>
          <!-- More rows can go here -->
        </tbody>
      </table>

      <!-- Bulk Action Panel -->
      <div id="bulk-panel" class="mt-4 hidden bg-white p-4 rounded shadow border-t">
        <h2 class="text-lg font-semibold mb-2">Bulk Actions</h2>
        <div class="flex items-center gap-4">
          <select class="border rounded p-2">
            <option value="">Change Status</option>
            <option value="approved">Approve</option>
            <option value="cancelled">Cancel</option>
            <option value="delivered">Mark as Delivered</option>
          </select>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Apply Changes</button>
          <button class="text-red-500">Deselect All</button>
        </div>
      </div>

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
