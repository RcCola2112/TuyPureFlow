<?php
// Admin: Backup Utility & Instructions
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
?>
<head>
  <meta charset="UTF-8">
  <title>System Backup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">System Backup</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Backup Instructions</h2>
      <ul class="list-disc pl-6 mb-6 text-gray-700">
        <li>Use phpMyAdmin or command line to export your database regularly.</li>
        <li>Backup uploaded files and images from the <code>PureFlow/images/</code> and <code>assets/</code> folders.</li>
        <li>Store backups in a secure, offsite location.</li>
        <li>Schedule automated backups if possible.</li>
      </ul>
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Quick Database Export (phpMyAdmin)</h2>
      <ol class="list-decimal pl-6 mb-6 text-gray-700">
        <li>Login to phpMyAdmin.</li>
        <li>Select your database.</li>
        <li>Click <strong>Export</strong> and choose <strong>SQL</strong> format.</li>
        <li>Download and save the file securely.</li>
      </ol>
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Quick Database Export (Command Line)</h2>
      <pre class="bg-gray-100 p-4 rounded mb-6">mysqldump -u [username] -p [database_name] > backup.sql</pre>
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Restore Database</h2>
      <pre class="bg-gray-100 p-4 rounded mb-6">mysql -u [username] -p [database_name] < backup.sql</pre>
    </div>
  </main>
</body>
</html>
