<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
require_once '../db.php';
$adminCount = $conn->query('SELECT COUNT(*) FROM admin')->fetchColumn();
$orderCount = $conn->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM consumer_feedback')->fetchColumn();
$logCount = 0; // You can replace this when logs table is ready
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 via-white to-blue-200 min-h-screen font-sans">

  <!-- Header -->
  <header class="bg-white shadow-md p-6 flex justify-between items-center sticky top-0 z-10">
    <div class="flex items-center gap-4">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="h-10">
      <h1 class="text-2xl font-bold text-blue-800">Admin Dashboard</h1>
    </div>
    <a href="logout.php" class="bg-red-100 text-red-600 font-medium px-4 py-2 rounded hover:bg-red-200 transition">Logout</a>
  </header>

  <!-- Main Content -->
  <main class="p-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Card Template -->
      <a href="users.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-blue-500">
        <div class="text-blue-600 text-xl font-semibold">Manage Users</div>
        <div class="mt-2 text-gray-600">Admins registered: <span class="font-bold"><?= htmlspecialchars($adminCount) ?></span></div>
      </a>

      <a href="orders.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-green-500">
        <div class="text-green-600 text-xl font-semibold">Manage Orders</div>
        <div class="mt-2 text-gray-600">Total orders: <span class="font-bold"><?= htmlspecialchars($orderCount) ?></span></div>
      </a>

      <a href="shops.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-purple-500">
        <div class="text-purple-600 text-xl font-semibold">Manage Shops</div>
        <div class="mt-2 text-gray-600">Total shops: <span class="font-bold"><?= htmlspecialchars($shopCount) ?></span></div>
      </a>

      <a href="messages.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-yellow-500">
        <div class="text-yellow-600 text-xl font-semibold">Messages</div>
        <div class="mt-2 text-gray-600">Check for new messages 💬</div>
      </a>

      <a href="analytics.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-pink-500">
        <div class="text-pink-600 text-xl font-semibold">View Analytics</div>
        <div class="mt-2 text-gray-600">Monitor system trends & data</div>
      </a>

      <a href="feedback.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-indigo-500">
        <div class="text-indigo-600 text-xl font-semibold">View Feedback</div>
        <div class="mt-2 text-gray-600">Feedback entries: <span class="font-bold"><?= htmlspecialchars($feedbackCount) ?></span></div>
      </a>

      <a href="logs.php" class="group bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 border-l-4 border-gray-500">
        <div class="text-gray-800 text-xl font-semibold">System Logs</div>
        <div class="mt-2 text-gray-600">Logs available: <span class="font-bold"><?= htmlspecialchars($logCount) ?></span></div>
      </a>
    </div>
  </main>

</body>
</html>
