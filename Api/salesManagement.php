<?php
// Api/salesManagement.php

require_once 'config.php';
require_once 'inventoryManagement.php';

// check for db if connected
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed. Check config.php']);
    exit;
}

if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    // get product
    if ($_GET['api'] === 'products') {
        $query = "SELECT id, product_code, CONCAT(product_code, ' - ', product_type) AS name, price, quantity AS stock FROM products WHERE quantity > 0";
        $result = mysqli_query($conn, $query);

        // error if sql fails
        if (!$result) {
            echo json_encode(['error' => 'SQL Error: ' . mysqli_error($conn)]);
            exit;
        }

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['id'] = intval($row['id']);
            $row['price'] = floatval($row['price']);
            $row['stock'] = intval($row['stock']);
            $products[] = $row;
        }
        echo json_encode($products);
        exit;
    }

    //  checkout
    if ($_GET['api'] === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $cart = $body['cart'] ?? [];
        $paid = floatval($body['paid'] ?? 0);

        if (empty($cart)) { echo json_encode(['error' => 'Cart is empty.']); exit; }

        $total = 0;
        $productsToBuy = [];
        foreach ($cart as $item) {
            $pid = intval($item['product_id']);
            $qty_needed = intval($item['qty']);

            $res = mysqli_query($conn, "SELECT product_code, CONCAT(product_code, ' - ', product_type) AS name, price, quantity FROM products WHERE id = $pid");
            $prod = mysqli_fetch_assoc($res);

            if (!$prod || $prod['quantity'] < $qty_needed) {
                echo json_encode(['error' => "Insufficient stock for " . ($prod['name'] ?? 'Unknown Item')]); exit;
            }
            $productsToBuy[$pid] = $prod;
            $total += ($prod['price'] * $qty_needed);
        }

        if ($paid < $total) { echo json_encode(['error' => 'Insufficient payment. Total is ₱' . number_format($total, 2)]); exit; }

        $change = $paid - $total;
        $txnId = 'TXN-' . strtoupper(substr(uniqid(), -7));

        foreach ($cart as $item) {
            $pid = intval($item['product_id']);
            $qty_sold = intval($item['qty']);
            
            $prod = $productsToBuy[$pid];
            $pcode = $prod['product_code'];
            
            $new_stock = $prod['quantity'] - $qty_sold;

            mysqli_query($conn, "UPDATE products SET quantity = $new_stock WHERE id = $pid");
            
            $notes = "Sale $txnId";
            $admin = "Cashier";
            
            logJournal($conn, $pcode, 0, $qty_sold, $notes, $admin);
        }

        $receipt = ['id' => $txnId, 'date' => date('Y-m-d H:i:s'), 'items' => $cart, 'total' => $total, 'paid' => $paid, 'change' => $change];
        echo json_encode(['success' => true, 'receipt' => $receipt]); exit;
    }
    
    echo json_encode(['error' => 'Unknown endpoint']); exit;
}
?>