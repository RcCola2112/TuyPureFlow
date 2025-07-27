<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// Expanded analytics for admin
$distributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Approved'")->fetchColumn();
$pendingDistributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Pending'")->fetchColumn();
$suspendedDistributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Suspended'")->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$consumerCount = $conn->query('SELECT COUNT(*) FROM consumer')->fetchColumn();
$suspendedConsumerCount = $conn->query('SELECT COUNT(*) FROM consumer WHERE suspended = 1')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM feedback')->fetchColumn();
?>
<head>
  <meta charset="UTF-8">
  <title>View Analytics</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">View Analytics</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Analytics</h2>
      <!-- Analytics charts go here -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-blue-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($distributorCount) ?></span>
          <span class="text-gray-700">Active Distributors</span>
        </div>
        <div class="bg-yellow-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-yellow-700 mb-2"><?= htmlspecialchars($pendingDistributorCount) ?></span>
          <span class="text-gray-700">Pending Distributors</span>
        </div>
        <div class="bg-red-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-red-700 mb-2"><?= htmlspecialchars($suspendedDistributorCount) ?></span>
          <span class="text-gray-700">Suspended Distributors</span>
        </div>
        <div class="bg-blue-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($shopCount) ?></span>
          <span class="text-gray-700">Total Shops</span>
        </div>
        <div class="bg-green-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-green-700 mb-2"><?= htmlspecialchars($consumerCount) ?></span>
          <span class="text-gray-700">Total Consumers</span>
        </div>
        <div class="bg-red-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-red-700 mb-2"><?= htmlspecialchars($suspendedConsumerCount) ?></span>
          <span class="text-gray-700">Suspended Consumers</span>
        </div>
        <div class="bg-purple-50 rounded p-6 text-center">
          <span class="block text-3xl font-bold text-purple-700 mb-2"><?= htmlspecialchars($feedbackCount) ?></span>
          <span class="text-gray-700">Feedback Entries</span>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
</html>
