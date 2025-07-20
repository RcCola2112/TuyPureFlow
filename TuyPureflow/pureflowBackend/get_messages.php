<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

$consumer_id = $_GET['consumer_id'] ?? null;
$distributor_id = $_GET['distributor_id'] ?? null;

if (!$consumer_id || !$distributor_id) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'tuypureflow';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$stmt = $conn->prepare('SELECT * FROM messages WHERE consumer_id = ? AND distributor_id = ? ORDER BY sent_at ASC');
$stmt->bind_param('ii', $consumer_id, $distributor_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
echo json_encode(['success' => true, 'messages' => $messages]);
$stmt->close();
$conn->close(); 