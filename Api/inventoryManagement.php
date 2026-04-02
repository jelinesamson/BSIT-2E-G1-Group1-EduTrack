<?php
// Inventory Management API
// Handles journal logging and retrieval for inventory tracking
require_once "config.php";

/**
 * Logs a journal entry into product_journal.
 * Called by teammates (Product Management, Sales Management) whenever stock changes.
 */
function logJournal($conn, $product_id, $incoming_qty, $sales, $notes, $journal_qty, $account_id) {
    $product_id = intval($product_id);
    $incoming_qty = intval($incoming_qty);
    $sales = intval($sales);
    $journal_qty = intval($journal_qty);
    $account_id = intval($account_id);
    $notes = mysqli_real_escape_string($conn, $notes);

    $stmt = mysqli_query($conn, "
        INSERT INTO product_journal
        (product_id, incoming_quantity, sales, notes, journal_qty, account_id, date_time)
        VALUES
        ($product_id, $incoming_qty, $sales, '$notes', $journal_qty, $account_id, NOW())
    ");

    if (!$stmt) {
        echo json_encode(['error' => 'Journal insert failed: ' . mysqli_error($conn)]);
        exit;
    }
}

// ─── API Actions (GET requests) ──────────────────────────────────────────────

if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // Returns all products for the filter dropdown
    if ($_GET['action'] == "getProducts") {

    $stmt = $conn->prepare("
    SELECT DISTINCT p.product_id AS id, p.product_code AS name
    FROM products p
    INNER JOIN product_journal j ON p.product_id = j.product_id
    ORDER BY p.product_code ASC
");
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $products]);
    exit;
}

    // Returns journal entries for a specific product, with optional date range
 if ($_GET['action'] == "getJournal") {
    $prod_id = trim($_GET['prod_id'] ?? '');

    if (empty($prod_id)) {
        echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
        exit;
    }

    $date_from = $_GET['date_from'] ?? '';
    $date_to   = $_GET['date_to'] ?? '';

    // Build query with optional date filter
    $params = [$prod_id];
    $types = "i"; // product_id is integer

    $date_filter = "";
    if (!empty($date_from) && !empty($date_to)) {
        $df = DateTime::createFromFormat('Y-m-d', $date_from);
        $dt = DateTime::createFromFormat('Y-m-d', $date_to);

        if (!$df || !$dt) {
            echo json_encode(["status" => "error", "message" => "Invalid date format."]);
            exit;
        }

        $date_from_full = $date_from . ' 00:00:00';
        $date_to_full   = $date_to . ' 23:59:59';
        $date_filter = " AND pj.date_time BETWEEN ? AND ?";
        $params[] = $date_from_full;
        $params[] = $date_to_full;
        $types .= "ss";
    }

        $query = "
                SELECT pj.journal_id,
                    CONCAT(p.product_code, ' - ', p.product_type) AS prod_name,
                    pj.notes,
                    pj.incoming_quantity,
                    pj.sales,
                    pj.journal_qty AS total_qty,
                    pj.date_time,
                    a.firstName AS account_name
               FROM product_journal pj
                LEFT JOIN products p ON p.product_id = pj.product_id
                LEFT JOIN accounts a ON a.account_id = pj.account_id
                WHERE pj.product_id = ?
                $date_filter
                ORDER BY pj.date_time ASC
            ";

    $stmt = $conn->prepare($query);

    // Dynamically bind parameters
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $entries]);
    exit;
}



    // Fallback for unknown actions
    echo json_encode(["status" => "error", "message" => "Unknown action."]);
    exit;
}
