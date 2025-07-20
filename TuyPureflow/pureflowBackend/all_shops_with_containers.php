<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

try {
    // Fetch all shops, now including distributor_id
    $shopsStmt = $pdo->query("SELECT shop_id, business_name, distributor_id FROM shop");
    $shops = $shopsStmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($shops as $shop) {
        $shop_id = $shop['shop_id'];
        // Fetch containers for this shop
        $containersStmt = $pdo->prepare("SELECT * FROM container WHERE shop_id = ?");
        $containersStmt->execute([$shop_id]);
        $containers = $containersStmt->fetchAll(PDO::FETCH_ASSOC);
        $result[] = [
            'shop_id' => $shop_id,
            'shop_name' => $shop['business_name'],
            'distributor_id' => $shop['distributor_id'],
            'containers' => $containers
        ];
    }
    echo json_encode($result);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch shops/containers: ' . $e->getMessage()]);
} 