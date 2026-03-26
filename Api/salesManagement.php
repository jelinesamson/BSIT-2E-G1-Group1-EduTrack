<?php
// Api/salesManagement.php

include('config.php');

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
        $query = "SELECT id, name, price, qty AS stock FROM product WHERE qty > 0";
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

            $res = mysqli_query($conn, "SELECT name, price, qty FROM product WHERE id = $pid");
            $prod = mysqli_fetch_assoc($res);

            if (!$prod || $prod['qty'] < $qty_needed) {
                echo json_encode(['error' => "Insufficient stock for " . ($prod['name'] ?? 'Unknown Item')]); exit;
            }
            $productsToBuy[$pid] = $prod;
            $total += ($prod['price'] * $qty_needed);
        }

        if ($paid < $total) { echo json_encode(['error' => 'Insufficient payment. Total is ₱' . number_format($total, 2)]); exit; }

        $change = $paid - $total;
        $vat = $total * 0.12; 
        $txnId = 'TXN-' . strtoupper(substr(uniqid(), -7));

        $stmt = $conn->prepare("INSERT INTO product_journal (prod_id, qty, total_qty, notes, status, unit_price, total_price, paid, `change`, vat_amount, created_by, date_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        foreach ($cart as $item) {
            $pid = intval($item['product_id']);
            $qty_sold = intval($item['qty']);
            
            $prod = $productsToBuy[$pid];
            
            $unit_price = $prod['price'];
            $total_price = $unit_price * $qty_sold;
            $new_stock = $prod['qty'] - $qty_sold;

            mysqli_query($conn, "UPDATE product SET qty = $new_stock WHERE id = $pid");
            
            $notes = "Sale $txnId";
            $status = "sales";
            $admin = "Cashier";
            
            $stmt->bind_param("iiissddddds", $pid, $qty_sold, $new_stock, $notes, $status, $unit_price, $total_price, $paid, $change, $vat, $admin);
            $stmt->execute();
        }

        $receipt = ['id' => $txnId, 'date' => date('Y-m-d H:i:s'), 'items' => $cart, 'total' => $total, 'paid' => $paid, 'change' => $change];
        echo json_encode(['success' => true, 'receipt' => $receipt]); exit;
    }
    
    echo json_encode(['error' => 'Unknown endpoint']); exit;
}
?>