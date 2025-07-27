<?php
// notification.php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['consumer_id'];

// Fetch notifications
// Replace 'consumer' with the actual column name in your notifications table
$stmt = $conn->prepare("SELECT * FROM notifications WHERE recipient_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <!-- Header -->
  <header class="bg-white shadow sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <!-- Optionally show logged in user -->
    </div>
  </header>
  <main class="container mx-auto px-4 py-8">
    <div class="grid md:grid-cols-4 gap-6">
      <!-- Sidebar -->
      <aside class="bg-white p-4 rounded-lg shadow md:col-span-1">
        <nav class="space-y-4">
          <a href="account.php" class="block hover:text-blue-600">Account Info</a>
          <a href="my_purchases.php" class="block hover:text-blue-600">My Purchases</a>
          <a href="notification.php" class="block text-blue-600 font-medium">Notifications</a>
          <a href="logout.php" class="block text-red-500 hover:underline">Logout</a>
        </nav>
      </aside>
      <!-- Content -->
      <section class="md:col-span-3">
        <h1 class="text-2xl font-bold mb-6">Notifications</h1>
        <div class="bg-white p-6 rounded shadow">
          <?php if ($notifications): ?>
            <ul class="space-y-4">
              <?php foreach ($notifications as $note): ?>
              <li class="border-b pb-2">
                <p class="text-sm text-gray-800"><?= htmlspecialchars($note['message']) ?></p>
                <p class="text-xs text-gray-500"><?= $note['created_at'] ?></p>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>No notifications available.</p>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
