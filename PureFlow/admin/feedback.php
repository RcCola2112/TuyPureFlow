<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}

$stmt = $conn->query('SELECT id, user_id, message, date, resolved, reply FROM feedback');
$feedbacks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Management | PureFlow Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; }
</style>
</head>
<body>
<div class="flex">

    <!-- Sidebar -->
    <?php
    $activePage = 'feedback'; // highlight current page in sidebar
    include 'sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="ml-64 w-full p-8">
        <!-- Page Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Feedback Management</h1>

        <!-- Feedback Table -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold mb-6 text-blue-600">User Feedback</h2>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-gray-700 text-sm">
                    <thead class="bg-blue-100 text-blue-700">
                        <tr>
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">User</th>
                            <th class="py-3 px-4 text-left">Message</th>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Reply</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($feedbacks as $fb): ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="py-3 px-4"><?= htmlspecialchars($fb['id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($fb['user_id']) ?></td>
                            <td class="py-3 px-4 truncate max-w-xs"><?= htmlspecialchars($fb['message']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($fb['date']) ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($fb['resolved']): ?>
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Resolved</span>
                                <?php else: ?>
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?= $fb['reply'] ? htmlspecialchars($fb['reply']) : '<span class="text-gray-400">No reply</span>' ?>
                            </td>
                            <td class="py-3 px-4 flex justify-center gap-3">
                                <?php if (!$fb['resolved']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="resolve_id" value="<?= $fb['id'] ?>">
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-2 rounded-full bg-green-50 hover:bg-green-100 transition" title="Mark as Resolved">
                                            <i data-feather="check-circle"></i>
                                        </button>
                                    </form>
                                    <button onclick="openReplyModal(<?= $fb['id'] ?>)" class="text-blue-600 hover:text-blue-800 p-2 rounded-full bg-blue-50 hover:bg-blue-100 transition" title="Reply">
                                        <i data-feather="message-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative">
        <button onclick="closeReplyModal()" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-3xl">&times;</button>
        <h2 class="text-xl font-bold mb-4 text-blue-700">Reply to Feedback</h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" id="reply_id" name="reply_id">
            <textarea name="reply_message" placeholder="Type your reply..." class="border rounded-lg px-3 py-2 w-full focus:ring-2 focus:ring-blue-300" required></textarea>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg w-full hover:bg-blue-700 shadow">Send Reply</button>
        </form>
    </div>
</div>

<script>
feather.replace();
function openReplyModal(id) {
    document.getElementById('reply_id').value = id;
    document.getElementById('replyModal').classList.remove('hidden');
}
function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
}
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resolve_id'])) {
        $resolve_id = intval($_POST['resolve_id']);
        $conn->prepare("UPDATE feedback SET resolved = 1 WHERE id = ?")->execute([$resolve_id]);
        echo '<script>location.reload();</script>';
    }
    if (isset($_POST['reply_id']) && !empty($_POST['reply_message'])) {
        $reply_id = intval($_POST['reply_id']);
        $reply_message = trim($_POST['reply_message']);
        $conn->prepare("UPDATE feedback SET reply = ?, resolved = 1 WHERE id = ?")->execute([$reply_message, $reply_id]);
        echo '<script>location.reload();</script>';
    }
}
?>
</body>
</html>
