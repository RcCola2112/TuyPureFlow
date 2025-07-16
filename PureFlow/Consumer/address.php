<?php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$consumer_id = $_SESSION['consumer_id'];

// Fetch address from database
$stmt = $conn->prepare("SELECT * FROM address WHERE consumer_id = ?");
$stmt->execute([$consumer_id]);
$address = $stmt->fetch(PDO::FETCH_ASSOC);

// Default coordinates
$defaultLat = $address['latitude'] ?? 13.8602; // Tuy, Batangas lat
$defaultLng = $address['longitude'] ?? 120.7335; // Tuy, Batangas lng
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Address</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    #map {
      height: 300px;
      width: 100%;
    }
  </style>
  <!-- Replace this with the real BlightMap JS library if available -->
  <script src="https://cdn.blightmap.com/js/blightmap.min.js"></script>
</head>
<body class="bg-white p-6 text-sm font-sans">
  <h2 class="text-lg font-semibold mb-4">Your Shipping Address</h2>

  <?php if ($address): ?>
    <div class="mb-4">
      <p><?= htmlspecialchars($address['full_name']) ?></p>
      <p><?= htmlspecialchars($address['street']) ?>, <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['region']) ?></p>
      <p>Phone: <?= htmlspecialchars($address['contact_number']) ?></p>
    </div>
  <?php else: ?>
    <p class="text-gray-500 mb-4">No address found. Please add one below.</p>
  <?php endif; ?>

  <form method="POST" action="save_address.php" class="space-y-4">
    <input type="hidden" name="latitude" id="latitude" value="<?= $defaultLat ?>">
    <input type="hidden" name="longitude" id="longitude" value="<?= $defaultLng ?>">

    <div>
      <label class="block font-medium mb-1">Full Name</label>
      <input type="text" name="full_name" value="<?= $address['full_name'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="block font-medium mb-1">Street</label>
      <input type="text" name="street" value="<?= $address['street'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="block font-medium mb-1">City</label>
      <input type="text" name="city" value="<?= $address['city'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="block font-medium mb-1">Region</label>
      <input type="text" name="region" value="<?= $address['region'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="block font-medium mb-1">Contact Number</label>
      <input type="text" name="contact_number" value="<?= $address['contact_number'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mt-4">
      <label class="block font-medium mb-2">Select Your Location on Map</label>
      <div id="map"></div>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">Save Address</button>
  </form>

  <!-- Address Modal -->
  <div id="addressModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
      <button onclick="closeAddressModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
      <h2 class="text-lg font-semibold mb-4">Manage Address</h2>
      <?php if ($address): ?>
        <div class="mb-4">
          <p><?= htmlspecialchars($address['street']) ?>, <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['province'] ?? $address['region']) ?> <?= htmlspecialchars($address['zip_code'] ?? '') ?></p>
        </div>
      <?php else: ?>
        <p class="text-gray-500 mb-4">No address found. Please add one below.</p>
      <?php endif; ?>
      <form method="POST" action="save_address.php" class="space-y-4">
        <input type="hidden" name="latitude" id="latitude" value="<?= $defaultLat ?>">
        <input type="hidden" name="longitude" id="longitude" value="<?= $defaultLng ?>">
        <div>
          <label class="block font-medium mb-1">Street</label>
          <input type="text" name="street" value="<?= $address['street'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-medium mb-1">City</label>
          <input type="text" name="city" value="<?= $address['city'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-medium mb-1">Province</label>
          <input type="text" name="province" value="<?= $address['province'] ?? $address['region'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-medium mb-1">Zip Code</label>
          <input type="text" name="zip_code" value="<?= $address['zip_code'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block font-medium mb-2">Select Your Location on Map</label>
          <div id="map"></div>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">Save Address</button>
      </form>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Simulated BlightMap initialization
      const map = new BlightMap.Map({
        apiKey: "YOUR_API_KEY",
        container: "map",
        center: [<?= $defaultLat ?>, <?= $defaultLng ?>],
        zoom: 14
      });

      let marker = map.addMarker([<?= $defaultLat ?>, <?= $defaultLng ?>]);

      map.onClick(function (coords) {
        const [lat, lng] = coords;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        marker.setPosition([lat, lng]);
      });
    });

    function openAddressModal() {
      document.getElementById('addressModal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
    function closeAddressModal() {
      document.getElementById('addressModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>
</body>
</html>
