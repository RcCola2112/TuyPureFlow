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

$distributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Approved'")->fetchColumn();
$pendingDistributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Pending'")->fetchColumn();
$suspendedDistributorCount = $conn->query("SELECT COUNT(*) FROM distributor WHERE status = 'Suspended'")->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$consumerCount = $conn->query('SELECT COUNT(*) FROM consumer')->fetchColumn();
$suspendedConsumerCount = $conn->query('SELECT COUNT(*) FROM consumer WHERE suspended = 1')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM feedback')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analytics | PureFlow Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; }
</style>
</head>
<body>
<div class="flex">

    <!-- Sidebar -->
    <?php
    $activePage = 'analytics'; // highlight current page
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="ml-64 w-full p-8">
        <!-- Page Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Analytics Overview</h1>
        <p class="text-gray-600 mb-8">Comprehensive summary of distributors, shops, consumers, and feedback.</p>

        <!-- KPI Cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $cards = [
                ['count' => $distributorCount, 'label' => 'Approved Distributors', 'icon' => '✔️', 'color' => 'text-green-600'],
                ['count' => $pendingDistributorCount, 'label' => 'Pending Distributors', 'icon' => '⏳', 'color' => 'text-yellow-600'],
                ['count' => $suspendedDistributorCount, 'label' => 'Suspended Distributors', 'icon' => '⛔', 'color' => 'text-red-600'],
                ['count' => $shopCount, 'label' => 'Total Shops', 'icon' => '🏪', 'color' => 'text-blue-600'],
                ['count' => $consumerCount, 'label' => 'Total Consumers', 'icon' => '👥', 'color' => 'text-indigo-600'],
                ['count' => $suspendedConsumerCount, 'label' => 'Suspended Consumers', 'icon' => '🚫', 'color' => 'text-pink-600'],
                ['count' => $feedbackCount, 'label' => 'Feedback Entries', 'icon' => '💬', 'color' => 'text-purple-600'],
            ];

            foreach ($cards as $card) {
                echo '
                <div class="rounded-xl shadow-lg hover:shadow-xl transition transform hover:scale-105 p-6 flex flex-col items-center bg-white">
                    <div class="text-4xl mb-3">' . $card['icon'] . '</div>
                    <p class="text-4xl font-extrabold ' . $card['color'] . '">' . htmlspecialchars($card['count']) . '</p>
                    <p class="mt-2 text-gray-700 font-medium">' . $card['label'] . '</p>
                </div>
                ';
            }
            ?>
        </section>
    </main>
</div>
</body>
</html>
