<?php
include('config.php');
header('Content-Type: application/json');

$today = date('Y-m-d');

// ── Total Sales & Transactions Today ──
$salesQuery = "
    SELECT 
        COUNT(*) AS transactions_today,
        SUM(total) AS total_sales_today
    FROM transactions
    WHERE DATE(date_time) = '$today'
      AND status = 'completed'
";
$salesRes = mysqli_query($conn, $salesQuery);
if (!$salesRes) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}
$salesData = mysqli_fetch_assoc($salesRes);

// ── Total Solds Today ──
$soldsQuery = "
    SELECT SUM(ti.qty) AS total_solds_today
    FROM transaction_items ti
    JOIN transactions t ON ti.transaction_id = t.transaction_id
    WHERE DATE(t.date_time) = '$today'
      AND t.status = 'completed'
";
$soldsRes = mysqli_query($conn, $soldsQuery);
if (!$soldsRes) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}
$soldsData = mysqli_fetch_assoc($soldsRes);

// ── Total Products in Stock ──
$productsQuery = "SELECT SUM(quantity) AS total_products FROM products";
$productsRes = mysqli_query($conn, $productsQuery);
if (!$productsRes) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}
$productsData = mysqli_fetch_assoc($productsRes);

// Low stock threshold
$lowStockThreshold = 10;

// Always return numbers
echo json_encode([
    'total_sales' => floatval($salesData['total_sales_today'] ?? 0),
    'transactions' => intval($salesData['transactions_today'] ?? 0),
    'total_solds' => intval($soldsData['total_solds_today'] ?? 0),
    'total_products' => intval($productsData['total_products'] ?? 0),
    'low_stock_threshold' => $lowStockThreshold
]);
?>