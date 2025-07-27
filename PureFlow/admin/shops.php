
<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
$stmt = $conn->query('SELECT shop_id, name, distributor_id, location, contact_number, average_rating, open_time, close_time FROM shop');
$shops = $stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>Manage Shops</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">Manage Shops</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Shop Management</h2>
      <!-- Shop management table goes here -->
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 border-b">Shop ID</th>
              <th class="py-2 px-4 border-b">Name</th>
              <th class="py-2 px-4 border-b">Distributor ID</th>
              <th class="py-2 px-4 border-b">Location</th>
              <th class="py-2 px-4 border-b">Contact</th>
              <th class="py-2 px-4 border-b">Rating</th>
              <th class="py-2 px-4 border-b">Open</th>
              <th class="py-2 px-4 border-b">Close</th>
              <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($shops as $shop): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['shop_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['name']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['distributor_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['location']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['contact_number']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['average_rating']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['open_time']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($shop['close_time']) ?></td>
              <td class="py-2 px-4 border-b">
                <button class="admin-btn">Edit</button>
                <button class="admin-btn bg-red-600 hover:bg-red-700 ml-2">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
