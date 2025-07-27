<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
?>
}

$user_id = $_SESSION['consumer_id'];

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        <?php if (isset($_SESSION['consumer_id'])): ?>
          <span class="text-sm text-gray-600">Hello, <?= htmlspecialchars($_SESSION['consumer_name']) ?></span>
          <a href="logout.php" class="text-sm text-gray-600 hover:text-blue-600">Logout</a>
        <?php else: ?>
          <a href="login.php" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
          <a href="signup.php" class="text-sm text-gray-600 hover:text-blue-600">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Cart Content -->
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Cart</h1>

    <form method="POST" action="checkout.php" id="checkoutForm">
      <div class="bg-white rounded-lg shadow p-6 overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2 text-center">
                <input type="checkbox" id="selectAll" />
              </th>
              <th class="text-left py-2">Product</th>
              <th class="text-center py-2">Type</th>
              <th class="text-center py-2">Qty</th>
              <th class="text-center py-2">Price</th>
              <th class="text-center py-2">Total</th>
              <th class="text-center py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_items as $item): ?>
            <tr class="border-b">
              <td class="text-center">
                <input type="checkbox" class="item-checkbox" name="cart_ids[]" value="<?= $item['cart_id'] ?>" />
              </td>
              <td class="py-3"><?= htmlspecialchars($item['product_name']) ?></td>
              <td class="text-center"><?= htmlspecialchars($item['type']) ?></td>
              <td class="text-center">
                <input type="number" name="qty[<?= $item['cart_id'] ?>]" value="<?= $item['qty'] ?>" min="1" class="w-12 text-center border rounded" />
              </td>
              <td class="text-center">₱<?= number_format($item['price'], 2) ?></td>
              <td class="text-center">₱<?= number_format($item['qty'] * $item['price'], 2) ?></td>
              <td class="text-center">
                <a href="remove_item.php?id=<?= $item['cart_id'] ?>" class="text-red-500 hover:underline">Remove</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Summary -->
        <div id="summarySection" class="mt-6 flex justify-end hidden">
          <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg border">
            <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
            <div class="flex justify-between text-sm mb-2">
              <span>Subtotal</span>
              <span id="subtotal">₱0.00</span>
            </div>
            <div class="flex justify-between text-sm mb-2">
              <span>Delivery Fee</span>
              <span id="deliveryFee">₱15.00</span>
            </div>
            <div class="flex justify-between text-base font-bold border-t pt-2">
              <span>Total</span>
              <span id="total">₱0.00</span>
            </div>
            <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
              Proceed to Checkout
            </button>
          </div>
        </div>
      </div>
    </form>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>

  <script>
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const summarySection = document.getElementById('summarySection');
    const subtotalSpan = document.getElementById('subtotal');
    const totalSpan = document.getElementById('total');
    const deliveryFee = 15;

    function updateSummary() {
      let subtotal = 0;
      let anyChecked = false;

      checkboxes.forEach(cb => {
        if (cb.checked) {
          anyChecked = true;
          const row = cb.closest('tr');
          const qty = parseInt(row.querySelector('input[type="number"]').value);
          const price = parseFloat(row.children[4].textContent.replace('₱', ''));
          subtotal += qty * price;
        }
      });

      if (anyChecked) {
        summarySection.classList.remove('hidden');
        subtotalSpan.textContent = `₱${subtotal.toFixed(2)}`;
        totalSpan.textContent = `₱${(subtotal + deliveryFee).toFixed(2)}`;
      } else {
        summarySection.classList.add('hidden');
        subtotalSpan.textContent = '₱0.00';
        totalSpan.textContent = '₱0.00';
      }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));
    document.querySelectorAll('input[type="number"]').forEach(input => input.addEventListener('input', updateSummary));
    document.getElementById('selectAll').addEventListener('change', e => {
      checkboxes.forEach(cb => cb.checked = e.target.checked);
      updateSummary();
    });

    // Initial call in case some checkboxes are pre-checked
    updateSummary();
  </script>
</body>
</html>
