<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>

<div class="w-64 bg-white shadow-md p-6 flex flex-col justify-between border-r border-gray-200 h-screen fixed top-0 left-0">
  <div>
    <div class="text-blue-700 font-bold text-xl mb-6">PureFlow Admin</div>
    <nav class="space-y-3">
      <a href="dashboard.php" class="nav-link <?php echo ($activePage == 'dashboard') ? 'active-link' : ''; ?>">🏠 Dashboard</a>
      <a href="users.php" class="nav-link <?php echo ($activePage == 'users') ? 'active-link' : ''; ?>">👥 Manage Users</a>
      <a href="orders.php" class="nav-link <?php echo ($activePage == 'orders') ? 'active-link' : ''; ?>">📦 Manage Orders</a>
      <a href="shops.php" class="nav-link <?php echo ($activePage == 'shops') ? 'active-link' : ''; ?>">🏪 Manage Shops</a>
      <a href="messages.php" class="nav-link <?php echo ($activePage == 'messages') ? 'active-link' : ''; ?>">💬 Messages</a>
      <a href="analytics.php" class="nav-link <?php echo ($activePage == 'analytics') ? 'active-link' : ''; ?>">📊 Analytics</a>
      <a href="feedback.php" class="nav-link <?php echo ($activePage == 'feedback') ? 'active-link' : ''; ?>">⭐ Feedback</a>
      <a href="logs.php" class="nav-link <?php echo ($activePage == 'logs') ? 'active-link' : ''; ?>">📝 System Logs</a>
    </nav>
  </div>
  <div>
    <a href="logout.php" class="block py-2 px-4 rounded-lg text-red-600 hover:bg-red-50">🚪 Logout</a>
  </div>
</div>

<style>
.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  border-radius: 8px;
  color: #374151;
  text-decoration: none;
  font-weight: 500;
}
.nav-link:hover {
  background-color: #f0f4ff;
}
.active-link {
  background-color: #dbeafe;
  color: #1d4ed8;
  font-weight: 600;
}
</style>
