<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

function log_debug($msg) {
    file_put_contents('debug_data.txt', date('Y-m-d H:i:s') . " " . $msg . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_debug('Invalid method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
log_debug('Input: ' . print_r($data, true));
$consumer_id = $data['consumer_id'] ?? null;
$distributor_id = $data['distributor_id'] ?? null;
$message = trim($data['message'] ?? '');
$sender = $data['sender'] ?? null;

if (!$consumer_id || !$distributor_id || !$message) {
    log_debug('Missing fields: ' . print_r($data, true));
    echo json_encode(['success' => false, 'message' => 'Missing fields', 'debug' => $data]);
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'tuypureflow';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    log_debug('DB ERROR: ' . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'DB error', 'error' => $conn->connect_error]);
    exit;
}

// Check if sender column exists
$sender_col = false;
$col_check = $conn->query("SHOW COLUMNS FROM messages LIKE 'sender'");
if ($col_check && $col_check->num_rows > 0) {
    $sender_col = true;
}

if ($sender_col) {
    $stmt = $conn->prepare('INSERT INTO messages (consumer_id, distributor_id, message, sender, sent_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->bind_param('iiss', $consumer_id, $distributor_id, $message, $sender);
    log_debug('SQL: INSERT INTO messages (consumer_id, distributor_id, message, sender, sent_at) VALUES (' . $consumer_id . ', ' . $distributor_id . ', ' . $message . ', ' . $sender . ', NOW())');
} else {
    $stmt = $conn->prepare('INSERT INTO messages (consumer_id, distributor_id, message, sent_at) VALUES (?, ?, ?, NOW())');
    $stmt->bind_param('iis', $consumer_id, $distributor_id, $message);
    log_debug('SQL: INSERT INTO messages (consumer_id, distributor_id, message, sent_at) VALUES (' . $consumer_id . ', ' . $distributor_id . ', ' . $message . ', NOW())');
}

if ($stmt->execute()) {
    log_debug('Message sent successfully.');
    echo json_encode(['success' => true]);
} else {
    log_debug('STMT ERROR: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Send failed', 'error' => $stmt->error]);
}
$stmt->close();
$conn->close(); 