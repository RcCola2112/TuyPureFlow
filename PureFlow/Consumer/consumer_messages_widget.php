
<?php
session_start();
if (!isset($_SESSION['consumer_id'])) {
    exit;
}
$consumer_id = $_SESSION['consumer_id'];
?>
<link rel="stylesheet" href="https://cdn.tailwindcss.com">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div id="messageWidget" class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg border-l z-50 transform translate-x-full transition-transform duration-300">
  <div class="flex justify-between items-center px-4 py-3 border-b bg-blue-600 text-white">
    <h2 class="text-lg font-semibold">My Messages</h2>
    <button onclick="toggleMessages()" class="text-white hover:text-gray-200">&times;</button>
  </div>
  <div class="p-4 border-b">
    <form id="sendForm" class="flex gap-2">
      <input type="hidden" name="sender_id" value="<?= $consumer_id ?>">
      <input type="hidden" name="sender_type" value="consumer">
      <input type="number" name="receiver_id" placeholder="Receiver ID" class="border rounded px-2 py-1 w-1/3" required>
      <select name="receiver_type" class="border rounded px-2 py-1 w-1/3" required>
        <option value="distributor">Distributor</option>
        <option value="admin">Admin</option>
      </select>
      <input type="text" name="content" placeholder="Type a message..." class="border rounded px-2 py-1 flex-1" required>
      <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Send</button>
    </form>
  </div>
  <div id="messages" class="p-4 overflow-y-auto h-[calc(100%-120px)] bg-gray-50"></div>
</div>
<?php
$currentFile = basename($_SERVER['PHP_SELF']);
if ($currentFile !== 'shop_page.php'):
?>
<button onclick="toggleMessages()" class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg z-50">
  📨
</button>
<?php endif; ?>
<script>
  function toggleMessages() {
    const widget = document.getElementById('messageWidget');
    widget.classList.toggle('translate-x-full');
  }
  function fetchMessages() {
    const receiver_id = $("input[name='receiver_id']").val();
    const receiver_type = $("select[name='receiver_type']").val();
    if (!receiver_id) return;
    $.get('../messages/fetch_messages.php', {
      user1_id: <?= $consumer_id ?>,
      user1_type: 'consumer',
      user2_id: receiver_id,
      user2_type: receiver_type
    }, function(data) {
      let html = '';
      try {
        const res = JSON.parse(data);
        if (res.success) {
          if (res.messages.length === 0) {
            html = '<div class="text-gray-500 text-sm">No messages yet.</div>';
          } else {
            res.messages.forEach(msg => {
              html += `<div class='mb-2'><b>${msg.sender_type} #${msg.sender_id}:</b> ${msg.content} <span class='text-xs text-gray-400'>${msg.sent_at}</span></div>`;
            });
          }
        } else {
          html = '<div class="text-red-600">' + res.error + '</div>';
        }
      } catch(e) { html = '<div class="text-red-600">Error loading messages.</div>'; }
      $('#messages').html(html);
    });
  }
  $(function() {
    fetchMessages();
    $('#sendForm').on('submit', function(e) {
      e.preventDefault();
      $.post('../messages/send_message.php', $(this).serialize(), function(data) {
        fetchMessages();
        $("input[name='content']").val('');
      });
    });
    $("input[name='receiver_id'], select[name='receiver_type']").on('change', fetchMessages);
  });
</script>
