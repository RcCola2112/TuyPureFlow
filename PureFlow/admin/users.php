<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
$stmt = $conn->query('SELECT admin_id, username, email, created_at FROM admin');
// Fetch pending distributors
$pending_stmt = $conn->query("SELECT distributor_id, name, email, phone, status, created_at FROM distributor WHERE status = 'Pending'");
$pending_distributors = $pending_stmt->fetchAll();
// Fetch consumers
$consumer_stmt = $conn->query("SELECT consumer_id, full_name, email, contact_number, username, created_at FROM consumer");
$consumers = $consumer_stmt->fetchAll();
$admins = $stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">Manage Users</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">User Management</h2>
      <!-- Pending Distributors Table -->
      <h3 class="text-md font-semibold mb-2 text-red-600">Pending Distributor Approvals</h3>
      <div class="overflow-x-auto mb-8">
        <table class="min-w-full border rounded">
          <thead class="bg-red-100">
            <tr>
              <th class="py-2 px-4 border-b">Distributor ID</th>
              <th class="py-2 px-4 border-b">Owner Name</th>
              <th class="py-2 px-4 border-b">Email</th>
              <th class="py-2 px-4 border-b">Contact</th>
              <th class="py-2 px-4 border-b">Created At</th>
              <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending_distributors as $dist): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($dist['distributor_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($dist['name']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($dist['email']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($dist['phone']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($dist['created_at']) ?></td>
              <td class="py-2 px-4 border-b">
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="approve_id" value="<?= $dist['distributor_id'] ?>">
                  <button type="submit" class="admin-btn bg-green-600 hover:bg-green-700">Approve</button>
                </form>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="reject_id" value="<?= $dist['distributor_id'] ?>">
                  <button type="submit" class="admin-btn bg-red-600 hover:bg-red-700 ml-2">Reject</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Consumer Management Table -->
      <h3 class="text-md font-semibold mb-2 text-green-600">Consumers</h3>
      <div class="overflow-x-auto mb-8">
        <table class="min-w-full border rounded">
          <thead class="bg-green-100">
            <tr>
              <th class="py-2 px-4 border-b">Consumer ID</th>
              <th class="py-2 px-4 border-b">Full Name</th>
              <th class="py-2 px-4 border-b">Email</th>
              <th class="py-2 px-4 border-b">Contact</th>
              <th class="py-2 px-4 border-b">Username</th>
              <th class="py-2 px-4 border-b">Created At</th>
              <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consumers as $consumer): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['consumer_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['full_name']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['email']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['contact_number']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['username']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($consumer['created_at']) ?></td>
              <td class="py-2 px-4 border-b">
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="suspend_consumer_id" value="<?= $consumer['consumer_id'] ?>">
                  <button type="submit" class="admin-btn bg-yellow-600 hover:bg-yellow-700">Suspend</button>
                </form>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="delete_consumer_id" value="<?= $consumer['consumer_id'] ?>">
                  <button type="submit" class="admin-btn bg-red-600 hover:bg-red-700 ml-2">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Existing Admins Table -->
      <h3 class="text-md font-semibold mb-2 text-blue-600">Admins & Assistants</h3>
      <form method="POST" class="mb-4 flex gap-4 items-center">
        <input type="text" name="new_username" placeholder="Username" class="border rounded px-2 py-1" required>
        <input type="email" name="new_email" placeholder="Email" class="border rounded px-2 py-1" required>
        <input type="password" name="new_password" placeholder="Password" class="border rounded px-2 py-1" required>
        <button type="submit" name="add_admin" class="admin-btn bg-blue-600 hover:bg-blue-700">Add Admin/Assistant</button>
      </form>
      <h3 class="text-md font-semibold mb-2 text-blue-600">Admins</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 border-b">Admin ID</th>
              <th class="py-2 px-4 border-b">Username</th>
              <th class="py-2 px-4 border-b">Email</th>
              <th class="py-2 px-4 border-b">Created At</th>
              <th class="py-2 px-4 border-b">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $admin): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($admin['admin_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($admin['username']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($admin['email']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($admin['created_at']) ?></td>
              <td class="py-2 px-4 border-b">
                <button class="admin-btn">Edit</button>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="delete_admin_id" value="<?= $admin['admin_id'] ?>">
                  <button type="submit" class="admin-btn bg-red-600 hover:bg-red-700 ml-2">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
<?php
// Handle approval/rejection, consumer/distributor, and admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['approve_id'])) {
    $approve_id = intval($_POST['approve_id']);
    // Check if distributor has a shop record
    $shop_check = $conn->prepare("SELECT shop_id FROM shop WHERE distributor_id = ?");
    $shop_check->execute([$approve_id]);
    if ($shop_check->rowCount() === 0) {
      // If no shop, do not approve and show error
      echo '<script>alert("Cannot approve distributor: No shop record found for this distributor.");</script>';
    } else {
      $conn->prepare("UPDATE distributor SET status = 'Approved' WHERE distributor_id = ?")->execute([$approve_id]);
      echo '<script>location.reload();</script>';
    }
  }
  if (isset($_POST['reject_id'])) {
    $reject_id = intval($_POST['reject_id']);
    $conn->prepare("UPDATE distributor SET status = 'Rejected' WHERE distributor_id = ?")->execute([$reject_id]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['suspend_consumer_id'])) {
    $suspend_id = intval($_POST['suspend_consumer_id']);
    $conn->prepare("UPDATE consumer SET suspended = 1 WHERE consumer_id = ?")->execute([$suspend_id]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['delete_consumer_id'])) {
    $delete_id = intval($_POST['delete_consumer_id']);
    $conn->prepare("DELETE FROM consumer WHERE consumer_id = ?")->execute([$delete_id]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['suspend_distributor_id'])) {
    $suspend_id = intval($_POST['suspend_distributor_id']);
    $conn->prepare("UPDATE distributor SET status = 'Suspended' WHERE distributor_id = ?")->execute([$suspend_id]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['delete_distributor_id'])) {
    $delete_id = intval($_POST['delete_distributor_id']);
    $conn->prepare("DELETE FROM distributor WHERE distributor_id = ?")->execute([$delete_id]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['add_admin'])) {
    $username = trim($_POST['new_username']);
    $email = trim($_POST['new_email']);
    $password = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
    $conn->prepare("INSERT INTO admin (username, email, password, created_at) VALUES (?, ?, ?, NOW())")->execute([$username, $email, $password]);
    echo '<script>location.reload();</script>';
  }
  if (isset($_POST['delete_admin_id'])) {
    $delete_id = intval($_POST['delete_admin_id']);
    $conn->prepare("DELETE FROM admin WHERE admin_id = ?")->execute([$delete_id]);
    echo '<script>location.reload();</script>';
  }
}
?>
    </div>
  </main>
</body>
</html>
