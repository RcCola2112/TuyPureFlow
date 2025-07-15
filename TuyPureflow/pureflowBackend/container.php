<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Database connection
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
    $containerName = isset($data['Container_Name']) ? $data['Container_Name'] : (isset($data['name']) ? $data['name'] : null);

    // ADD CONTAINER
    if (!isset($data['action'])) {
        if (!isset($data['shop_id']) || !$containerName || !isset($data['type']) || !isset($data['price'])) {
            echo json_encode(['error' => 'Missing required fields: shop_id, Container_Name, type, price']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO container (shop_id, Container_Name, type, price, stock_quantity, damaged_quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['shop_id'],
                $containerName,
                $data['type'],
                $data['price'],
                $data['stock_quantity'] ?? 0,
                $data['damaged_quantity'] ?? 0
            ]);
            $container_id = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Container added successfully',
                'container_id' => $container_id
            ]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to add container: ' . $e->getMessage()]);
        }
        exit;
    }

    // DELETE CONTAINER
    if ($data['action'] === 'delete' && isset($data['container_id'])) {
        $container_id = $data['container_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM container WHERE container_id = ?");
            $stmt->execute([$container_id]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Container deleted successfully']);
            } else {
                echo json_encode(['error' => 'Failed to delete container: No rows affected']);
            }
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to delete container: ' . $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all containers for a given shop_id
    $shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : null;
    if (!$shop_id) {
        echo json_encode(['error' => 'Missing shop_id parameter']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM container WHERE shop_id = ?");
        $stmt->execute([$shop_id]);
        $containers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'containers' => $containers]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch containers: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $containerName = isset($data['Container_Name']) ? $data['Container_Name'] : (isset($data['name']) ? $data['name'] : null);
    if (!isset($data['container_id']) || !$containerName || !isset($data['type']) || !isset($data['price'])) {
        echo json_encode(['error' => 'Missing required fields: container_id, Container_Name, type, price']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("UPDATE container SET Container_Name = ?, type = ?, price = ?, stock_quantity = ?, damaged_quantity = ? WHERE container_id = ?");
        $stmt->execute([
            $containerName,
            $data['type'],
            $data['price'],
            $data['stock_quantity'] ?? 0,
            $data['damaged_quantity'] ?? 0,
            $data['container_id']
        ]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Container updated successfully']);
        } else {
            echo json_encode(['error' => 'Failed to update container: No rows affected']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to update container: ' . $e->getMessage()]);
    }
    exit;
} 