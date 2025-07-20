<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get consumer_id from query string
$consumer_id = isset($_GET['consumer_id']) ? intval($_GET['consumer_id']) : 0;
if (!$consumer_id) {
    echo json_encode(['success' => false, 'message' => 'Missing consumer_id']);
    exit;
}

// DB connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'tuypureflow';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Fetch user info
$stmt = $conn->prepare('SELECT consumer_id, name, email, phone FROM consumer WHERE consumer_id = ?');
$stmt->bind_param('i', $consumer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
$stmt->close();
$conn->close(); 