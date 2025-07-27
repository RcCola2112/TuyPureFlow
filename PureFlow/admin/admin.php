<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// For demo, assume admin is logged in
$_SESSION['admin_name'] = $_SESSION['admin_name'] ?? 'Admin';

// Connect to DB
include '../includes/db.php';

// Admin actions: manage users, shops, orders, inventory, analytics, feedback
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <span class="text-xl font-bold text-blue-600">Tuy PureFlow Admin</span>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Hello, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        <a href="logout.php" class="text-sm text-gray-600 hover:text-blue-600">Logout</a>
      </div>
    </div>
  </header>

  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">User Management</h2>
        <ul class="list-disc ml-6 text-sm">
          <li><a href="manage_consumers.php" class="text-blue-600 hover:underline">Manage Consumers</a></li>
          <li><a href="manage_distributors.php" class="text-blue-600 hover:underline">Manage Distributors</a></li>
          <li><a href="manage_employees.php" class="text-blue-600 hover:underline">Manage Employees</a></li>
        </ul>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Shop & Inventory</h2>
        <ul class="list-disc ml-6 text-sm">
          <li><a href="manage_shops.php" class="text-blue-600 hover:underline">Manage Shops</a></li>
          <li><a href="manage_inventory.php" class="text-blue-600 hover:underline">Manage Inventory</a></li>
        </ul>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Orders & Delivery</h2>
        <ul class="list-disc ml-6 text-sm">
          <li><a href="manage_orders.php" class="text-blue-600 hover:underline">View Orders</a></li>
          <li><a href="manage_deliveries.php" class="text-blue-600 hover:underline">Manage Deliveries</a></li>
        </ul>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Analytics & Feedback</h2>
        <ul class="list-disc ml-6 text-sm">
          <li><a href="view_analytics.php" class="text-blue-600 hover:underline">View Analytics</a></li>
          <li><a href="view_feedback.php" class="text-blue-600 hover:underline">View Feedback</a></li>
        </ul>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Notifications & Logs</h2>
        <ul class="list-disc ml-6 text-sm">
          <li><a href="view_notifications.php" class="text-blue-600 hover:underline">View Notifications</a></li>
          <li><a href="view_logs.php" class="text-blue-600 hover:underline">View System Logs</a></li>
        </ul>
      </div>
    </div>
  </main>
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
