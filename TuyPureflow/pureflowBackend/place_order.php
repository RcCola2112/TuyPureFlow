<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$host = 'localhost';
$dbname = 'tuypureflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $consumer_id = $data['consumer_id'] ?? null;
    $shop_id = $data['shop_id'] ?? null;
    $container_id = $data['container_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    $price = $data['price'] ?? 0;
    $total_price = $data['total_price'] ?? ($price * $quantity);
    if (!$consumer_id || !$shop_id || !$container_id || !$quantity) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO orders (consumer_id, shop_id, status, total_price, created_at) VALUES (?, ?, 'pending', ?, NOW())");
        $stmt->execute([$consumer_id, $shop_id, $total_price]);
        $order_id = $pdo->lastInsertId();
        $stmt2 = $pdo->prepare("INSERT INTO order_items (order_id, container_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt2->execute([$order_id, $container_id, $quantity, $price]);
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Order placed successfully.']);
    } catch(PDOException $e) {
        $pdo->rollBack();
        error_log('Failed to place order: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to place order: ' . $e->getMessage()]);
    }
    exit;
}
echo json_encode(['error' => 'Invalid request method.']); 