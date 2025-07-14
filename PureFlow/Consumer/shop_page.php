<?php
// shop_page.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-100 font-sans">
  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <input type="text" placeholder="Search containers..." class="hidden md:block px-4 py-2 border rounded-full w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <div class="flex items-center gap-4">
        <a href="#" class="relative">
          <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13l-1.5-6M7 13H5.4M16 21a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z"></path>
          </svg>
          <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">2</span>
        </a>
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Sign Up</a>
      </div>
    </div>
  </header>

  <!-- Shop Banner -->
  <section class="bg-white shadow-sm mb-4">
    <div class="container mx-auto px-4 py-6 flex flex-col md:flex-row items-center gap-6">
      <img src="https://via.placeholder.com/150" class="w-36 h-36 object-cover rounded-full border" alt="Shop Logo">
      <div class="flex-1">
        <h1 class="text-2xl font-bold">Dela Cruz Water Station</h1>
        <p class="text-gray-600 text-sm">Tuy, Batangas</p>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-yellow-500">★★★★☆</span>
          <span class="text-sm text-gray-500">4.3 (120 reviews)</span>
        </div>
        <div class="mt-2 text-sm text-gray-600">
          <p>Operating Hours: 08:00 AM - 05:00 PM</p>
          <p>Contact: 0912-345-6789</p>
        </div>
      </div>
      <div class="mt-4 md:mt-0">
        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">Message Seller</a>
      </div>
    </div>
  </section>

  <!-- Product List -->
  <main class="container mx-auto px-4">
    <h2 class="text-lg font-semibold mb-4">Available Containers</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Container Card -->
      <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
        <img src="https://via.placeholder.com/300x150" alt="5 Gallon" class="w-full h-32 object-cover rounded">
        <h3 class="text-md font-semibold mt-3">5 Gallon Container</h3>
        <p class="text-gray-600 text-sm">₱30.00</p>
        <p class="text-gray-500 text-xs">In Stock: 25</p>
        <div class="flex flex-col gap-2 mt-3">
          <div class="flex items-center gap-2">
            <label for="qty1" class="text-sm">Qty:</label>
            <input id="qty1" type="number" min="1" max="25" value="1" class="w-16 text-center border rounded py-1">
          </div>
          <div class="flex items-center gap-2">
            <label for="option1" class="text-sm">Option:</label>
            <select id="option1" class="border rounded py-1 px-2 text-sm">
              <option value="with-container">With Container</option>
              <option value="refill-only">Refill Only</option>
            </select>
          </div>
        </div>
        <div class="flex gap-2 mt-3">
          <button class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg">Add to Cart</button>
          <button class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Buy Now</button>
        </div>
      </div>

      <!-- Example 2 -->
      <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
        <img src="https://via.placeholder.com/300x150" alt="3 Gallon" class="w-full h-32 object-cover rounded">
        <h3 class="text-md font-semibold mt-3">3 Gallon Container</h3>
        <p class="text-gray-600 text-sm">₱25.00</p>
        <p class="text-gray-500 text-xs">In Stock: 10</p>
        <div class="flex flex-col gap-2 mt-3">
          <div class="flex items-center gap-2">
            <label for="qty2" class="text-sm">Qty:</label>
            <input id="qty2" type="number" min="1" max="10" value="1" class="w-16 text-center border rounded py-1">
          </div>
          <div class="flex items-center gap-2">
            <label for="option2" class="text-sm">Option:</label>
            <select id="option2" class="border rounded py-1 px-2 text-sm">
              <option value="with-container">With Container</option>
              <option value="refill-only">Refill Only</option>
            </select>
          </div>
        </div>
        <div class="flex gap-2 mt-3">
          <button class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg">Add to Cart</button>
          <button class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Buy Now</button>
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
