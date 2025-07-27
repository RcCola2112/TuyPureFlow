<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
require_once '../db.php';

// Initial counts fallback
$adminCount = $conn->query('SELECT COUNT(*) FROM admin')->fetchColumn();
$orderCount = $conn->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM consumer_feedback')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Fix chart container heights */
    #revenueChart, #salesChart {
      max-height: 320px;
      width: 100% !important;
      height: 320px !important;
    }
    /* Slimmer doughnut ring */
    #salesChart {
      max-height: 280px !important;
      height: 280px !important;
    }
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-700 overflow-hidden">

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-md p-6 flex flex-col justify-between border-r border-gray-200">
    <div>
      <div class="text-blue-700 font-bold text-xl mb-6">PureFlow Admin</div>
      <nav class="space-y-3">
        <a href="dashboard.php" class="flex items-center gap-3 py-2 px-4 rounded-lg bg-blue-100 text-blue-700 font-semibold">🏠 Dashboard</a>
        <a href="users.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">👥 Manage Users</a>
        <a href="orders.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">📦 Manage Orders</a>
        <a href="shops.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">🏪 Manage Shops</a>
        <a href="messages.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">💬 Messages</a>
        <a href="analytics.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">📊 Analytics</a>
        <a href="feedback.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">⭐ Feedback</a>
        <a href="logs.php" class="block py-2 px-4 rounded-lg hover:bg-blue-50">📝 System Logs</a>
      </nav>
    </div>
    <div>
      <a href="logout.php" class="block py-2 px-4 rounded-lg text-red-600 hover:bg-red-50">🚪 Logout</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 overflow-auto">

    <!-- Header + Filter -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Welcome back, Admin!</h1>
        <p class="text-gray-500">Here’s your dashboard overview.</p>
      </div>
      <div class="flex items-center space-x-3">
        <select id="filterType" class="border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-blue-400">
          <option value="week">Per Week</option>
          <option value="month">Per Month</option>
          <option value="year">Per Year</option>
        </select>
        <button id="applyFilter" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Apply</button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-green-50 p-6 rounded-xl shadow hover:shadow-md transition">
        <p class="text-gray-500">Total Orders</p>
        <p class="text-3xl font-bold text-green-700" id="ordersCount"><?= htmlspecialchars($orderCount) ?></p>
        <p class="text-green-600 text-sm mt-2">+ Growth this period</p>
      </div>
      <div class="bg-yellow-50 p-6 rounded-xl shadow hover:shadow-md transition">
        <p class="text-gray-500">Total Shops</p>
        <p class="text-3xl font-bold text-yellow-600" id="shopsCount"><?= htmlspecialchars($shopCount) ?></p>
        <p class="text-yellow-600 text-sm mt-2">+ Active shops</p>
      </div>
      <div class="bg-blue-50 p-6 rounded-xl shadow hover:shadow-md transition">
        <p class="text-gray-500">Admins</p>
        <p class="text-3xl font-bold text-blue-700" id="adminsCount"><?= htmlspecialchars($adminCount) ?></p>
        <p class="text-blue-600 text-sm mt-2">+ Total admins</p>
      </div>
      <div class="bg-pink-50 p-6 rounded-xl shadow hover:shadow-md transition">
        <p class="text-gray-500">Feedback</p>
        <p class="text-3xl font-bold text-pink-600" id="feedbackCount"><?= htmlspecialchars($feedbackCount) ?></p>
        <p class="text-pink-600 text-sm mt-2">+ New feedback</p>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Line Chart -->
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-md border border-gray-100 flex flex-col">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Orders Trend</h3>
        <canvas id="revenueChart" class="flex-grow"></canvas>
      </div>

      <!-- Doughnut Chart -->
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-md border border-gray-100 flex flex-col items-center">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Sales by Category</h3>
        <canvas id="salesChart" style="max-width: 280px; max-height: 280px;"></canvas>
      </div>
    </div>

  </main>
</div>

<script>
  let revenueChart;
  let salesChart;

  function renderRevenueChart(labels, data) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();
    revenueChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Orders',
          data,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  function renderSalesChart(admins, shops, feedback, orders) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    if (salesChart) salesChart.destroy();
    salesChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Admins', 'Shops', 'Feedback', 'Orders'],
        datasets: [{
          data: [admins, shops, feedback, orders],
          backgroundColor: ['#3b82f6', '#10b981', '#ec4899', '#f59e0b'],
          hoverBackgroundColor: ['#2563eb', '#059669', '#db2777', '#d97706']
        }]
      },
      options: {
        responsive: true,
        cutout: '80%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#374151', font: { size: 14, weight: 'bold' } }
          }
        }
      }
    });
  }

  function fetchDashboardData(filter = 'week') {
    fetch('get_dashboard_data.php?filter=' + filter)
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          console.error('Error:', data.error);
          return;
        }

        // Update counts
        document.getElementById('ordersCount').textContent = data.orderCount;
        document.getElementById('shopsCount').textContent = data.shopCount;
        document.getElementById('adminsCount').textContent = data.adminCount;
        document.getElementById('feedbackCount').textContent = data.feedbackCount;

        // Update charts
        renderRevenueChart(data.labels, data.chartData);
        renderSalesChart(data.adminCount, data.shopCount, data.feedbackCount, data.orderCount);
      })
      .catch(err => console.error('Fetch error:', err));
  }

  document.getElementById('applyFilter').addEventListener('click', () => {
    const filter = document.getElementById('filterType').value;
    fetchDashboardData(filter);
  });

  window.addEventListener('DOMContentLoaded', () => fetchDashboardData('week'));
</script>

</body>
</html>
