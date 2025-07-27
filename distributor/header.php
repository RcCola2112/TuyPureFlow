<?php
// Ensure $username, $shopname, and $profilePic are set
$username = $username ?? "Juan Dela Cruz";
$shopname = $shopname ?? "Tuy Aqua Station";
$profilePic = $profilePic ?? "images/profile.jpg";
?>

<header class="bg-white shadow px-6 py-4 flex items-center justify-between">
  <!-- Left: Tuy PureFlow clickable -->
  <div class="flex items-center gap-6">
    <a href="dashboard.php" class="text-xl font-bold text-blue-600 hover:underline">Tuy PureFlow</a>
    <h2 class="text-lg font-semibold text-gray-700">
      Hello, <span class="text-blue-600"><?php echo htmlspecialchars($username); ?></span> of <span class="text-blue-600"><?php echo htmlspecialchars($shopname); ?></span>
    </h2>
  </div>
  <!-- Right: Notification + Profile + Logout -->
  <div class="flex items-center space-x-4">
    <!-- Notification Icon -->
    <button class="relative focus:outline-none">
      <svg class="w-6 h-6 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 00-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
      <!-- Example notification badge -->
      <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">3</span>
    </button>

    <!-- Profile -->
    <div class="flex items-center space-x-2">
      <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border">
      <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($username); ?></span>
    </div>

    <!-- Logout Button -->
    <a href="logout.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Logout</a>
  </div>
</header>
