<?php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    exit; // Don't show modal to guests
}

$consumer_id = $_SESSION['consumer_id'];
$stmt = $conn->prepare("SELECT * FROM address WHERE consumer_id = ?");
$stmt->execute([$consumer_id]);
$address = $stmt->fetch(PDO::FETCH_ASSOC);

$defaultLat = $address['latitude'] ?? 13.8602;
$defaultLng = $address['longitude'] ?? 120.7335;
?>

<!-- Address Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
    <button onclick="closeAddressModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
    <h2 class="text-lg font-semibold mb-4">Manage Address</h2>

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
        <input type="text" name="province" value="<?= $address['province'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block font-medium mb-1">Zip Code</label>
        <input type="text" name="zip_code" value="<?= $address['zip_code'] ?? '' ?>" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block font-medium mb-2">Select Your Location on Map</label>
        <div id="map" class="w-full h-[300px] border"></div>
      </div>

      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">Save Address</button>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
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
