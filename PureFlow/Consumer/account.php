<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

$user_id = $_SESSION['consumer_id'];
$user_name = $_SESSION['consumer_name']; // will update to full_name below

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM consumer WHERE consumer_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$user_name = $user['full_name'];

$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $new_full_name = trim($_POST['full_name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_contact_number = trim($_POST['contact_number'] ?? '');
    $new_username = trim($_POST['username'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$new_full_name || !$new_email || !$new_contact_number || !$new_username) {
        $update_msg = "Full name, email, contact number, and username are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $update_msg = "Invalid email address.";
    } elseif ($new_password && $new_password !== $confirm_password) {
        $update_msg = "Passwords do not match.";
    } else {
        // Check if email or username is taken by another user
        $stmt = $conn->prepare("SELECT consumer_id FROM consumer WHERE (email = ? OR username = ?) AND consumer_id != ?");
        $stmt->execute([$new_email, $new_username, $user_id]);
        if ($stmt->fetch()) {
            $update_msg = "Email or username already in use.";
        } else {
            // Update fields
            if ($new_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE consumer SET full_name = ?, email = ?, contact_number = ?, username = ?, password = ? WHERE consumer_id = ?");
                $success = $stmt->execute([$new_full_name, $new_email, $new_contact_number, $new_username, $hashed, $user_id]);
            } else {
                $stmt = $conn->prepare("UPDATE consumer SET full_name = ?, email = ?, contact_number = ?, username = ? WHERE consumer_id = ?");
                $success = $stmt->execute([$new_full_name, $new_email, $new_contact_number, $new_username, $user_id]);
            }
            if ($success) {
                $_SESSION['consumer_name'] = $new_full_name;
                $update_msg = "Account updated successfully!";
                // Refresh user info
                $stmt = $conn->prepare("SELECT * FROM consumer WHERE consumer_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $update_msg = "Update failed. Please try again.";
            }
        }
    }
}

// Fetch consumer addresses
$stmt = $conn->prepare("SELECT * FROM address WHERE consumer_id = ?");
$stmt->execute([$user_id]);
$addresses = $stmt->fetchAll();

// Handle edit address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_address'])) {
    $address_id = intval($_POST['address_id']);
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $zip_code = trim($_POST['zip_code'] ?? '');
    if ($street && $city && $region && $zip_code) {
        $stmt = $conn->prepare("UPDATE address SET street = ?, city = ?, region = ?, zip_code = ? WHERE address_id = ? AND consumer_id = ?");
        $stmt->execute([$street, $city, $region, $zip_code, $address_id, $user_id]);
        header('Location: account.php');
        exit;
    }
}

// Handle add address (via modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address_modal'])) {
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $zip_code = trim($_POST['zip_code'] ?? '');
    if ($street && $city && $region && $zip_code) {
        $stmt = $conn->prepare("INSERT INTO address (consumer_id, street, city, region, zip_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $street, $city, $region, $zip_code]);
        header('Location: account.php');
        exit;
    }
}

$change_pass_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    // Fetch hashed password
    $stmt = $conn->prepare("SELECT password FROM consumer WHERE consumer_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    if (!$current_password || !$new_password || !$confirm_password) {
        $change_pass_msg = "All password fields are required.";
    } elseif (!password_verify($current_password, $row['password'])) {
        $change_pass_msg = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $change_pass_msg = "New passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE consumer SET password = ? WHERE consumer_id = ?");
        if ($stmt->execute([$hashed, $user_id])) {
            $change_pass_msg = "Password changed successfully!";
        } else {
            $change_pass_msg = "Failed to change password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Account - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <!-- Header -->
  <header class="bg-white shadow sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <a href="landing_page.php" class="text-xl font-bold text-blue-600">Tuy PureFlow</a>
      <div class="text-sm text-gray-700">Hello, <?= htmlspecialchars($user_name) ?></div>
    </div>
  </header>

  <main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Account</h1>
    <div class="grid md:grid-cols-4 gap-6">
      <!-- Sidebar -->
      <aside class="bg-white p-4 rounded-lg shadow md:col-span-1">
        <nav class="space-y-4">
          <a href="account.php" class="block text-blue-600 font-medium">Account Info</a>
          <a href="my_purchases.php" class="block hover:text-blue-600">My Purchases</a>
          <a href="notification.php" class="block hover:text-blue-600">Notifications</a>
          <a href="logout.php" class="block text-red-500 hover:underline">Logout</a>
        </nav>
      </aside>

      <!-- Content -->
      <section class="md:col-span-3 space-y-8">
        <!-- Account Info Tabs/List -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-lg font-semibold mb-4">Account Info</h2>
          <ul class="flex gap-4 mb-6 text-sm font-medium border-b pb-2">
            <li>
              <button type="button" class="px-4 py-2 focus:outline-none tab-btn text-blue-600 border-b-2 border-blue-600" onclick="showTab('profileTab')">Profile</button>
            </li>
            <li>
              <button type="button" class="px-4 py-2 focus:outline-none tab-btn text-gray-600 hover:text-blue-600" onclick="showTab('addressTab')">Addresses</button>
            </li>
            <li>
              <button type="button" class="px-4 py-2 focus:outline-none tab-btn text-gray-600 hover:text-blue-600" onclick="showTab('passwordTab')">Change Password</button>
            </li>
          </ul>
          <!-- Profile Tab -->
          <div id="profileTab" class="tab-content">
            <form method="POST" class="space-y-4">
              <input type="hidden" name="update_account" value="1">
              <div>
                <label class="block mb-1 text-sm font-medium">Full Name</label>
                <input type="text" name="full_name" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Full Name *">
              </div>
              <div>
                <label class="block mb-1 text-sm font-medium">Email Address</label>
                <input type="email" name="email" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Email Address *">
              </div>
              <div>
                <label class="block mb-1 text-sm font-medium">Contact Number</label>
                <input type="text" name="contact_number" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>" placeholder="Contact Number *">
              </div>
              <div>
                <label class="block mb-1 text-sm font-medium">Username</label>
                <input type="text" name="username" required class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="Username *">
              </div>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded font-semibold">Save Changes</button>
            </form>
          </div>
          <!-- Addresses Tab -->
          <div id="addressTab" class="tab-content hidden">
            <div class="mt-2">
              <label class="block mb-1 text-sm font-medium">Addresses</label>
              <div class="space-y-4">
                <?php foreach ($addresses as $addr): ?>
                  <div class="border rounded p-3 flex justify-between items-center bg-gray-50">
                    <div>
                      <div class="font-semibold"><?= htmlspecialchars($addr['street']) ?></div>
                      <div class="text-sm text-gray-700">
                        <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['region']) ?> <?= htmlspecialchars($addr['zip_code']) ?>
                      </div>
                    </div>
                    <button type="button"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
                      onclick="openEditAddressModal(<?= $addr['address_id'] ?>, '<?= htmlspecialchars($addr['street'], ENT_QUOTES) ?>', '<?= htmlspecialchars($addr['city'], ENT_QUOTES) ?>', '<?= htmlspecialchars($addr['region'], ENT_QUOTES) ?>', '<?= htmlspecialchars($addr['zip_code'], ENT_QUOTES) ?>')">
                      Edit
                    </button>
                  </div>
                <?php endforeach; ?>
                <button type="button" onclick="openAddAddressModal()" class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">Add Address</button>
              </div>
            </div>
          </div>
          <!-- Change Password Tab -->
          <div id="passwordTab" class="tab-content hidden">
            <?php if ($change_pass_msg): ?>
              <div class="mb-2 text-center text-sm <?= strpos($change_pass_msg, 'success') !== false ? 'text-green-600' : 'text-red-500' ?>">
                <?= htmlspecialchars($change_pass_msg) ?>
              </div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
              <input type="hidden" name="change_password" value="1">
              <div>
                <label class="block mb-1 text-sm font-medium">Current Password</label>
                <input type="password" name="current_password" class="w-full border px-3 py-2 rounded" required>
              </div>
              <div>
                <label class="block mb-1 text-sm font-medium">New Password</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
              </div>
              <div>
                <label class="block mb-1 text-sm font-medium">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full border px-3 py-2 rounded" required>
              </div>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded font-semibold">Change Password</button>
            </form>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Add Address Modal -->
<div id="addAddressModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeAddAddressModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
    <h2 class="text-lg font-semibold mb-4">Add Address</h2>
    <form method="POST" class="space-y-2">
      <input type="hidden" name="add_address_modal" value="1">
      <input type="text" name="street" placeholder="Street" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="city" placeholder="City" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="region" placeholder="Region" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="zip_code" placeholder="Zip Code" required class="w-full border px-3 py-2 rounded">
      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-semibold">Add Address</button>
    </form>
  </div>
</div>

<!-- Edit Address Modal -->
<div id="editAddressModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button onclick="closeEditAddressModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
    <h2 class="text-lg font-semibold mb-4">Edit Address</h2>
    <form method="POST" class="space-y-2" id="editAddressForm">
      <input type="hidden" name="edit_address" value="1">
      <input type="hidden" name="address_id" id="edit_address_id">
      <input type="text" name="street" id="edit_street" placeholder="Street" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="city" id="edit_city" placeholder="City" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="region" id="edit_region" placeholder="Region" required class="w-full border px-3 py-2 rounded">
      <input type="text" name="zip_code" id="edit_zip" placeholder="Zip Code" required class="w-full border px-3 py-2 rounded">
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded font-semibold">Save Changes</button>
    </form>
  </div>
</div>

<script>
  function openAddAddressModal() {
    document.getElementById('addAddressModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
  function closeAddAddressModal() {
    document.getElementById('addAddressModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }
  function openEditAddressModal(id, street, city, region, zip) {
    document.getElementById('edit_address_id').value = id;
    document.getElementById('edit_street').value = street;
    document.getElementById('edit_city').value = city;
    document.getElementById('edit_region').value = region;
    document.getElementById('edit_zip').value = zip;
    document.getElementById('editAddressModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
  function closeEditAddressModal() {
    document.getElementById('editAddressModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }
  function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(function(tab) {
      tab.classList.add('hidden');
    });
    document.getElementById(tabId).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
      btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
      btn.classList.add('text-gray-600');
    });
    // Highlight active tab button
    const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn => btn.textContent.replace(/\s/g, '') === tabId.replace('Tab',''));
    if (activeBtn) {
      activeBtn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
      activeBtn.classList.remove('text-gray-600');
    }
  }
  // Show Profile tab by default
  document.addEventListener('DOMContentLoaded', function() {
    showTab('profileTab');
  });
</script>

  <footer class="mt-10 py-6 text-center text-gray-500 text-sm">
    &copy; 2025 Tuy PureFlow. All rights reserved.
  </footer>
</body>
</html>
</body>
</html>
