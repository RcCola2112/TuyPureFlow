<?php
// checkout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-100 font-sans">
  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <div class="flex items-center gap-4">
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Sign Up</a>
      </div>
    </div>
  </header>

  <!-- Checkout Content -->
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Shipping Address -->
      <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Shipping Information</h2>
        <form action="#" method="post" class="space-y-4">
          <div>
            <label class="block text-sm font-medium">Order Type</label>
            <select name="order_type" class="w-full border rounded px-3 py-2">
              <option value="normal">Normal</option>
              <option value="special">Special</option>
            </select>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <label class="block text-sm font-medium">Shipping Address</label>
              <p class="text-gray-700 text-sm mt-1">Juan Dela Cruz<br>Tuy, Batangas, Philippines<br>0912-345-6789</p>
            </div>
            <button type="button" class="text-blue-600 text-sm hover:underline">Change</button>
          </div>
        </form>
      </div>

      <!-- Order Summary -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
        <ul class="text-sm divide-y">
          <li class="flex justify-between py-2">
            <span>5 Gallon Container x2</span>
            <span>₱60.00</span>
          </li>
          <li class="flex justify-between py-2">
            <span>3 Gallon Refill x1</span>
            <span>₱25.00</span>
          </li>
        </ul>
        <div class="flex justify-between font-medium text-sm mt-4">
          <span>Subtotal</span>
          <span>₱85.00</span>
        </div>
        <div class="flex justify-between font-medium text-sm">
          <span>Delivery Fee</span>
          <span>₱15.00</span>
        </div>
        <div class="flex justify-between font-bold text-base border-t pt-3 mt-3">
          <span>Total</span>
          <span>₱100.00</span>
        </div>
        <button class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Place Order</button>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
