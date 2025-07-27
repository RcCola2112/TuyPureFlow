<?php
// Admin: System Settings Management
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../db.php';
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $open_time = $_POST['open_time'] ?? '';
    $close_time = $_POST['close_time'] ?? '';
    $delivery_zones = $_POST['delivery_zones'] ?? '';
    $delivery_fee = $_POST['delivery_fee'] ?? '';
    $terms = $_POST['terms'] ?? '';
    $privacy = $_POST['privacy'] ?? '';
    $stmt = $conn->prepare("UPDATE settings SET open_time=?, close_time=?, delivery_zones=?, delivery_fee=?, terms=?, privacy=? WHERE id=1");
    $stmt->execute([$open_time, $close_time, $delivery_zones, $delivery_fee, $terms, $privacy]);
    echo '<script>location.reload();</script>';
}
// Fetch settings
$set_stmt = $conn->query("SELECT * FROM settings WHERE id=1");
$settings = $set_stmt->fetch();
?>
<head>
  <meta charset="UTF-8">
  <title>System Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">System Settings</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Update System Settings</h2>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block font-semibold mb-1">Operational Hours</label>
          <input type="time" name="open_time" value="<?= htmlspecialchars($settings['open_time'] ?? '') ?>" class="border rounded px-4 py-2 mr-2">
          <input type="time" name="close_time" value="<?= htmlspecialchars($settings['close_time'] ?? '') ?>" class="border rounded px-4 py-2">
        </div>
        <div>
          <label class="block font-semibold mb-1">Delivery Zones</label>
          <input type="text" name="delivery_zones" value="<?= htmlspecialchars($settings['delivery_zones'] ?? '') ?>" class="border rounded px-4 py-2 w-full">
        </div>
        <div>
          <label class="block font-semibold mb-1">Delivery Fee</label>
          <input type="number" step="0.01" name="delivery_fee" value="<?= htmlspecialchars($settings['delivery_fee'] ?? '') ?>" class="border rounded px-4 py-2 w-full">
        </div>
        <div>
          <label class="block font-semibold mb-1">Terms & Conditions</label>
          <textarea name="terms" class="border rounded px-4 py-2 w-full" rows="3"><?= htmlspecialchars($settings['terms'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="block font-semibold mb-1">Privacy Policy</label>
          <textarea name="privacy" class="border rounded px-4 py-2 w-full" rows="3"><?= htmlspecialchars($settings['privacy'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="admin-btn bg-blue-600 hover:bg-blue-700">Save Settings</button>
      </form>
    </div>
  </main>
</body>
</html>
