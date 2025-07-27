<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';
$adminCount = $conn->query('SELECT COUNT(*) FROM admin')->fetchColumn();
$orderCount = $conn->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM consumer_feedback')->fetchColumn();
$logCount = 0; // No logs table exists in the database
?>
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="h-8 mr-3">
      <h1 class="text-xl font-bold text-blue-700">Admin Dashboard</h1>
    </div>
    <a href="logout.php" class="text-blue-600 font-semibold">Logout</a>
  </header>
  <main class="p-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <a href="users.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">Manage Users <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded"><?= htmlspecialchars($adminCount) ?></span></a>
      <a href="orders.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">Manage Orders <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded"><?= htmlspecialchars($orderCount) ?></span></a>
      <a href="shops.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">Manage Shops <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded"><?= htmlspecialchars($shopCount) ?></span></a>
      <a href="messages.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">Messages <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">💬</span></a>
      <a href="analytics.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">View Analytics</a>
      <a href="feedback.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">View Feedback <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded"><?= htmlspecialchars($feedbackCount) ?></span></a>
      <a href="logs.php" class="block bg-white p-8 rounded-xl shadow hover:bg-blue-50 transition text-center font-semibold text-blue-700">System Logs <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded"><?= htmlspecialchars($logCount) ?></span></a>
    </div>
  </main>
</body>
</html>
