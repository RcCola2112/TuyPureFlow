<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
require_once '../db.php';

$filter = $_GET['filter'] ?? 'week';

$labels = [];
$chartData = [];

switch ($filter) {
    case 'week':
        // last 12 weeks
        $startDate = new DateTime();
        $startDate->modify('-11 weeks');
        for ($i = 0; $i < 12; $i++) {
            // label like "W27 2025"
            $labels[] = 'W' . $startDate->format('W') . ' ' . $startDate->format('Y');
            $startDate->modify('+1 week');
        }

        $stmt = $conn->prepare("
            SELECT YEAR(order_date) AS yr, WEEK(order_date, 1) AS wk, COUNT(*) AS cnt
            FROM orders
            WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
            GROUP BY yr, wk
        ");
        $stmt->execute();
        $results = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $results[$row['yr'] . '-' . $row['wk']] = $row['cnt'];
        }

        $startDate = new DateTime();
        $startDate->modify('-11 weeks');
        for ($i = 0; $i < 12; $i++) {
            $key = $startDate->format('Y') . '-' . $startDate->format('W');
            $chartData[] = $results[$key] ?? 0;
            $startDate->modify('+1 week');
        }
        break;

    case 'month':
        // last 12 months
        $startDate = new DateTime();
        $startDate->modify('-11 months');
        for ($i = 0; $i < 12; $i++) {
            $labels[] = $startDate->format('M Y');
            $startDate->modify('+1 month');
        }

        $stmt = $conn->prepare("
            SELECT DATE_FORMAT(order_date, '%Y-%m') AS ym, COUNT(*) AS cnt
            FROM orders
            WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY ym
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $startDate = new DateTime();
        $startDate->modify('-11 months');
        for ($i = 0; $i < 12; $i++) {
            $key = $startDate->format('Y-m');
            $chartData[] = $results[$key] ?? 0;
            $startDate->modify('+1 month');
        }
        break;

    case 'year':
        // last 5 years
        $currentYear = (int)date('Y');
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $labels[] = (string)$y;
        }

        $stmt = $conn->prepare("
            SELECT YEAR(order_date) AS yr, COUNT(*) AS cnt
            FROM orders
            WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
            GROUP BY yr
        ");
        $stmt->execute();
        $results = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $results[$row['yr']] = $row['cnt'];
        }
        foreach ($labels as $year) {
            $chartData[] = $results[(int)$year] ?? 0;
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid filter']);
        exit;
}

// Overall counts
$orderCount = $conn->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$shopCount = $conn->query('SELECT COUNT(*) FROM shop')->fetchColumn();
$adminCount = $conn->query('SELECT COUNT(*) FROM admin')->fetchColumn();
$feedbackCount = $conn->query('SELECT COUNT(*) FROM consumer_feedback')->fetchColumn();

echo json_encode([
    'orderCount' => (int)$orderCount,
    'shopCount' => (int)$shopCount,
    'adminCount' => (int)$adminCount,
    'feedbackCount' => (int)$feedbackCount,
    'labels' => $labels,
    'chartData' => $chartData
]);
