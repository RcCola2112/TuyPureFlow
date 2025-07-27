<?php
// fetch_messages.php
// GET: user1_id, user1_type, user2_id, user2_type
include '../db.php';

$user1_id = intval($_GET['user1_id'] ?? 0);
$user1_type = $_GET['user1_type'] ?? '';
$user2_id = intval($_GET['user2_id'] ?? 0);
$user2_type = $_GET['user2_type'] ?? '';


// Inbox: messages where admin is receiver
if (isset($_GET['user2_id']) && isset($_GET['user2_type']) && $_GET['user2_type'] === 'admin') {
    $admin_id = intval($_GET['user2_id']);
    $stmt = $conn->prepare("SELECT * FROM messages WHERE receiver_id = ? AND receiver_type = 'admin' ORDER BY sent_at DESC");
    $stmt->execute([$admin_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}
// Sent: messages sent by admin
if (isset($_GET['user1_id']) && isset($_GET['user1_type']) && $_GET['user1_type'] === 'admin') {
    $admin_id = intval($_GET['user1_id']);
    $stmt = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? AND sender_type = 'admin' ORDER BY sent_at DESC");
    $stmt->execute([$admin_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}
// All Mail: all messages involving admin
if (isset($_GET['admin_id'])) {
    $admin_id = intval($_GET['admin_id']);
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND sender_type = 'admin') OR (receiver_id = ? AND receiver_type = 'admin') ORDER BY sent_at DESC");
    $stmt->execute([$admin_id, $admin_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}
// Default: require all params for conversation
if ($user1_id && $user1_type && $user2_id && $user2_type) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE 
        ((sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?) 
        OR (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?))
        ORDER BY sent_at ASC");
    $stmt->execute([$user1_id, $user1_type, $user2_id, $user2_type, $user2_id, $user2_type, $user1_id, $user1_type]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Missing required parameters.']);
