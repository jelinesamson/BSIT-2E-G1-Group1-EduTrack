<?php
// Inventory Management API
// Handles journal logging and retrieval for inventory tracking
require_once "config.php";

/**
 * Logs a journal entry into product_journal.
 * Called by teammates (Product Management, Sales Management) whenever stock changes.
 */
function logJournal($conn, $product_code, $action_incoming_qty, $action_sales, $notes, $created_by) {
    // Step 1: Get the current stock from product table as a snapshot
    $stmt = $conn->prepare("SELECT quantity FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $product_code);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $quantity = $result['quantity'] ?? 0;

    // Step 2: Insert the journal entry with the snapshot of current stock
    $stmt = $conn->prepare(
        "INSERT INTO product_journal (product_code, incoming_qty, quantity, sales, notes, created_by)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("siiiss", $product_code, $action_incoming_qty, $quantity, $action_sales, $notes, $created_by);
    $stmt->execute();
}


// ─── API Actions (GET requests) ──────────────────────────────────────────────

if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // Returns all products for the filter dropdown
    if ($_GET['action'] == "getProducts") {
        $stmt = $conn->prepare("SELECT product_code AS id, CONCAT(product_code, ' - ', product_type) AS name FROM products ORDER BY name ASC");
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
        $prod_id = $_GET['prod_id'] ?? '';

        if (empty($prod_id)) {
            echo json_encode(["status" => "error", "message" => "Invalid product code."]);
            exit;
        }

        $date_from = $_GET['date_from'] ?? '';
        $date_to   = $_GET['date_to'] ?? '';

        // Build query with optional date filter
        if (!empty($date_from) && !empty($date_to)) {
            // Validate date formats
            $df = DateTime::createFromFormat('Y-m-d', $date_from);
            $dt = DateTime::createFromFormat('Y-m-d', $date_to);

            if (!$df || !$dt) {
                echo json_encode(["status" => "error", "message" => "Invalid date format."]);
                exit;
            }

            // Append time to cover the full day range
            $date_from_full = $date_from . ' 00:00:00';
            $date_to_full   = $date_to . ' 23:59:59';

            $stmt = $conn->prepare(
                "SELECT pj.id, CONCAT(p.product_code, ' - ', p.product_type) AS prod_name, pj.incoming_qty, pj.sales, pj.notes, pj.quantity AS total_qty, pj.date_time, pj.created_by
                 FROM product_journal pj
                 JOIN products p ON p.product_code = pj.product_code
                 WHERE pj.product_code = ?
                   AND pj.date_time BETWEEN ? AND ?
                 ORDER BY pj.date_time ASC"
            );
            $stmt->bind_param("sss", $prod_id, $date_from_full, $date_to_full);
        } else {
            // No date filter — return all entries for this product
            $stmt = $conn->prepare(
                "SELECT pj.id, CONCAT(p.product_code, ' - ', p.product_type) AS prod_name, pj.incoming_qty, pj.sales, pj.notes, pj.quantity AS total_qty, pj.date_time, pj.created_by
                 FROM product_journal pj
                 JOIN products p ON p.product_code = pj.product_code
                 WHERE pj.product_code = ?
                 ORDER BY pj.date_time ASC"
            );
            $stmt->bind_param("s", $prod_id);
        }

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
