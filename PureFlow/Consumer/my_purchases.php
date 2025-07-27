<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// my_purchases.php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

$consumer_id = $_SESSION['consumer_id'];

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['shop_id'], $_POST['rating'])) {
    $order_id = $_POST['order_id'];
    $shop_id = $_POST['shop_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'] ?? '';
    $stmt = $conn->prepare("INSERT INTO ratings (consumer_id, shop_id, order_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$consumer_id, $shop_id, $order_id, $rating, $comment]);
    header("Location: my_purchases.php?status=" . ($_GET['status'] ?? 'All'));
    exit;
}

// Get status filter from URL, default to 'All'
$status = isset($_GET['status']) ? $_GET['status'] : 'All';

// Build SQL query based on status filter
if ($status === 'All') {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE consumer_id = ? ORDER BY order_date DESC");
    $stmt->execute([$consumer_id]);
} elseif ($status === 'To Rate') {
    $stmt = $conn->prepare("SELECT o.* FROM orders o LEFT JOIN ratings r ON o.shop_id = r.shop_id AND r.consumer_id = ? WHERE o.consumer_id = ? AND o.status = 'Completed' AND r.rating_id IS NULL ORDER BY o.order_date DESC");
    $stmt->execute([$consumer_id, $consumer_id]);
} elseif ($status === 'Rated') {
    $stmt = $conn->prepare("SELECT o.*, r.rating, r.comment FROM orders o INNER JOIN ratings r ON o.shop_id = r.shop_id AND r.consumer_id = ? WHERE o.consumer_id = ? AND o.status = 'Completed' ORDER BY o.order_date DESC");
    $stmt->execute([$consumer_id, $consumer_id]);
} else {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE consumer_id = ? AND status = ? ORDER BY order_date DESC");
    $stmt->execute([$consumer_id, $status]);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <a href="my_purchases.php?status=All" class="px-4 py-2 rounded <?= $status === 'All' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">All</a>
          <a href="my_purchases.php?status=Pending" class="px-4 py-2 rounded <?= $status === 'Pending' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Pending</a>
          <a href="my_purchases.php?status=Processing" class="px-4 py-2 rounded <?= $status === 'Processing' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Processing</a>
          <a href="my_purchases.php?status=Out for Delivery" class="px-4 py-2 rounded <?= $status === 'Out for Delivery' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Out for Delivery</a>
          <a href="my_purchases.php?status=Completed" class="px-4 py-2 rounded <?= $status === 'Completed' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Completed</a>
          <a href="my_purchases.php?status=Cancelled" class="px-4 py-2 rounded <?= $status === 'Cancelled' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Cancelled</a>
          <a href="my_purchases.php?status=To Rate" class="px-4 py-2 rounded <?= $status === 'To Rate' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">To Rate</a>
          <a href="my_purchases.php?status=Rated" class="px-4 py-2 rounded <?= $status === 'Rated' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">Rated</a>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <table class="min-w-full border rounded mb-4">
            <thead class="bg-blue-100">
              <tr>
                <th class="py-2 px-4 border-b">Order ID</th>
                <th class="py-2 px-4 border-b">Total</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Date</th>
                <?php if ($status === 'To Rate' || $status === 'Rated'): ?>
                  <th class="py-2 px-4 border-b">Rating</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php if (count($orders) === 0): ?>
                <tr><td colspan="<?= ($status === 'To Rate' || $status === 'Rated') ? 5 : 4 ?>" class="py-4 text-center text-gray-500">No orders found.</td></tr>
              <?php else: ?>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td class="py-2 px-4 border-b">#<?= htmlspecialchars($order['order_id']) ?></td>
                    <td class="py-2 px-4 border-b">₱<?= htmlspecialchars($order['total_amount']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['status']) ?></td>
                    <td class="py-2 px-4 border-b"><?= htmlspecialchars($order['order_date']) ?></td>
                    <?php if ($status === 'To Rate'): ?>
                      <td class="py-2 px-4 border-b">
                        <form method="post" class="flex items-center gap-2">
                          <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                          <input type="hidden" name="shop_id" value="<?= $order['shop_id'] ?>">
                          <select name="rating" class="border rounded px-2 py-1" required>
                            <option value="">Rate</option>
                            <option value="1">★</option>
                            <option value="2">★★</option>
                            <option value="3">★★★</option>
                            <option value="4">★★★★</option>
                            <option value="5">★★★★★</option>
                          </select>
                          <input type="text" name="comment" class="border rounded px-2 py-1" placeholder="Add a comment (optional)">
                          <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Submit</button>
                        </form>
                      </td>
                    <?php elseif ($status === 'Rated'): ?>
                      <td class="py-2 px-4 border-b">
                        <span class="text-yellow-500 font-bold"><?= str_repeat('★', $order['rating']) ?></span>
                        <?php if (!empty($order['comment'])): ?>
                          <div class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($order['comment']) ?></div>
                        <?php endif; ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>