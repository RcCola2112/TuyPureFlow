<?php
session_start();
include '../db.php';

if (!isset($_SESSION['consumer_id'])) {
  exit;
}

$user_id = $_SESSION['consumer_id'];

// Fetch messages for this consumer
$stmt = $conn->prepare("SELECT * FROM messages WHERE receiver_id = ? AND receiver_role = 'Consumer' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll();
?>

<div id="messageWidget" class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg border-l z-50 transform translate-x-full transition-transform duration-300">
  <div class="flex justify-between items-center px-4 py-3 border-b bg-blue-600 text-white">
    <h2 class="text-lg font-semibold">My Messages</h2>
    <button onclick="toggleMessages()" class="text-white hover:text-gray-200">&times;</button>
  </div>

  <div class="p-4 overflow-y-auto h-[calc(100%-60px)] space-y-4">
    <?php if (count($messages) === 0): ?>
      <p class="text-gray-500 text-sm">No messages yet.</p>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <div class="bg-gray-100 p-3 rounded">
          <div class="flex justify-between mb-1">
            <span class="text-xs font-medium text-blue-700"><?= ucfirst($msg['category']) ?></span>
            <span class="text-xs text-gray-500"><?= date('M d, Y', strtotime($msg['created_at'])) ?></span>
          </div>
          <p class="text-sm text-gray-800"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php
// Only show floating button if not on shop_page.php
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
</script>
