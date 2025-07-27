<?php
// Admin: Security & Fraud Monitoring
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../db.php';
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// Example: Fetch suspicious logins (requires logs table with action column)
$logs_stmt = $conn->query("SELECT id, user_id, action, date FROM logs WHERE action LIKE '%suspicious%' OR action LIKE '%failed%' ORDER BY date DESC LIMIT 50");
$suspicious_logs = $logs_stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>Security & Fraud Monitoring</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">Security & Fraud Monitoring</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-red-700">Suspicious Activity Logs</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-red-100">
            <tr>
              <th class="py-2 px-4 border-b">Log ID</th>
              <th class="py-2 px-4 border-b">User ID</th>
              <th class="py-2 px-4 border-b">Action</th>
              <th class="py-2 px-4 border-b">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($suspicious_logs as $log): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['user_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['action']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($log['date']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
