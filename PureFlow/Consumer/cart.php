<?php
// cart.php
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
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Sign Up</a>
      </div>
    </div>
  </header>

  <!-- Cart Content -->
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Cart</h1>
    <div class="bg-white rounded-lg shadow p-6">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b">
            <th class="py-2 text-center">
              <input type="checkbox" />
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
          <tr class="border-b">
            <td class="text-center">
              <input type="checkbox" />
            </td>
            <td class="py-3">5 Gallon Container</td>
            <td class="text-center">With Container</td>
            <td class="text-center">
              <input type="number" value="2" min="1" class="w-12 text-center border rounded">
            </td>
            <td class="text-center">₱30.00</td>
            <td class="text-center">₱60.00</td>
            <td class="text-center">
              <button class="text-red-500 hover:underline">Remove</button>
            </td>
          </tr>
          <tr class="border-b">
            <td class="text-center">
              <input type="checkbox" />
            </td>
            <td class="py-3">3 Gallon Container</td>
            <td class="text-center">Refill Only</td>
            <td class="text-center">
              <input type="number" value="1" min="1" class="w-12 text-center border rounded">
            </td>
            <td class="text-center">₱25.00</td>
            <td class="text-center">₱25.00</td>
            <td class="text-center">
              <button class="text-red-500 hover:underline">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Summary -->
      <div class="mt-6 flex justify-end">
        <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg border">
          <h2 class="text-lg font-semibold mb-3">Order Summary</h2>
          <div class="flex justify-between text-sm mb-2">
            <span>Subtotal</span>
            <span>₱85.00</span>
          </div>
          <div class="flex justify-between text-sm mb-2">
            <span>Delivery Fee</span>
            <span>₱15.00</span>
          </div>
          <div class="flex justify-between text-base font-bold border-t pt-2">
            <span>Total</span>
            <span>₱100.00</span>
          </div>
          <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Proceed to Checkout</button>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
