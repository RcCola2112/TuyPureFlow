<?php
// signup.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = trim($_POST['shop_name'] ?? '');
    $owner_name = trim($_POST['owner_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    $opening_time = trim($_POST['opening_time'] ?? '');
    $closing_time = trim($_POST['closing_time'] ?? '');

    // Only validate required info
    if (!$owner_name || !$contact_number || !$email || !$password || !$street || !$city || !$region || !$opening_time || !$closing_time) {
        $error = "All fields are required.";
    } elseif (!$shop_name) {
        $error = "Shop name is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM distributor WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            // Insert distributor with status 'Pending'
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO distributor (name, phone, email, password, created_at, status) VALUES (?, ?, ?, ?, NOW(), 'Pending')");
            if ($stmt->execute([$owner_name, $contact_number, $email, $hashed_password])) {
                $distributor_id = $conn->lastInsertId();
                // Insert shop with business_name
                $shop_location = "$street, $city, $region";
                $shop_stmt = $conn->prepare("INSERT INTO shop (distributor_id, name, location, contact_number, latitude, longitude, open_time, close_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $shop_stmt->execute([$distributor_id, $shop_name, $shop_location, $contact_number, $latitude, $longitude, $opening_time, $closing_time]);
                $success = "Registration submitted! Your account is pending approval by the admin.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
    if ($success) {
        // Redirect to distributor login page after successful registration
        header("Location: login.php?registered=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Distributor Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const steps = document.querySelectorAll('.step');
      const nextBtns = document.querySelectorAll('.next-btn');
      const prevBtns = document.querySelectorAll('.prev-btn');
      let currentStep = 0;

      function showStep(index) {
        steps.forEach((step, i) => {
          step.classList.toggle('hidden', i !== index);
        });
        updateProgress(index);
      }

      function updateProgress(index) {
        for (let i = 0; i < 3; i++) {
          const bar = document.getElementById('progress-step-' + i);
          if (i < index) {
            bar.classList.remove('bg-blue-600');
            bar.classList.add('bg-blue-300');
          } else if (i === index) {
            bar.classList.remove('bg-blue-300');
            bar.classList.add('bg-blue-600');
          } else {
            bar.classList.remove('bg-blue-600');
            bar.classList.add('bg-blue-300');
          }
        }
      }

      nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          // Validate required fields in the current step
          const currentFields = steps[currentStep].querySelectorAll('input, select');
          let allFilled = true;
          currentFields.forEach(field => {
            if (field.hasAttribute('required') && !field.value.trim()) {
              allFilled = false;
              field.classList.add('border-red-500');
            } else {
              field.classList.remove('border-red-500');
            }
          });
          if (!allFilled) {
            alert('Please fill in all required fields before proceeding.');
            return;
          }
          if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
          }
        });
      });

      prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
          }
        });
      });

      // Prevent form submission unless on last step
      const form = document.querySelector('form');
      form.addEventListener('submit', function(e) {
        if (currentStep !== steps.length - 1) {
          e.preventDefault();
        }
      });

      showStep(currentStep);
    });

    // Interactive Map Modal logic
    let mapInstance = null;
    let markerInstance = null;
    function openMapModal() {
      document.getElementById('mapModal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
      setTimeout(() => {
        if (!mapInstance) {
          mapInstance = L.map('map').setView([13.8602, 120.7335], 14);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
          }).addTo(mapInstance);
          markerInstance = L.marker([13.8602, 120.7335], { draggable: true }).addTo(mapInstance);

          function updateAddressFields(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
              .then(response => response.json())
              .then(data => {
                // Fill Street and House Number
                document.getElementsByName('street')[0].value =
                  data.address.road || data.address.pedestrian || data.address.neighbourhood || data.address.house_number || '';
                // Fill City
                document.getElementsByName('city')[0].value =
                  data.address.city || data.address.town || data.address.village || data.address.hamlet || 'Tuy';
                // Fill Region
                document.getElementsByName('region')[0].value =
                  data.address.state || data.address.province || data.address.region || 'Region IV-A';
              });
          }
          markerInstance.on('dragend', function (e) {
            var latlng = markerInstance.getLatLng();
            updateAddressFields(latlng.lat, latlng.lng);  
          });
          mapInstance.on('click', function (e) {
            markerInstance.setLatLng(e.latlng);
            updateAddressFields(e.latlng.lat, e.latlng.lng);
          });
        }
      }, 100);
    }
    function closeMapModal() {
      document.getElementById('mapModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>
</head>
<body class="bg-gray-100">
  <!-- Custom Fixed Header -->
  <header class="fixed top-0 left-0 w-full bg-white shadow z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="../index.html" class="text-xl font-bold text-blue-600 hover:underline">Tuy PureFlow</a>
      <a href="login.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Login</a>
    </div>
  </header>
  <!-- Add top padding to prevent content under header -->
  <div class="pt-20">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

      <h2 class="text-2xl font-bold mb-4 text-center">Distributor Signup</h2>
      <!-- Progress Bar (3 steps) -->
      <div class="flex justify-between mb-6">
        <div id="progress-step-0" class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
        <div id="progress-step-1" class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
        <div id="progress-step-2" class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
      </div>

      <form action="signup.php" method="POST" enctype="multipart/form-data">
        <!-- Step 1: Distributor Information -->
        <div class="step">
          <h3 class="text-lg font-semibold mb-2">Distributor Information</h3>
          <input type="text" name="owner_name" placeholder="Owner Name *" required class="w-full border p-2 mb-2">
          <input type="text" name="contact_number" placeholder="Contact Number *" required maxlength="11" class="w-full border p-2 mb-2">
          <input type="email" name="email" placeholder="Email Address *" required class="w-full border p-2 mb-2">
          <input type="password" name="password" placeholder="Password *" required class="w-full border p-2 mb-2">
          <div class="flex justify-end mt-4">
            <button type="button" class="next-btn bg-blue-600 text-white px-4 py-2 rounded">Next</button>
          </div>
        </div>

        <!-- Step 2: Business Information & Address -->
        <div class="step hidden">
          <h3 class="text-lg font-semibold mt-4 mb-2">Business Information</h3>
          <input type="text" name="shop_name" placeholder="Shop Name *" required class="w-full border p-2 mb-2">
          <h3 class="text-lg font-semibold mt-4 mb-2">Business Address</h3>
          <div class="mb-4">
            <label class="block font-medium mb-2">Shop Location (Latitude & Longitude)</label>
            <button type="button" onclick="openMapModal()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded mb-2">Use Map to Set Location</button>
            <div class="flex gap-2">
              <input type="text" name="latitude" id="latitude" placeholder="Latitude (use map)" readonly required class="w-1/2 border p-2 mb-2 bg-gray-100 cursor-not-allowed">
              <input type="text" name="longitude" id="longitude" placeholder="Longitude (use map)" readonly required class="w-1/2 border p-2 mb-2 bg-gray-100 cursor-not-allowed">
            </div>
            <small class="text-gray-500">Use the map to fill up the latitude and longitude.</small>
          </div>
          <input type="text" name="street" placeholder="Street and House Number *" required class="w-full border p-2 mb-2">
          <select name="city" required class="w-full border p-2 mb-2">
            <option value="">Select City *</option>
            <option value="Tuy">Tuy</option>
          </select>
          <select name="region" required class="w-full border p-2 mb-2">
            <option value="">Select Region *</option>
            <option value="Region IV-A">Region IV-A</option>
          </select>
          <div class="mb-4">
            <label class="block font-medium mb-2">Operating Hours</label>
            <div class="flex gap-4">
              <div>
                <label for="opening_time" class="block text-sm mb-1">Opening</label>
                <input type="time" id="opening_time" name="opening_time" required class="border p-2 rounded w-full">
              </div>
              <div>
                <label for="closing_time" class="block text-sm mb-1">Closing</label>
                <input type="time" id="closing_time" name="closing_time" required class="border p-2 rounded w-full">
              </div>
            </div>
          </div>
          <div class="flex justify-between mt-4">
            <button type="button" class="prev-btn bg-gray-600 text-white px-4 py-2 rounded">Back</button>
            <button type="button" class="next-btn bg-blue-600 text-white px-4 py-2 rounded">Next</button>
          </div>
        </div>

        <!-- Step 3: Review & Submit -->
        <div class="step hidden">
          <h3 class="text-lg font-semibold mt-4 mb-2">Review & Submit</h3>
          <p class="mb-4">Please review your information before submitting.</p>
          <div class="flex justify-between mt-4">
            <button type="button" class="prev-btn bg-gray-600 text-white px-4 py-2 rounded">Back</button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
          </div>
        </div>
      </form>

      <!-- Show error/success messages above the form -->
      <?php if ($error): ?>
        <div class="mb-4 text-red-500 text-center"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="mb-4 text-green-600 text-center"><?= $success ?></div>
      <?php endif; ?>

    </div>
  </div>
  <!-- Map Modal -->
  <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded shadow-lg p-4 w-full max-w-xl relative">
      <button onclick="closeMapModal()" class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl font-bold">&times;</button>
      <h2 class="text-lg font-bold mb-2">Select Business Location</h2>
      <div id="map" style="height: 350px; width: 100%;" class="rounded"></div>
      <p class="mt-2 text-sm text-gray-600">Drag the marker or click on the map to set your shop's location.</p>
      <button onclick="closeMapModal()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Done</button>
    </div>
  </div>
</body>
</html>
