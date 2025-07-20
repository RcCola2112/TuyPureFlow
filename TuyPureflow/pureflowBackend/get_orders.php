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

$shop_id = $_GET['shop_id'] ?? null;
if (!$shop_id) {
    echo json_encode(['error' => 'Missing shop_id']);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT o.*, c.name as consumer_name, oi.order_item_id, oi.container_id, oi.quantity, oi.price as item_price, t.Container_Name FROM orders o JOIN consumer c ON o.consumer_id = c.consumer_id JOIN order_items oi ON o.order_id = oi.order_id JOIN container t ON oi.container_id = t.container_id WHERE o.shop_id = ? ORDER BY o.created_at DESC");
    $stmt->execute([$shop_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Group by order_id
    $orders = [];
    foreach ($rows as $row) {
        $oid = $row['order_id'];
        if (!isset($orders[$oid])) {
            $orders[$oid] = [
                'order_id' => $row['order_id'],
                'consumer_name' => $row['consumer_name'],
                'status' => $row['status'],
                'total_price' => $row['total_price'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }
        $orders[$oid]['items'][] = [
            'order_item_id' => $row['order_item_id'],
            'container_id' => $row['container_id'],
            'Container_Name' => $row['Container_Name'],
            'quantity' => $row['quantity'],
            'price' => $row['item_price']
        ];
    }
    echo json_encode(['success' => true, 'orders' => array_values($orders)]);
} catch(PDOException $e) {
    error_log('Failed to fetch orders: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch orders: ' . $e->getMessage()]);
} 