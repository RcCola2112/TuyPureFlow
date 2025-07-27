<?php
// Admin: Inventory overview for all shops
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// ...existing code for authentication...
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Overview</title>
  <link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-gray-100">
  <h1 class="text-2xl font-bold p-6">Inventory Overview</h1>
  <div class="p-6">View and manage inventory for all shops.</div>
</body>
</html>
