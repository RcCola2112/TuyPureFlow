<?php
session_start();
require '../db.php';

if (!isset($_SESSION['distributor_id'])) {
    echo '<script>window.location.replace("login.php");</script>';
    exit;
}

// Fetch distributor and shop info
$distributor_id = $_SESSION['distributor_id'];
$stmt = $conn->prepare("SELECT name FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['distributor_name'] = $distributor['name'] ?? '';

$shopStmt = $conn->prepare("SELECT name FROM shop WHERE distributor_id = ?");
$shopStmt->execute([$distributor_id]);
$shop = $shopStmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['shop_name'] = $shop ? $shop['name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Distributor Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex">
  <?php include 'sidebar.php'; ?>
  <div class="flex-1 ml-64 min-h-screen flex flex-col">
    <!-- HEADER: Place OUTSIDE the main content, full width -->
    <header class="bg-white shadow px-8 py-4 flex justify-between items-center w-full">
      <div class="flex items-center gap-2">
        <span class="text-2xl font-bold text-blue-700">Tuy PureFlow</span>
        <span class="ml-4 text-gray-700">
          Hello, 
          <span class="text-blue-700 font-semibold">
            <?= htmlspecialchars($_SESSION['distributor_name']) ?>
            <?php if (!empty($_SESSION['shop_name'])): ?>
              of <?= htmlspecialchars($_SESSION['shop_name']) ?>
            <?php endif; ?>
          </span>
        </span>
      </div>
      <div class="flex items-center gap-4">
        <div class="relative">
          <span class="material-icons text-blue-700" style="font-size: 28px;">notifications</span>
          <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-2">3</span>
        </div>
        <img src="../assets/profile.png" alt="Profile" class="w-8 h-8 rounded-full border object-cover">
        <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['distributor_name']) ?></span>
        <a href="logout.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Logout</a>
      </div>
    </header>
    <!-- MAIN CONTENT -->
    <main class="p-8">
      <h2 class="text-2xl font-bold mb-6 text-blue-700">Order Management</h2>
      <div class="bg-white rounded shadow p-6">
        <table class="min-w-full border rounded mb-4">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 border-b">Order ID</th>
              <th class="py-2 px-4 border-b">Customer Name</th>
              <th class="py-2 px-4 border-b">Phone Number</th>
              <th class="py-2 px-4 border-b">Email</th>
              <th class="py-2 px-4 border-b">Address</th>
              <th class="py-2 px-4 border-b">Status</th>
              <th class="py-2 px-4 border-b">Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Table rows go here -->
          </tbody>
        </table>
        <div class="mb-4">
          <h3 class="font-semibold mb-2">Bulk Actions</h3>
          <div class="flex gap-2">
            <select class="border rounded px-2 py-1">
              <option>Change Status</option>
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Apply Changes</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>