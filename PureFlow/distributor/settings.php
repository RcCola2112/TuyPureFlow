<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['distributor_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
include '../db.php';
$currentPage = 'settings';

// Get distributor info from session or database

$distributor_id = $_SESSION['distributor_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch();

$username = $distributor['name'] ?? '';
$stmtShop = $conn->prepare("SELECT name FROM shop WHERE distributor_id = ? LIMIT 1");
$stmtShop->execute([$distributor_id]);
$shopname = $stmtShop->fetchColumn() ?: '';
$profilePic = isset($distributor['profile_pic']) && $distributor['profile_pic'] ? $distributor['profile_pic'] : "images/profile.jpg";
$email = $distributor['email'] ?? '';
$phone = $distributor['phone'] ?? '';

$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $new_name = trim($_POST['name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_phone = trim($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$new_name || !$new_email || !$new_phone) {
        $update_msg = "Name, email, and phone are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $update_msg = "Invalid email address.";
    } elseif ($new_password && $new_password !== $confirm_password) {
        $update_msg = "New passwords do not match.";
    } elseif ($new_password && $current_password) {
        // Check current password
        if (!password_verify($current_password, $distributor['password'])) {
            $update_msg = "Current password is incorrect.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE distributor SET name = ?, email = ?, phone = ?, password = ? WHERE distributor_id = ?");
            $success = $stmt->execute([$new_name, $new_email, $new_phone, $hashed, $distributor_id]);
            if ($success) {
                $update_msg = "Account updated successfully!";
            } else {
                $update_msg = "Update failed.";
            }
        }
    } else {
        $stmt = $conn->prepare("UPDATE distributor SET name = ?, email = ?, phone = ? WHERE distributor_id = ?");
        $success = $stmt->execute([$new_name, $new_email, $new_phone, $distributor_id]);
        if ($success) {
            $update_msg = "Account updated successfully!";
        } else {
            $update_msg = "Update failed.";
        }
    }
    // Refresh distributor info
    $stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
    $stmt->execute([$distributor_id]);
// Fetch distributor info
$stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch();

$business_name = $distributor['business_name'] ?? '';
$owner_name = $distributor['owner_name'] ?? '';
$contact_number = $distributor['contact_number'] ?? '';
$email = $distributor['email'] ?? '';
$open_time = $distributor['open_time'] ?? '';
$close_time = $distributor['close_time'] ?? '';

$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $new_business_name = trim($_POST['business_name'] ?? '');
    $new_owner_name = trim($_POST['owner_name'] ?? '');
    $new_contact_number = trim($_POST['contact_number'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_open_time = trim($_POST['open_time'] ?? '');
    $new_close_time = trim($_POST['close_time'] ?? '');

    if (!$new_business_name || !$new_owner_name || !$new_contact_number || !$new_email) {
        $update_msg = "Business name, owner name, contact number, and email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $update_msg = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE distributor SET business_name = ?, owner_name = ?, contact_number = ?, email = ?, open_time = ?, close_time = ? WHERE distributor_id = ?");
        $success = $stmt->execute([$new_business_name, $new_owner_name, $new_contact_number, $new_email, $new_open_time, $new_close_time, $distributor_id]);
        if ($success) {
            $update_msg = "Account updated successfully!";
        } else {
            $update_msg = "Update failed.";
        }
    }
    // Refresh distributor info
    $stmt = $conn->prepare("SELECT * FROM distributor WHERE distributor_id = ?");
    $stmt->execute([$distributor_id]);
    $distributor = $stmt->fetch();
    $business_name = $distributor['business_name'] ?? '';
    $owner_name = $distributor['owner_name'] ?? '';
    $contact_number = $distributor['contact_number'] ?? '';
    $email = $distributor['email'] ?? '';
    $open_time = $distributor['open_time'] ?? '';
    $close_time = $distributor['close_time'] ?? '';
}
    $distributor = $stmt->fetch();
    $username = $distributor['name'] ?? "Juan Dela Cruz";
    $shopname = $_SESSION['shop_name'] ?? "Tuy Aqua Station";
    $profilePic = "images/profile.jpg"; // Placeholder
    $email = $distributor['email'] ?? '';
    $phone = $distributor['phone'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>
  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Page Content -->
    <main class="p-8 max-w-3xl">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Settings</h1>
      <div class="bg-white p-6 rounded-lg shadow space-y-6">
        <!-- Account Info -->
        <form method="POST">
          <input type="hidden" name="update_account" value="1">
          <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Account Information</h2>
            <div>
              <label class="block text-sm text-gray-600">Full Name</label>
              <input type="text" name="name" value="<?= htmlspecialchars($distributor['name'] ?? '') ?>" class="w-full mt-1 p-2 border rounded">
            </div>
            <div>
              <label class="block text-sm text-gray-600">Email Address</label>
              <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="w-full mt-1 p-2 border rounded">
            </div>
            <div>
              <label class="block text-sm text-gray-600">Phone Number</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" class="w-full mt-1 p-2 border rounded">
            </div>
          </div>
          <!-- Password Change -->
          <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Change Password</h2>
            <div>
              <label class="block text-sm text-gray-600">Current Password</label>
              <input type="password" name="current_password" class="w-full mt-1 p-2 border rounded">
            </div>
            <div>
              <label class="block text-sm text-gray-600">New Password</label>
              <input type="password" name="new_password" class="w-full mt-1 p-2 border rounded">
            </div>
            <div>
              <label class="block text-sm text-gray-600">Confirm New Password</label>
              <input type="password" name="confirm_password" class="w-full mt-1 p-2 border rounded">
            </div>
            <div class="text-right mt-4">
              <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save Changes</button>
            </div>
            <?php if ($update_msg): ?>
              <div class="mt-2 text-center text-sm <?= strpos($update_msg, 'success') !== false ? 'text-green-600' : 'text-red-500' ?>">
                <?= htmlspecialchars($update_msg) ?>
              </div>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>