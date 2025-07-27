<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

$stmt = $conn->query('SELECT shop_id, name, distributor_id, location, contact_number, average_rating, open_time, close_time FROM shop');
$shops = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Shops</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .icon-btn {
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .icon-btn:hover {
        transform: scale(1.2);
    }
    .edit-icon {
        color: #2563eb; /* Blue */
    }
    .edit-icon:hover {
        color: #1d4ed8; /* Darker Blue */
    }
    .delete-icon {
        color: #dc2626; /* Red */
    }
    .delete-icon:hover {
        color: #b91c1c; /* Darker Red */
    }
</style>
</head>
<body class="bg-gray-100">
<div class="flex">
    <!-- Sidebar -->
    <?php
    $activePage = 'shops'; // For active highlight
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="ml-64 w-full p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Shops</h1>
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow">
                <i class="fas fa-plus mr-2"></i>Add Shop
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-blue-700 mb-6">Shop Management</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left border-collapse rounded-lg overflow-hidden shadow">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Shop ID</th>
                            <th class="py-3 px-4 font-semibold">Name</th>
                            <th class="py-3 px-4 font-semibold">Distributor</th>
                            <th class="py-3 px-4 font-semibold">Location</th>
                            <th class="py-3 px-4 font-semibold">Contact</th>
                            <th class="py-3 px-4 font-semibold">Rating</th>
                            <th class="py-3 px-4 font-semibold">Open</th>
                            <th class="py-3 px-4 font-semibold">Close</th>
                            <th class="py-3 px-4 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($shops as $shop): ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['shop_id']) ?></td>
                            <td class="py-3 px-4 font-medium"><?= htmlspecialchars($shop['name']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['distributor_id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['location']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['contact_number']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['average_rating']) ?> ⭐</td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['open_time']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($shop['close_time']) ?></td>
                            <td class="py-3 px-4 text-center flex justify-center gap-4">
                                <i class="fas fa-edit icon-btn edit-icon" title="Edit"></i>
                                <i class="fas fa-trash icon-btn delete-icon" title="Delete"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
