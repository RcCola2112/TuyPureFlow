<?php
$currentPage = 'messages';
$username = 'Juan Dela Cruz';
$shopName = 'Dela Cruz Water Station';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 mt-16 p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Messages</h1>

    <div class="flex gap-6">
      
      <!-- Sidebar Filters -->
      <div class="w-1/5 space-y-2">
        <button class="w-full bg-blue-600 text-white py-2 rounded font-semibold">Compose</button>
        <ul class="space-y-1 text-sm text-gray-700">
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📨 All</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">✉️ Unread</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📋 Reports</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">🚫 Damaged Goods</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">🚚 Delivery Issues</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📦 Order Inquiries</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">💬 Feedback / Suggestions</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">⚠️ Flagged / Urgent</button></li>
          <li><button class="w-full text-left py-1 px-2 hover:bg-blue-100 rounded">📁 Archived</button></li>
        </ul>
      </div>

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
          <!-- Example Message Row -->
          <label class="block hover:bg-gray-50 cursor-pointer">
            <div class="flex items-center p-4 gap-3">
              <input type="checkbox" class="w-4 h-4">
              <div class="flex-1">
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-800">Maria Santos</span>
                  <span class="text-sm text-gray-400">2 min ago</span>
                </div>
                <p class="text-sm text-gray-600 mt-1 truncate">Hi! I'd like to confirm my order and ask about the delivery window...</p>
              </div>
            </div>
          </label>

          <label class="block hover:bg-gray-50 cursor-pointer">
            <div class="flex items-center p-4 gap-3">
              <input type="checkbox" class="w-4 h-4">
              <div class="flex-1">
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-800">Jose Rizal</span>
                  <span class="text-sm text-gray-400">10 min ago</span>
                </div>
                <p class="text-sm text-gray-600 mt-1 truncate">Can I change my delivery address from Zone 2 to Zone 5?</p>
              </div>
            </div>
          </label>

          <label class="block hover:bg-gray-50 cursor-pointer">
            <div class="flex items-center p-4 gap-3">
              <input type="checkbox" class="w-4 h-4">
              <div class="flex-1">
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-800">Andres Bonifacio</span>
                  <span class="text-sm text-gray-400">Yesterday</span>
                </div>
                <p class="text-sm text-gray-600 mt-1 truncate">Thanks for the fast delivery! Great service!</p>
              </div>
            </div>
          </label>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
