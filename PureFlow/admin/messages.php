
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
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="bg-white shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="h-8 mr-3">
      <h1 class="text-xl font-bold text-blue-700">Admin Messages</h1>
    </div>
    <a href="dashboard.php" class="text-blue-600 font-semibold">Dashboard</a>
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
  </main>
  <!-- Compose Modal -->
  <div id="composeModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
      <button id="closeCompose" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
      <h2 class="text-xl font-bold mb-4">Compose Message</h2>
      <form id="composeForm" class="space-y-3">
        <input type="hidden" name="sender_id" value="<?= $admin_id ?>">
        <input type="hidden" name="sender_type" value="admin">
        <input type="number" name="receiver_id" placeholder="Receiver ID" class="border rounded px-3 py-2 w-full" required>
        <select name="receiver_type" class="border rounded px-3 py-2 w-full" required>
          <option value="distributor">Distributor</option>
          <option value="consumer">Consumer</option>
        </select>
        <textarea name="content" placeholder="Type your message..." class="border rounded px-3 py-2 w-full" required></textarea>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Send</button>
      </form>
    </div>
  </div>
  <script>
    function loadMessages(type) {
      let params = {};
      if (type === 'inbox') {
        // Messages where admin is the receiver
        params.user2_id = <?= $admin_id ?>;
        params.user2_type = 'admin';
      } else if (type === 'sent') {
        // Messages sent by admin
        params.user1_id = <?= $admin_id ?>;
        params.user1_type = 'admin';
      } else if (type === 'all') {
        // All messages involving admin (as sender or receiver)
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
