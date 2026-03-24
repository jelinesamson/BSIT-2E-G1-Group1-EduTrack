<?php
$PRODUCTS_FILE     = _DIR_ . '/../data/products.json';
$SALES_FILE        = _DIR_ . '/../data/sales.json';
$TRANSACTIONS_FILE = _DIR_ . '/../data/transactions.json';

if (!is_dir(_DIR_ . '/../data')) mkdir(_DIR_ . '/../data', 0755, true);

$defaultProducts = [
    ["id" => 1,  "name" => "Uniform",    "price" => 300, "stock" => 50],
    ["id" => 2,  "name" => "Book",       "price" => 150, "stock" => 80],
    ["id" => 3,  "name" => "ID Lace",    "price" => 50,  "stock" => 100],
    ["id" => 4,  "name" => "PE Shirt",   "price" => 250, "stock" => 40],
    ["id" => 5,  "name" => "Notebook",   "price" => 80,  "stock" => 120],
    ["id" => 6,  "name" => "Ballpen",    "price" => 15,  "stock" => 200],
    ["id" => 7,  "name" => "Folder",     "price" => 25,  "stock" => 90],
    ["id" => 8,  "name" => "School Bag", "price" => 850, "stock" => 20],
    ["id" => 9,  "name" => "Lab Gown",   "price" => 400, "stock" => 30],
    ["id" => 10, "name" => "Ruler",      "price" => 20,  "stock" => 150],
];

if (!file_exists($PRODUCTS_FILE))     file_put_contents($PRODUCTS_FILE, json_encode($defaultProducts, JSON_PRETTY_PRINT));
if (!file_exists($SALES_FILE))        file_put_contents($SALES_FILE, json_encode([], JSON_PRETTY_PRINT));
if (!file_exists($TRANSACTIONS_FILE)) file_put_contents($TRANSACTIONS_FILE, json_encode([], JSON_PRETTY_PRINT));

function loadJSON($f) { return json_decode(file_get_contents($f), true) ?? []; }
function saveJSON($f, $d) { file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT)); }


if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    if ($_GET['api'] === 'products') {
        echo json_encode(loadJSON($PRODUCTS_FILE)); exit;
    }

    if ($_GET['api'] === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $cart = $body['cart'] ?? [];
        $paid = floatval($body['paid'] ?? 0);

        if (empty($cart)) { echo json_encode(['error' => 'Cart is empty.']); exit; }

        $products = loadJSON($PRODUCTS_FILE);
        $total    = 0;

        foreach ($cart as $item) {
            $pid = intval($item['product_id']);
            $qty = intval($item['qty']);
            foreach ($products as &$p) {
                if ($p['id'] === $pid) {
                    if ($p['stock'] < $qty) {
                        echo json_encode(['error' => "Insufficient stock for {$p['name']}. Available: {$p['stock']}"]); exit;
                    }
                    $total += $p['price'] * $qty;
                    break;
                }
            } unset($p);
        }

        if ($paid < $total) { echo json_encode(['error' => 'Insufficient payment. Total is ₱' . number_format($total, 2)]); exit; }

        
        foreach ($cart as $item) {
            $pid = intval($item['product_id']); $qty = intval($item['qty']);
            foreach ($products as &$p) { if ($p['id'] === $pid) { $p['stock'] -= $qty; break; } } unset($p);
        }
        saveJSON($PRODUCTS_FILE, $products);

        
        $transactions = loadJSON($TRANSACTIONS_FILE);
        $txnId        = 'TXN-' . strtoupper(substr(uniqid(), -7));
        $receipt      = ['id' => $txnId, 'date' => date('Y-m-d H:i:s'), 'items' => $cart, 'total' => $total, 'paid' => $paid, 'change' => $paid - $total];
        $transactions[] = $receipt;
        saveJSON($TRANSACTIONS_FILE, $transactions);

        
        $sales = loadJSON($SALES_FILE);
        foreach ($cart as $item) {
            $sales[] = ['txn_id' => $txnId, 'date' => date('Y-m-d H:i:s'), 'product_id' => $item['product_id'], 'name' => $item['name'], 'qty' => $item['qty'], 'price' => $item['price'], 'subtotal' => $item['qty'] * $item['price']];
        }
        saveJSON($SALES_FILE, $sales);

        echo json_encode(['success' => true, 'receipt' => $receipt]); exit;
    }

    echo json_encode(['error' => 'Unknown endpoint']); exit;
}
?>
