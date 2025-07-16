<?php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['consumer_id'];

// Handle add to cart or buy now
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_to_cart']) || isset($_POST['buy_now']))) {
    $container_id = intval($_POST['container_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $qty = intval($_POST['qty'] ?? 1);
    $option = $_POST['option'] ?? '';

    // Remove shop_id from insert, as your cart table does NOT have a shop_id column
    $stmt = $conn->prepare("INSERT INTO cart (user_id, container_id, product_name, price, qty, type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $container_id, $name, $price, $qty, $option]);

    if (isset($_POST['buy_now'])) {
        header('Location: confirm_order.php');
        exit;
    } else {
        header('Location: cart.php');
        exit;
    }
}

// Handle delete selected
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $selected = $_POST['selected_items'] ?? [];
    if (!empty($selected)) {
        $in = str_repeat('?,', count($selected) - 1) . '?';
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id IN ($in) AND user_id = ?");
        $stmt->execute(array_merge($selected, [$user_id]));
    }
    header('Location: cart.php');
    exit;
}

// Fetch cart items grouped by shop
// Fix: Use the correct column name for shop reference in your cart table.
// Your cart table does NOT have a shop_id column, but it DOES have container_id.
// So, join container to shop using container_id, then shop_id.
$stmt = $conn->prepare(
    "SELECT c.*, s.name AS shop_name, s.shop_id 
     FROM cart c
     LEFT JOIN container ct ON c.container_id = ct.container_id
     LEFT JOIN shop s ON ct.shop_id = s.shop_id
     WHERE c.user_id = ?
     ORDER BY s.shop_id, c.cart_id"
);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Group items by shop_id
$grouped_cart = [];
foreach ($cart_items as $item) {
    // Fix: If shop_id is NULL (e.g. for items with container_id = 0), use 0 as key
    $shop_id = isset($item['shop_id']) ? $item['shop_id'] : 0;
    // Fix: If shop_name is empty, fallback to shop name from shop table or show "No Shop"
    $shop_name = !empty($item['shop_name']) ? $item['shop_name'] : 'No Shop';
    $grouped_cart[$shop_id]['shop_name'] = $shop_name;
    $grouped_cart[$shop_id]['items'][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-100 font-sans">
  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Hello, <?= htmlspecialchars($_SESSION['consumer_name']) ?></span>
        <a href="logout.php" class="text-sm text-gray-600 hover:text-blue-600">Logout</a>
      </div>
    </div>
  </header>

  <!-- Cart Content -->
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Cart</h1>

    <form method="POST" action="cart.php" id="cartForm">
      <div class="bg-white rounded-lg shadow p-6">
        <?php foreach ($grouped_cart as $shop): ?>
        <div class="mb-8">
          <h2 class="text-lg font-bold mb-2 text-blue-700"><?= htmlspecialchars($shop['shop_name']) ?></h2>
          <table class="w-full text-sm mb-2">
            <thead>
              <tr class="border-b">
                <th class="py-2 text-center"><input type="checkbox" id="selectAll" /></th>
                <th class="text-left py-2">Product</th>
                <th class="text-center py-2">Type</th>
                <th class="text-center py-2">Qty</th>
                <th class="text-center py-2">Price</th>
                <th class="text-center py-2">Total</th>
                <th class="text-center py-2">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($shop['items'] as $item): ?>
              <tr class="border-b">
                <td class="text-center">
                  <input type="checkbox" name="selected_items[]" value="<?= $item['cart_id'] ?>" class="item-checkbox" />
                </td>
                <td class="py-3"><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-center"><?= htmlspecialchars($item['type']) ?></td>
                <td class="text-center">
                  <input type="number" name="qty[<?= $item['cart_id'] ?>]" value="<?= $item['qty'] ?>" min="1" class="w-12 text-center border rounded">
                </td>
                <td class="text-center">₱<?= number_format($item['price'], 2) ?></td>
                <td class="text-center">₱<?= number_format($item['qty'] * $item['price'], 2) ?></td>
                <td class="text-center">
                  <a href="remove_cart_item.php?cart_id=<?= $item['cart_id'] ?>" class="text-red-500 hover:underline">Remove</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endforeach; ?>

        <!-- Summary Section -->
        <div class="mt-6 flex justify-end hidden" id="summarySection">
          <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg border">
            <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
            <div class="flex justify-between text-sm mb-2">
              <span>Subtotal</span>
              <span id="subtotal">₱0.00</span>
            </div>
            <div class="flex justify-between text-sm mb-2">
              <span>Delivery Fee</span>
              <span>₱15.00</span>
            </div>
            <div class="flex justify-between text-base font-bold border-t pt-2">
              <span>Total</span>
              <span id="total">₱15.00</span>
            </div>
            <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
              onclick="return !window.deleteClicked;">Proceed to Checkout</button>
            <button type="submit" name="delete_selected" class="mt-2 w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg"
              onclick="return confirm('Are you sure you want to delete these items in your cart?');">Delete Selected</button>
          </div>
        </div>
      </div>
    </form>
  </main>

  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>

  <script>
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const summary = document.getElementById('summarySection');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const selectAll = document.getElementById('selectAll');

    // Update cartData for JS summary calculations
    const cartData = <?= json_encode($cart_items) ?>;

    function updateSummary() {
      let subtotal = 0;
      let selected = 0;

      checkboxes.forEach((box, index) => {
        if (box.checked) {
          const id = box.value;
          const qtyInput = document.querySelector(`input[name="qty[${id}]"]`);
          const qty = parseInt(qtyInput.value) || 0;
          const item = cartData.find(i => i.cart_id == id);
          subtotal += item.price * qty;
          selected++;
        }
      });

      if (selected > 0) {
        summary.classList.remove('hidden');
        subtotalEl.innerText = `₱${subtotal.toFixed(2)}`;
        totalEl.innerText = `₱${(subtotal + 15).toFixed(2)}`;
      } else {
        summary.classList.add('hidden');
      }
    }

    checkboxes.forEach(box => {
      box.addEventListener('change', updateSummary);
    });

    document.querySelectorAll('input[type="number"]').forEach(input => {
      input.addEventListener('input', updateSummary);
    });

    selectAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = this.checked);
      updateSummary();
    });
  </script>
</body>
</html>
</html>
</html>
</html>
