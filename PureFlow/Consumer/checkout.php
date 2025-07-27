<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

// ...existing code...
$user_id = $_SESSION['consumer_id'];
$selected_ids = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];

if (empty($selected_ids)) {
    header('Location: cart.php');
    exit;
}

// Prepare placeholders for IN clause
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

// Fetch selected cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND cart_id IN ($placeholders)");
$stmt->execute(array_merge([$user_id], $selected_ids));
$cart_items = $stmt->fetchAll();

// Fetch default address for the logged-in user
$stmt = $conn->prepare("SELECT * FROM address WHERE consumer_id = ? AND is_default = 1 LIMIT 1");
$stmt->execute([$user_id]);
$default_address = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$default_address) {
    // fallback: get any address
    $stmt = $conn->prepare("SELECT * FROM address WHERE consumer_id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $default_address = $stmt->fetch(PDO::FETCH_ASSOC);
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['qty'] * $item['price'];
}
$delivery_fee = 15.00;
$total = $subtotal + $delivery_fee;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!$default_address) {
        echo '<script>alert("No address found. Please add a shipping address first."); window.location="address.php";</script>';
        exit;
    }

    // Assume all items are from the same shop (or use the first item's shop_id)
    $shop_id = 0;
    if (!empty($cart_items)) {
        // Get shop_id from container table
        $container_id = $cart_items[0]['container_id'];
        $stmt = $conn->prepare("SELECT shop_id FROM container WHERE container_id = ? LIMIT 1");
        $stmt->execute([$container_id]);
        $shop = $stmt->fetch(PDO::FETCH_ASSOC);
        $shop_id = $shop ? $shop['shop_id'] : 0;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (consumer_id, shop_id, total_amount, status, order_date) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->execute([$user_id, $shop_id, $total]);
    $order_id = $conn->lastInsertId();

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, container_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt->execute([$order_id, $item['container_id'], $item['qty'], $item['price']]);
    }

    // Remove items from cart
    $cart_ids = array_column($cart_items, 'cart_id');
    if (!empty($cart_ids)) {
        $in = str_repeat('?,', count($cart_ids) - 1) . '?';
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id IN ($in) AND user_id = ?");
        $stmt->execute(array_merge($cart_ids, [$user_id]));
    }

    // Redirect to confirmation page
    header('Location: confirm_order.php?order_id=' . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout - Tuy PureFlow</title>
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
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Shipping Address -->
      <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">
          Shipping Information
          <?php if ($default_address && isset($default_address['is_default']) && $default_address['is_default'] == 1): ?>
            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded align-middle">Default</span>
          <?php endif; ?>
        </h2>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-700 text-sm mt-1">
              <?php if ($default_address): ?>
                <?= htmlspecialchars($default_address['name'] ?? $_SESSION['consumer_name']) ?><br />
                <?= htmlspecialchars($default_address['contact_number'] ?? '') ?><br />
                <?= htmlspecialchars($default_address['street'] ?? '') ?><?= !empty($default_address['barangay']) ? ', ' . htmlspecialchars($default_address['barangay']) : '' ?><?= !empty($default_address['city']) ? ', ' . htmlspecialchars($default_address['city']) : '' ?><?= !empty($default_address['region']) ? ', ' . htmlspecialchars($default_address['region']) : '' ?><?= !empty($default_address['zip_code']) ? ', ' . htmlspecialchars($default_address['zip_code']) : '' ?><br />
              <?php else: ?>
                <span class="text-gray-500">No address found. Please add one.</span><br />
              <?php endif; ?>
            </p>
          </div>
          <a href="address.php" class="text-blue-600 text-sm hover:underline">Change</a>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
        <ul class="text-sm divide-y">
          <?php foreach ($cart_items as $item): ?>
            <li class="flex justify-between py-2">
              <span><?= htmlspecialchars($item['product_name']) ?> x<?= $item['qty'] ?></span>
              <span>₱<?= number_format($item['qty'] * $item['price'], 2) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="flex justify-between text-sm mt-4">
          <span>Subtotal</span>
          <span>₱<?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="flex justify-between text-sm">
          <span>Delivery Fee</span>
          <span>₱<?= number_format($delivery_fee, 2) ?></span>
        </div>
        <div class="flex justify-between text-base font-bold border-t pt-3 mt-3">
          <span>Total</span>
          <span>₱<?= number_format($total, 2) ?></span>
        </div>
        <form method="POST" action="checkout.php" class="mt-4">
          <?php foreach ($selected_ids as $id): ?>
            <input type="hidden" name="selected_items[]" value="<?= $id ?>">
          <?php endforeach; ?>
          <button type="submit" name="place_order" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Place Order</button>
        </form>
      </div>
    </div>
  </main>

  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
