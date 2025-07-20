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

$distributor_id = $_GET['distributor_id'] ?? null;
if (!$distributor_id) {
    echo json_encode(['success' => false, 'message' => 'Missing distributor_id']);
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

$sql = 'SELECT DISTINCT c.consumer_id, c.name, c.email FROM messages m JOIN consumer c ON m.consumer_id = c.consumer_id WHERE m.distributor_id = ? ORDER BY c.name';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $distributor_id);
$stmt->execute();
$result = $stmt->get_result();
$consumers = [];
while ($row = $result->fetch_assoc()) {
    $consumers[] = $row;
}
echo json_encode(['success' => true, 'consumers' => $consumers]);
$stmt->close();
$conn->close(); 