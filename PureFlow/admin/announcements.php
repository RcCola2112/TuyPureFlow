<?php
// Admin: Announcements and Notifications Management
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../db.php';
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// Handle new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement'])) {
    $announcement = trim($_POST['announcement']);
    if ($announcement) {
        $stmt = $conn->prepare("INSERT INTO announcements (message, created_at) VALUES (?, NOW())");
        $stmt->execute([$announcement]);
        echo '<script>location.reload();</script>';
    }
}
// Fetch announcements
$ann_stmt = $conn->query("SELECT id, message, created_at FROM announcements ORDER BY created_at DESC");
$announcements = $ann_stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>Announcements & Notifications</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">Announcements & Notifications</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Post Announcement</h2>
      <form method="POST" class="mb-6 flex gap-4">
        <input type="text" name="announcement" placeholder="Type announcement..." class="border rounded px-4 py-2 flex-1" required>
        <button type="submit" class="admin-btn bg-blue-600 hover:bg-blue-700">Post</button>
      </form>
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Recent Announcements</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 border-b">ID</th>
              <th class="py-2 px-4 border-b">Message</th>
              <th class="py-2 px-4 border-b">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($announcements as $ann): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($ann['id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($ann['message']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($ann['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
