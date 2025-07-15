<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['distributor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing distributor_id']);
    exit;
}

$host = 'localhost';
$db = 'tuypureflow';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$distributor_id = $conn->real_escape_string($_GET['distributor_id']);
$sql = "SELECT business_name FROM shop WHERE distributor_id = '$distributor_id' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'shop_name' => $row['business_name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Shop not found for distributor.']);
}

$conn->close(); 