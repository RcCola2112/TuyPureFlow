
<?php
require_once '../db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo '<script>window.location.replace("../index.html");</script>';
    exit;
}
// Fetch feedbacks with resolved status and reply
$stmt = $conn->query('SELECT id, user_id, message, date, resolved, reply FROM feedback');
$feedbacks = $stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>View Feedback</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen">
  <header class="admin-header shadow p-4 flex justify-between items-center">
    <div class="flex items-center">
      <img src="../assets/PureLogo.png" alt="PureFlow Logo" class="admin-logo">
      <h1 class="text-xl font-bold">View Feedback</h1>
    </div>
    <a href="dashboard.php" class="admin-btn">Dashboard</a>
  </header>
  <main class="p-8">
    <div class="admin-card bg-white p-8 mb-8">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Feedback</h2>
      <!-- Feedback table goes here -->
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded">
          <thead class="bg-blue-100">
            <tr>
              <th class="py-2 px-4 border-b">Feedback ID</th>
              <th class="py-2 px-4 border-b">User</th>
              <th class="py-2 px-4 border-b">Message</th>
              <th class="py-2 px-4 border-b">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($feedbacks as $fb): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($fb['id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($fb['user_id']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($fb['message']) ?></td>
              <td class="py-2 px-4 border-b"><?= htmlspecialchars($fb['date']) ?></td>
              <td class="py-2 px-4 border-b">
                <?php if (!$fb['resolved']): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="resolve_id" value="<?= $fb['id'] ?>">
                    <button type="submit" class="admin-btn bg-green-600 hover:bg-green-700">Mark Resolved</button>
                  </form>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="reply_id" value="<?= $fb['id'] ?>">
                    <input type="text" name="reply_message" placeholder="Reply..." class="border rounded px-2 py-1">
                    <button type="submit" class="admin-btn bg-blue-600 hover:bg-blue-700 ml-2">Reply</button>
                  </form>
                <?php else: ?>
                  <span class="text-green-700 font-semibold">Resolved</span>
                <?php endif; ?>
              </td>
              <td class="py-2 px-4 border-b">
                <?= $fb['reply'] ? htmlspecialchars($fb['reply']) : '<span class="text-gray-400">No reply</span>' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
    // Handle feedback actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['resolve_id'])) {
        $resolve_id = intval($_POST['resolve_id']);
        $conn->prepare("UPDATE feedback SET resolved = 1 WHERE id = ?")->execute([$resolve_id]);
        echo '<script>location.reload();</script>';
      }
      if (isset($_POST['reply_id']) && !empty($_POST['reply_message'])) {
        $reply_id = intval($_POST['reply_id']);
        $reply_message = trim($_POST['reply_message']);
        $conn->prepare("UPDATE feedback SET reply = ? WHERE id = ?")->execute([$reply_message, $reply_id]);
        echo '<script>location.reload();</script>';
      }
    }
    ?>
  </main>
</body>
</html>
