<?php
session_start();
include '../db.php';
$currentPage = 'messages';

// Get distributor info from session or database
$distributor_id = $_SESSION['distributor_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch();

$username = $distributor['name'] ?? '';
$stmtShop = $conn->prepare("SELECT name FROM shop WHERE distributor_id = ? LIMIT 1");
$stmtShop->execute([$distributor_id]);
$shopname = $stmtShop->fetchColumn() ?: '';
$profilePic = isset($distributor['profile_pic']) && $distributor['profile_pic'] ? $distributor['profile_pic'] : "images/profile.jpg";

// Fetch messages for this distributor
$stmt = $conn->prepare("SELECT * FROM messages WHERE receiver_id = ? AND receiver_role = 'Distributor' ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$distributor_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main class="p-6">
      <h1 class="text-2xl font-semibold text-gray-700 mb-6">Messages</h1>

      <div class="flex gap-6">
        <!-- Sidebar Filters -->
        <div class="w-1/5 space-y-2">
        <button class="w-full bg-blue-600 text-white py-2 rounded font-semibold">Compose</button>
        <ul class="space-y-1 text-sm text-gray-700">
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📨 All</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">✉️ Unread</button></li>
<body class="flex bg-gray-100">
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">🚫 Damaged Goods</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">🚚 Delivery Issues</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📦 Order Inquiries</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">💬 Feedback / Suggestions</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">⚠️ Flagged / Urgent</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📁 Archived</button></li>
        </ul>
      </div>
  <div class="ml-64 flex flex-col flex-1">
    <main class="p-6">
      <!-- Message Content Area -->
      <div class="w-4/5">
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <input type="checkbox" id="select-all" class="w-4 h-4">
            <button class="text-sm text-gray-600 hover:text-blue-600">Delete</button>
            <button class="text-sm text-gray-600 hover:text-blue-600">Mark as Read</button>
            <button class="text-sm text-gray-600 hover:text-blue-600">Mark as Unread</button>
          </div>
          <input type="text" placeholder="Search messages..." class="border rounded px-3 py-1 w-1/3">
        </div>

        <!-- Message List -->
        <div class="bg-white shadow rounded-lg overflow-hidden divide-y">
          <?php foreach ($messages as $msg): ?>
          <label class="block hover:bg-gray-50 cursor-pointer">
            <div class="flex items-center p-4 gap-3">
              <input type="checkbox" class="w-4 h-4">
              <div class="flex-1">
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-800"><?= htmlspecialchars($msg['sender_role']) ?> #<?= htmlspecialchars($msg['sender_id']) ?></span>
                  <span class="text-sm text-gray-400"><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></span>
                </div>
                <p class="text-sm text-gray-600 mt-1 truncate"><?= htmlspecialchars($msg['message']) ?></p>
              </div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
</body>
</html>
