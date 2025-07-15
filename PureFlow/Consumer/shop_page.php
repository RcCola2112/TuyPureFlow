<?php
// shop_page.php
session_start();
include '../db.php';

// Get shop_id from URL
$shop_id = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 0;

// Fetch shop info
$stmt = $conn->prepare("SELECT * FROM shop WHERE shop_id = ?");
$stmt->execute([$shop_id]);
$station = $stmt->fetch();

// Fetch containers for this shop
$stmt = $conn->prepare("SELECT * FROM container WHERE shop_id = ?");
$stmt->execute([$shop_id]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($station['name'] ?? 'Shop') ?> - Tuy PureFlow</title>
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

  <!-- Shop Banner -->
  <section class="bg-white shadow-sm mb-4">
    <div class="container mx-auto px-4 py-6 flex flex-col md:flex-row items-center gap-6">
      <img src="<?= htmlspecialchars($station['image_url'] ?? 'https://via.placeholder.com/150') ?>" class="w-36 h-36 object-cover rounded-full border" alt="Shop Logo">
      <div class="flex-1">
        <h1 class="text-2xl font-bold"><?= htmlspecialchars($station['name'] ?? 'Shop Not Found') ?></h1>
        <p class="text-gray-600 text-sm"><?= htmlspecialchars($station['address'] ?? '') ?></p>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-yellow-500">
            <?php
              $rating = round($station['rating'] ?? 4);
              for ($i = 0; $i < 5; $i++) {
                echo $i < $rating ? '★' : '☆';
              }
            ?>
          </span>
          <span class="text-sm text-gray-500"><?= number_format($station['rating'] ?? 4.0, 1) ?> (<?= $station['reviews'] ?? 0 ?> reviews)</span>
        </div>
        <div class="mt-2 text-sm text-gray-600">
          <p>Operating Hours: <?= htmlspecialchars($station['hours'] ?? '08:00 AM - 05:00 PM') ?></p>
          <p>Contact: <?= htmlspecialchars($station['contact'] ?? '') ?></p>
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
      <?php foreach ($products as $product): ?>
      <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
        <form method="POST" action="cart.php">
          <input type="hidden" name="shop_id" value="<?= $shop_id ?>">
          <input type="hidden" name="container_id" value="<?= $product['container_id'] ?>">
          <input type="hidden" name="name" value="<?= htmlspecialchars($product['name'] ?? 'Container') ?>">
          <input type="hidden" name="price" value="<?= $product['price'] ?? 0 ?>">
          <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x150') ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Container') ?>" class="w-full h-32 object-cover rounded">
          <h3 class="text-md font-semibold mt-3"><?= htmlspecialchars($product['name'] ?? 'Container') ?></h3>
          <p class="text-gray-600 text-sm">₱<?= isset($product['price']) ? number_format($product['price'], 2) : 'N/A' ?></p>
          <p class="text-gray-500 text-xs">In Stock: <?= htmlspecialchars($product['stock'] ?? 'N/A') ?></p>
          <div class="flex flex-col gap-2 mt-3">
            <div class="flex items-center gap-2">
              <label for="qty<?= $product['container_id'] ?>" class="text-sm">Qty:</label>
              <?php
                $max_stock = (isset($product['stock']) && $product['stock'] > 0) ? intval($product['stock']) : 99;
              ?>
              <input id="qty<?= $product['container_id'] ?>" name="qty" type="number" min="1" max="<?= $max_stock ?>" value="1" class="w-16 text-center border rounded py-1">
            </div>
            <div class="flex items-center gap-2">
              <label for="option<?= $product['container_id'] ?>" class="text-sm">Option:</label>
              <select id="option<?= $product['container_id'] ?>" name="option" class="border rounded py-1 px-2 text-sm">
                <option value="with-container">With Container</option>
                <option value="refill-only">Refill Only</option>
              </select>
            </div>
          </div>
          <div class="flex gap-2 mt-3">
            <button type="submit" name="add_to_cart" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg">Add to Cart</button>
            <button type="submit" name="buy_now" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Buy Now</button>
          </div>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
?>
