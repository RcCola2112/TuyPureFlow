<?php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['consumer_id'];

// Fetch selected cart item IDs
$selected_ids = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];

if (empty($selected_ids)) {
    header('Location: cart.php');
    exit;
}

// Prepare placeholders for IN clause
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

// Fetch cart items by selected IDs
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND cart_id IN ($placeholders)");
$stmt->execute(array_merge([$user_id], $selected_ids));
$selected_items = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
foreach ($selected_items as $item) {
    $subtotal += $item['qty'] * $item['price'];
}
$delivery_fee = 15;
$total = $subtotal + $delivery_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Confirm Order - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Hello, <?= htmlspecialchars($_SESSION['consumer_name']) ?></span>
        <a href="logout.php" class="text-sm text-gray-600 hover:text-blue-600">Logout</a>
      </div>
    </div>
  </header>

  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Confirm Your Order</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="text-left py-2">Product</th>
            <th class="text-center py-2">Type</th>
            <th class="text-center py-2">Qty</th>
            <th class="text-center py-2">Price</th>
            <th class="text-center py-2">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($selected_items as $item): ?>
            <tr class="border-b">
              <td class="py-3"><?= htmlspecialchars($item['product_name']) ?></td>
              <td class="text-center"><?= htmlspecialchars($item['type']) ?></td>
              <td class="text-center"><?= $item['qty'] ?></td>
              <td class="text-center">₱<?= number_format($item['price'], 2) ?></td>
              <td class="text-center">₱<?= number_format($item['qty'] * $item['price'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Summary and Confirm -->
    <div class="w-full max-w-md mx-auto bg-gray-50 p-4 rounded-lg border">
      <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
      <div class="flex justify-between text-sm mb-2">
        <span>Subtotal</span>
        <span>₱<?= number_format($subtotal, 2) ?></span>
      </div>
      <div class="flex justify-between text-sm mb-2">
        <span>Delivery Fee</span>
        <span>₱<?= number_format($delivery_fee, 2) ?></span>
      </div>
      <div class="flex justify-between text-base font-bold border-t pt-2">
        <span>Total</span>
        <span>₱<?= number_format($total, 2) ?></span>
      </div>

      <form action="checkout.php" method="post" class="mt-4">
        <?php foreach ($selected_ids as $id): ?>
          <input type="hidden" name="selected_items[]" value="<?= $id ?>">
        <?php endforeach; ?>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Confirm</button>
      </form>
    </div>
  </main>

  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
