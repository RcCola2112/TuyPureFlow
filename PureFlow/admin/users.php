<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
require_once '../db.php';

// Fetch data
$stmt = $conn->query('SELECT admin_id, username, email, created_at FROM admin');
$pending_stmt = $conn->query("SELECT distributor_id, name, email, phone, status, created_at FROM distributor WHERE status = 'Pending'");
$pending_distributors = $pending_stmt->fetchAll();
$consumer_stmt = $conn->query("SELECT consumer_id, full_name, email, contact_number, username, created_at FROM consumer");
$consumers = $consumer_stmt->fetchAll();
$admins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .action-btn {
        padding: 8px;
        border-radius: 8px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        transition: 0.2s ease-in-out;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
    .tooltip {
        position: relative;
    }
    .tooltip::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 130%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: #fff;
        font-size: 12px;
        padding: 4px 6px;
        border-radius: 4px;
        opacity: 0;
        white-space: nowrap;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .tooltip:hover::after {
        opacity: 1;
    }
</style>
</head>
<body class="bg-gray-50 font-sans text-gray-700">
<div class="flex">

    <!-- Sidebar -->
    <?php
    $activePage = 'users';
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="ml-64 w-full p-8">
        <h1 class="text-2xl font-bold mb-6">Manage Users</h1>

        <div class="bg-white p-8 rounded-xl shadow mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700">User Management</h2>

            <!-- Pending Distributors -->
            <h3 class="text-md font-semibold mb-2 text-red-600">Pending Distributor Approvals</h3>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border rounded">
                    <thead class="bg-red-100">
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Contact</th>
                            <th class="py-2 px-4 border-b">Created</th>
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
                            <td class="py-2 px-4 border-b flex gap-2">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="approve_id" value="<?= $dist['distributor_id'] ?>">
                                    <button type="submit" class="action-btn bg-green-100 text-green-700 tooltip" data-tooltip="Approve">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reject_id" value="<?= $dist['distributor_id'] ?>">
                                    <button type="submit" class="action-btn bg-red-100 text-red-700 tooltip" data-tooltip="Reject">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Consumers -->
            <h3 class="text-md font-semibold mb-2 text-green-600">Consumers</h3>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border rounded">
                    <thead class="bg-green-100">
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Full Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Contact</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Created</th>
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
                            <td class="py-2 px-4 border-b flex gap-2">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="suspend_consumer_id" value="<?= $consumer['consumer_id'] ?>">
                                    <button type="submit" class="action-btn bg-orange-100 text-orange-700 tooltip" data-tooltip="Suspend">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_consumer_id" value="<?= $consumer['consumer_id'] ?>">
                                    <button type="submit" class="action-btn bg-red-100 text-red-700 tooltip" data-tooltip="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Admin Management -->
            <h3 class="text-md font-semibold mb-4 text-blue-600">Admins & Assistants</h3>
            <form method="POST" class="mb-4 flex gap-4 items-center">
                <input type="text" name="new_username" placeholder="Username" class="border rounded px-2 py-1" required>
                <input type="email" name="new_email" placeholder="Email" class="border rounded px-2 py-1" required>
                <input type="password" name="new_password" placeholder="Password" class="border rounded px-2 py-1" required>
                <button type="submit" name="add_admin" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full border rounded">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Created</th>
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
                            <td class="py-2 px-4 border-b flex gap-2">
                                <button class="action-btn bg-blue-100 text-blue-700 tooltip" data-tooltip="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_admin_id" value="<?= $admin['admin_id'] ?>">
                                    <button type="submit" class="action-btn bg-red-100 text-red-700 tooltip" data-tooltip="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
