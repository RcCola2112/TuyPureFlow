<?php
session_start();
if (!isset($_SESSION['distributor_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
$distributor_id = $_SESSION['distributor_id'];
$currentPage = 'messages';
include '../db.php';

// Fetch distributor and shop info for header (delivery.php logic)
$stmt = $conn->prepare("SELECT name FROM distributor WHERE distributor_id = ?");
$stmt->execute([$distributor_id]);
$distributor = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['distributor_name'] = $distributor['name'] ?? '';

$shopStmt = $conn->prepare("SELECT name FROM shop WHERE distributor_id = ?");
$shopStmt->execute([$distributor_id]);
$shop = $shopStmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['shop_name'] = $shop ? $shop['name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Distributor Messages - Tuy PureFlow</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="flex bg-gray-100">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>
  <!-- Main Content -->
  <div class="ml-64 flex flex-col flex-1">
    <!-- Delivery.php style header -->
    <header class="bg-white shadow px-8 py-4 flex justify-between items-center w-full">
      <div class="flex items-center gap-2">
        <span class="text-2xl font-bold text-blue-700">Tuy PureFlow</span>
        <span class="ml-4 text-gray-700">
          Hello, 
          <span class="text-blue-700 font-semibold">
            <?= htmlspecialchars($_SESSION['distributor_name']) ?>
            <?php if (!empty($_SESSION['shop_name'])): ?>
              of <?= htmlspecialchars($_SESSION['shop_name']) ?>
            <?php endif; ?>
          </span>
        </span>
      </div>
      <div class="flex items-center gap-4">
        <div class="relative">
          <span class="material-icons text-blue-700" style="font-size: 28px;">notifications</span>
          <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-2">3</span>
        </div>
        <img src="../assets/profile.png" alt="Profile" class="w-8 h-8 rounded-full border object-cover">
        <span class="font-medium text-blue-700"><?= htmlspecialchars($_SESSION['distributor_name']) ?></span>
        <a href="logout.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Logout</a>
      </div>
    </header>
    <main class="flex flex-col items-center justify-center min-h-[80vh] p-8">
      <div class="w-full max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow p-8 mb-8 flex flex-col items-center">
          <div class="flex flex-col md:flex-row md:items-center w-full mb-6 gap-4 justify-between">
            <div class="flex gap-2">
              <button id="inboxBtn" class="bg-blue-100 text-blue-700 font-semibold px-4 py-2 rounded hover:bg-blue-200">📥 Inbox</button>
              <button id="sentBtn" class="bg-blue-100 text-blue-700 font-semibold px-4 py-2 rounded hover:bg-blue-200">📤 Sent</button>
              <button id="allBtn" class="bg-blue-100 text-blue-700 font-semibold px-4 py-2 rounded hover:bg-blue-200">📁 All Mail</button>
            </div>
            <button id="composeBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded flex items-center gap-2"><span>✉️</span>Compose</button>
          </div>
          <div id="messageList" class="w-full divide-y">
            <!-- Messages will be loaded here -->
          </div>
        </div>
      </div>
      <!-- Compose Modal -->
      <div id="composeModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
          <button id="closeCompose" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
          <h2 class="text-xl font-bold mb-4">Compose Message</h2>
          <form id="composeForm" class="space-y-3">
            <input type="hidden" name="sender_id" value="<?= $distributor_id ?>">
            <input type="hidden" name="sender_type" value="distributor">
            <input type="number" name="receiver_id" placeholder="Receiver ID" class="border rounded px-3 py-2 w-full" required>
            <select name="receiver_type" class="border rounded px-3 py-2 w-full" required>
              <option value="consumer">Consumer</option>
              <option value="admin">Admin</option>
            </select>
            <textarea name="content" placeholder="Type your message..." class="border rounded px-3 py-2 w-full" required></textarea>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Send</button>
          </form>
        </div>
      </div>
    </main>
  </div>
  <script>
    function loadMessages(type) {
      let params = {};
      if (type === 'inbox') {
        // Messages where distributor is the receiver
        params.user2_id = <?= $distributor_id ?>;
        params.user2_type = 'distributor';
      } else if (type === 'sent') {
        // Messages sent by distributor
        params.user1_id = <?= $distributor_id ?>;
        params.user1_type = 'distributor';
      } else if (type === 'all') {
        // All messages involving distributor (as sender or receiver)
        params.distributor_id = <?= $distributor_id ?>;
      }
      $.get('../messages/fetch_messages.php', params, function(data) {
        let html = '';
        try {
          const res = JSON.parse(data);
          if (res.success && res.messages.length > 0) {
            res.messages.reverse().forEach(msg => {
              let senderLabel = msg.sender_type.charAt(0).toUpperCase() + msg.sender_type.slice(1) + ' #' + msg.sender_id;
              let receiverLabel = msg.receiver_type.charAt(0).toUpperCase() + msg.receiver_type.slice(1) + ' #' + msg.receiver_id;
              html += `<div class='flex items-center px-4 py-3 hover:bg-blue-50 cursor-pointer'>
                <div class='w-1/4 font-semibold text-gray-700' title='From: ${senderLabel}'>${senderLabel}</div>
                <div class='w-2/4 text-gray-800 truncate' title='To: ${receiverLabel}'>${msg.content}</div>
                <div class='w-1/4 text-right text-xs text-gray-500'>${msg.sent_at || ''}</div>
              </div>`;
            });
          } else {
            html = '<div class="p-6 text-gray-500 text-center">No messages found.</div>';
          }
        } catch(e) { html = '<div class="p-6 text-red-600 text-center">Error loading messages.</div>'; }
        $('#messageList').html(html);
      });
    }
    $(function() {
      // Default to inbox
      loadMessages('inbox');
      $('#inboxBtn').on('click', function(){ loadMessages('inbox'); });
      $('#sentBtn').on('click', function(){ loadMessages('sent'); });
      $('#allBtn').on('click', function(){ loadMessages('all'); });
      $('#composeBtn').on('click', function(){ $('#composeModal').removeClass('hidden'); });
      $('#closeCompose').on('click', function(){ $('#composeModal').addClass('hidden'); });
      $('#composeForm').on('submit', function(e) {
        e.preventDefault();
        $.post('../messages/send_message.php', $(this).serialize(), function(data) {
          $('#composeModal').addClass('hidden');
          loadMessages('sent');
        });
      });
    });
  </script>
</body>
</html>
