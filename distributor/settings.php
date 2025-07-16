<?php
$currentPage = 'settings';
$username = 'Juan Dela Cruz';
$shopName = 'Dela Cruz Water Station';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>

  <!-- Main Content -->
  <div class="ml-64 mt-16 p-8 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Settings</h1>

    <div class="bg-white p-6 rounded-lg shadow space-y-6">
      
      <!-- Account Info -->
      <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Account Information</h2>
        <form class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600">Full Name</label>
            <input type="text" value="Juan Dela Cruz" class="w-full mt-1 p-2 border rounded">
          </div>
          <div>
            <label class="block text-sm text-gray-600">Email Address</label>
            <input type="email" value="juan@example.com" class="w-full mt-1 p-2 border rounded">
          </div>
          <div>
            <label class="block text-sm text-gray-600">Phone Number</label>
            <input type="text" value="09171234567" class="w-full mt-1 p-2 border rounded">
          </div>
        </form>
      </div>

      <!-- Password Change -->
      <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Change Password</h2>
        <form class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600">Current Password</label>
            <input type="password" class="w-full mt-1 p-2 border rounded">
          </div>
          <div>
            <label class="block text-sm text-gray-600">New Password</label>
            <input type="password" class="w-full mt-1 p-2 border rounded">
          </div>
          <div>
            <label class="block text-sm text-gray-600">Confirm New Password</label>
            <input type="password" class="w-full mt-1 p-2 border rounded">
          </div>
        </form>
      </div>

      <div class="text-right">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save Changes</button>
      </div>

    </div>
  </div>

</body>
</html>