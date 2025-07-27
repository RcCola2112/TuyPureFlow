<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Messages - Tuy PureFlow</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .active-tab { background-color: #2563eb; color: white; }
    .tab-btn { padding: 10px 20px; border-radius: 8px; transition: 0.2s; }
    .tab-btn:hover { background-color: #e0e7ff; }
</style>
</head>
<body class="bg-gray-100 flex">

<!-- Sidebar -->
<?php
$activePage = 'messages';
include 'sidebar.php';
?>

<!-- Main Content -->
<main class="ml-64 w-full p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-blue-700">Messages</h1>
        <button id="composeBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-pen"></i> Compose
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 mb-6">
        <button id="inboxBtn" class="tab-btn bg-blue-100 text-blue-700 font-medium">Inbox</button>
        <button id="sentBtn" class="tab-btn bg-blue-100 text-blue-700 font-medium">Sent</button>
        <button id="allBtn" class="tab-btn bg-blue-100 text-blue-700 font-medium">All Mail</button>
    </div>

    <!-- Message List -->
    <div id="messageList" class="space-y-4">
        <div class="p-6 bg-white rounded-xl shadow text-gray-500 text-center">Loading messages...</div>
    </div>
</main>

<!-- Compose Modal -->
<div id="composeModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <button id="closeCompose" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="text-xl font-bold mb-4 text-blue-700">Compose Message</h2>
        <form id="composeForm" class="space-y-4">
            <input type="hidden" name="sender_id" value="<?= $admin_id ?>">
            <input type="hidden" name="sender_type" value="admin">

            <div>
                <label class="block text-gray-700 mb-1 font-semibold">Receiver ID</label>
                <input type="number" name="receiver_id" placeholder="Enter receiver ID" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-300" required>
            </div>
            <div>
                <label class="block text-gray-700 mb-1 font-semibold">Receiver Type</label>
                <select name="receiver_type" class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-300" required>
                    <option value="distributor">Distributor</option>
                    <option value="consumer">Consumer</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-1 font-semibold">Message</label>
                <textarea name="content" placeholder="Type your message..." class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-300" required></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition">Send</button>
        </form>
    </div>
</div>

<script>
function loadMessages(type) {
    let params = {};
    if (type === 'inbox') {
        params.user2_id = <?= $admin_id ?>;
        params.user2_type = 'admin';
    } else if (type === 'sent') {
        params.user1_id = <?= $admin_id ?>;
        params.user1_type = 'admin';
    } else if (type === 'all') {
        params.admin_id = <?= $admin_id ?>;
    }
    $.get('../messages/fetch_messages.php', params, function(data) {
        let html = '';
        try {
            const res = JSON.parse(data);
            if (res.success && res.messages.length > 0) {
                res.messages.reverse().forEach(msg => {
                    let senderLabel = msg.sender_type.charAt(0).toUpperCase() + msg.sender_type.slice(1) + ' #' + msg.sender_id;
                    let receiverLabel = msg.receiver_type.charAt(0).toUpperCase() + msg.receiver_type.slice(1) + ' #' + msg.receiver_id;
                    html += `
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">${senderLabel} → ${receiverLabel}</p>
                            <p class="text-gray-800 font-medium">${msg.content}</p>
                        </div>
                        <span class="text-xs text-gray-400">${msg.sent_at || ''}</span>
                    </div>`;
                });
            } else {
                html = '<div class="p-6 bg-white rounded-lg shadow text-gray-500 text-center">No messages found.</div>';
            }
        } catch(e) { html = '<div class="p-6 bg-red-100 rounded-lg text-red-600 text-center">Error loading messages.</div>'; }
        $('#messageList').html(html);
    });
}

$(function() {
    loadMessages('inbox');
    $('#inboxBtn').on('click', function(){ setActive(this); loadMessages('inbox'); });
    $('#sentBtn').on('click', function(){ setActive(this); loadMessages('sent'); });
    $('#allBtn').on('click', function(){ setActive(this); loadMessages('all'); });
    $('#composeBtn').on('click', function(){ $('#composeModal').removeClass('hidden'); });
    $('#closeCompose').on('click', function(){ $('#composeModal').addClass('hidden'); });
    $('#composeForm').on('submit', function(e) {
        e.preventDefault();
        $.post('../messages/send_message.php', $(this).serialize(), function() {
            $('#composeModal').addClass('hidden');
            loadMessages('sent');
        });
    });
});

function setActive(button) {
    $('.tab-btn').removeClass('active-tab');
    $(button).addClass('active-tab');
}
</script>
</body>
</html>
