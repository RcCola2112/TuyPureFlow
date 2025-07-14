<?php
// landing_page.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tuy PureFlow - Consumer Shop UI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-100 font-sans">
  <!-- Navbar -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <h1 class="text-xl font-bold text-blue-600">Tuy PureFlow</h1>
      <input type="text" placeholder="Search for water stations..." class="px-4 py-2 border rounded-full w-1/2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <div class="flex gap-4 items-center">
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
        <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Sign Up</a>
      </div>
    </div>
  </header>

  <!-- Categories (Tabs) -->
  <nav class="bg-white py-2 shadow-sm">
    <div class="container mx-auto px-4 flex gap-3 overflow-x-auto text-sm font-medium whitespace-nowrap">
      <button class="bg-blue-500 text-white px-4 py-2 rounded-full">Near Me</button>
      <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full">Highest Sale</button>
      <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full">Top Rated</button>
      <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full">Open Now</button>
      <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full">Most Affordable</button>
    </div>
  </nav>
  <!-- Shop Grid -->
  <main class="container mx-auto px-4 mt-6 grid gap-6 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <!-- Card Example -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition shop-card">
      <img src="https://via.placeholder.com/300x150" alt="Shop" class="w-full h-36 object-cover" />
      <div class="p-4">
        <h2 class="text-md font-semibold truncate">Dela Cruz Water Station</h2>
        <p class="text-gray-500 text-sm truncate">Tuy, Batangas</p>
        <p class="text-sm mt-1">5 Gallon - ₱30 • 3 Gallon - ₱25</p>
        <div class="flex items-center text-sm mt-1">
          <span class="text-yellow-500 mr-1">★★★★☆</span>
          <span class="text-gray-500">4.3 (120)</span>
        </div>
        <span class="inline-block mt-2 px-2 py-1 text-xs rounded bg-green-100 text-green-700">Open Now</span>
        <button class="view-button mt-3 w-full py-2 text-center text-white rounded-lg">View Shop</button>
      </div>
    </div>
    <!-- Repeat for other shops -->
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
