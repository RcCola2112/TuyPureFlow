<?php
// my_purchases.php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['consumer_id'];

// Filter by status (tab)
$status_filter = $_GET['status'] ?? 'All';
$status_options = ['All', 'Pending', 'Processing', 'Out for Delivery', 'Completed', 'Cancelled'];

$query = "SELECT * FROM orders WHERE consumer_id = ?";
$params = [$user_id];
if ($status_filter !== 'All') {
    // "Pending" tab: show orders with status Pending
    if ($status_filter === 'Pending') {
        $query .= " AND status = 'Pending'";
    }
    // "Processing" tab: show orders with status Processing or Approved
    elseif ($status_filter === 'Processing') {
        $query .= " AND (status = 'Approved')";
    }
    // "Out for Delivery" tab: show orders with status Out for Delivery
    elseif ($status_filter === 'Out for Delivery') {
        $query .= " AND status = 'Out for Delivery'";
    }
    else {
        $query .= " AND status = ?";
        $params[] = $status_filter;
    }
}
$query .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$purchases = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Purchases - Tuy PureFlow</title>
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
          <a href="my_purchases.php" class="block text-blue-600 font-medium">My Purchases</a>
          <a href="notification.php" class="block hover:text-blue-600">Notifications</a>
          <a href="logout.php" class="block text-red-500 hover:underline">Logout</a>
        </nav>
      </aside>
      <!-- Content -->
      <section class="md:col-span-3">
        <h1 class="text-2xl font-bold mb-6">My Purchases</h1>
        <!-- Filter Tabs -->
        <div class="mb-4 flex gap-2">
          <?php foreach ($status_options as $status): ?>
            <a href="?status=<?= urlencode($status) ?>"
               class="px-4 py-2 rounded-full text-sm font-medium
               <?= $status_filter === $status ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?>">
              <?= $status ?>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <?php if ($purchases): ?>
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b">
                  <th class="text-left py-2">Order ID</th>
                  <th class="text-left py-2">Total</th>
                  <th class="text-left py-2">Status</th>
                  <th class="text-left py-2">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($purchases as $order): ?>
                <tr class="border-b">
                  <td class="py-2">#<?= $order['order_id'] ?></td>
                  <td class="py-2">₱<?= number_format($order['total_price'], 2) ?></td>
                  <td class="py-2">
                    <?php
                      // Show "Processing" for status Approved
                      if ($order['status'] === 'Approved') {
                        echo 'Processing';
                      } else {
                        echo htmlspecialchars($order['status']);
                      }
                    ?>
                  </td>
                  <td class="py-2"><?= $order['created_at'] ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No purchases found.</p>
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