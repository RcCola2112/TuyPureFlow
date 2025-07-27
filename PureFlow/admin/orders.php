<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

$stmt = $conn->query('SELECT order_id, consumer_id, order_date, status FROM orders');
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Orders</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .icon-btn {
        font-size: 1.4rem;
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }
    .icon-btn:hover {
        transform: scale(1.2);
        color: #1d4ed8; /* Darker blue on hover */
    }
</style>
</head>
<body class="bg-gray-100">
<div class="flex">
    <!-- Sidebar -->
    <?php
    $activePage = 'orders'; // highlight active page
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="ml-64 w-full p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Orders</h1>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-blue-700 mb-6">Order Management</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full text-left border-collapse rounded-lg overflow-hidden shadow">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Order ID</th>
                            <th class="py-3 px-4 font-semibold">Consumer ID</th>
                            <th class="py-3 px-4 font-semibold">Date</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="py-3 px-4"><?= htmlspecialchars($order['order_id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($order['consumer_id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($order['order_date']) ?></td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded text-white text-sm
                                    <?= $order['status'] === 'Completed' ? 'bg-green-500' : 'bg-yellow-500' ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <i class="fas fa-eye icon-btn text-blue-600" title="View Details"
                                onclick="viewOrderDetails(<?= $order['order_id'] ?>)"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal for Order Details -->
<div id="orderModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
        <h2 class="text-xl font-bold mb-4 text-blue-700">Order Details</h2>
        <div id="orderDetails" class="space-y-2 text-gray-700">
            Loading details...
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    $('#orderModal').removeClass('hidden');
    $('#orderDetails').html('Loading details...');
    $.get('fetch_order_details.php', { id: orderId }, function(data) {
        $('#orderDetails').html(data);
    });
}

function closeModal() {
    $('#orderModal').addClass('hidden');
}
</script>
</body>
</html>
