
<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
$stmt = $conn->query('SELECT order_id, consumer_id, order_date, status FROM orders');
$orders = $stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>Manage Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">Manage Orders</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Order Management</h2>
      <!-- Order management table goes here -->
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-blue-100">
            <tr>
            <th class="py-2 px-4 border-b">Order ID</th>
            <th class="py-2 px-4 border-b">Consumer ID</th>
            <th class="py-2 px-4 border-b">Date</th>
            <th class="py-2 px-4 border-b">Status</th>
            <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['order_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['consumer_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['order_date']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['status']) ?></td>
              <td class="py-2 px-4 border-b">
                <button class="admin-btn">View</button>
                <button class="admin-btn bg-green-600 hover:bg-green-700 ml-2">Complete</button>
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
</html>
