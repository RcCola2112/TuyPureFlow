<?php
session_start();
include '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Fetch consumer by email
    $stmt = $conn->prepare("SELECT * FROM consumer WHERE email = ?");
    $stmt->execute([$email]);
    $consumer = $stmt->fetch();

    if ($consumer && password_verify($password, $consumer['password'])) {
        $_SESSION['consumer_id'] = $consumer['consumer_id'];
        $_SESSION['consumer_name'] = $consumer['name'];
        header('Location: landing_page.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
    <h1 class="text-2xl font-bold mb-6 text-blue-600 text-center">Consumer Login</h1>
    <?php if ($error): ?>
      <div class="mb-4 text-red-500 text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="mb-4">
      <label class="block mb-1 text-sm font-medium">Email</label>
      <input type="email" name="email" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-6">
      <label class="block mb-1 text-sm font-medium">Password</label>
      <input type="password" name="password" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold">Login</button>
    <p class="mt-4 text-center text-sm">Don't have an account? <a href="signup.php" class="text-blue-600 hover:underline">Sign Up</a></p>
  </form>
</body>
</html>