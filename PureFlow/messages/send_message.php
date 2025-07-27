<?php
// send_message.php
// POST: sender_id, sender_type, receiver_id, receiver_type, content
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = intval($_POST['sender_id'] ?? 0);
    $sender_type = $_POST['sender_type'] ?? '';
    $receiver_id = intval($_POST['receiver_id'] ?? 0);
    $receiver_type = $_POST['receiver_type'] ?? '';
    $content = trim($_POST['content'] ?? '');
    if ($sender_id && $receiver_id && $sender_type && $receiver_type && $content) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sender_id, $sender_type, $receiver_id, $receiver_type, $content]);
        echo json_encode(['success' => true, 'message' => 'Message sent.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    }
    exit;
}
echo json_encode(['success' => false, 'error' => 'Invalid request.']);
