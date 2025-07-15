<?php
// landing_page.php
session_start();
include '../db.php';

// Fetch water stations
$stmt = $conn->query("SELECT * FROM shop");
$stations = $stmt->fetchAll();
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
        <?php
          // Show cart icon and pending count for logged in users
          $pending_count = 0;
          if (isset($_SESSION['consumer_id'])) {
              $cart_stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
              $cart_stmt->execute([$_SESSION['consumer_id']]);
              $pending_count = $cart_stmt->fetchColumn();
          }
        ?>
        <a href="cart.php" class="relative">
          <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13l-1.5-6M7 13H5.4M16 21a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z"></path>
          </svg>
          <?php if ($pending_count > 0): ?>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"><?= $pending_count ?></span>
          <?php endif; ?>
        </a>
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
    <?php foreach ($stations as $station): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition shop-card">
      <img src="<?= htmlspecialchars($station['image_url'] ?? 'https://via.placeholder.com/300x150') ?>" alt="Shop" class="w-full h-36 object-cover" />
      <div class="p-4">
        <h2 class="text-md font-semibold truncate"><?= htmlspecialchars($station['name'] ?? 'Shop') ?></h2>
        <p class="text-gray-500 text-sm truncate"><?= htmlspecialchars($station['address'] ?? '') ?></p>
        <p class="text-sm mt-1"><?= htmlspecialchars($station['products'] ?? '5 Gallon - ₱30 • 3 Gallon - ₱25') ?></p>
        <div class="flex items-center text-sm mt-1">
          <span class="text-yellow-500 mr-1">
            <?php
              $rating = round($station['rating'] ?? 4);
              for ($i = 0; $i < 5; $i++) {
                echo $i < $rating ? '★' : '☆';
              }
            ?>
          </span>
          <span class="text-gray-500"><?= number_format($station['rating'] ?? 4.0, 1) ?> (<?= $station['reviews'] ?? 0 ?>)</span>
        </div>
        <?php if (($station['is_open'] ?? 1) == 1): ?>
          <span class="inline-block mt-2 px-2 py-1 text-xs rounded bg-green-100 text-green-700">Open Now</span>
        <?php else: ?>
          <span class="inline-block mt-2 px-2 py-1 text-xs rounded bg-red-100 text-red-700">Closed</span>
        <?php endif; ?>
        <a href="shop_page.php?shop_id=<?= urlencode($station['shop_id']) ?>" class="view-button mt-3 w-full py-2 text-center text-white rounded-lg block bg-blue-600 hover:bg-blue-700">View Shop</a>
      </div>
    </div>
    <?php endforeach; ?>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
