<?php
// Inventory Management API
// Handles journal logging and retrieval for inventory tracking
include "config.php";

/**
 * Logs a journal entry into product_journal.
 * Called by teammates (Product Management, Sales Management) whenever stock changes.
 *
 * @param mysqli $conn      Database connection
 * @param int    $prod_id   Product ID
 * @param int    $qty       Quantity involved in the action
 * @param string $notes     Action type: 'Add', 'Edit', 'Delete', or 'Sale'
 * @param string $created_by  Name of the user performing the action
 */
function logJournal($conn, $prod_id, $qty, $notes, $created_by) {
    // Step 1: Get the current stock from product table as a snapshot
    $stmt = $conn->prepare("SELECT qty FROM product WHERE id = ?");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_qty = $result['qty'] ?? 0;

    // Step 2: Insert the journal entry with the snapshot of current stock
    $stmt = $conn->prepare(
        "INSERT INTO product_journal (prod_id, qty, notes, total_qty, created_by)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("iisis", $prod_id, $qty, $notes, $total_qty, $created_by);
    $stmt->execute();
}


// ─── API Actions (GET requests) ──────────────────────────────────────────────

if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // Returns all products for the filter dropdown
    if ($_GET['action'] == "getProducts") {
        $stmt = $conn->prepare("SELECT id, name FROM product ORDER BY name ASC");
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
        $prod_id = intval($_GET['prod_id'] ?? 0);

        if ($prod_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
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
                "SELECT pj.id, p.name AS prod_name, pj.qty, pj.notes, pj.total_qty, pj.date_time, pj.created_by
                 FROM product_journal pj
                 JOIN product p ON p.id = pj.prod_id
                 WHERE pj.prod_id = ?
                   AND pj.date_time BETWEEN ? AND ?
                 ORDER BY pj.date_time ASC"
            );
            $stmt->bind_param("iss", $prod_id, $date_from_full, $date_to_full);
        } else {
            // No date filter — return all entries for this product
            $stmt = $conn->prepare(
                "SELECT pj.id, p.name AS prod_name, pj.qty, pj.notes, pj.total_qty, pj.date_time, pj.created_by
                 FROM product_journal pj
                 JOIN product p ON p.id = pj.prod_id
                 WHERE pj.prod_id = ?
                 ORDER BY pj.date_time ASC"
            );
            $stmt->bind_param("i", $prod_id);
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

    // ██████████████████████████████████████████████████████████████████████████
    // ██  TEST ACTIONS — REMOVE BEFORE GIT PUSH                              ██
    // ██  These simulate logJournal() calls so you can see the module work.   ██
    // ██  Hit these URLs in your browser to create test data:                 ██
    // ██                                                                      ██
    // ██  1) /BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php?action=testSetup  ██
    // ██     → Creates a test product "Uniform 001" with qty=5                ██
    // ██                                                                      ██
    // ██  2) /BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php?action=testAdd    ██
    // ██     → Logs an "Add" entry (qty=5) for product id=1                   ██
    // ██                                                                      ██
    // ██  3) /BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php?action=testEdit   ██
    // ██     → Updates product qty to 10, logs an "Edit" entry (qty=10)       ██
    // ██                                                                      ██
    // ██  4) /BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php?action=testSale   ██
    // ██     → Reduces product qty by 2, logs a "Sale" entry (qty=2)          ██
    // ██                                                                      ██
    // ██  5) /BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php?action=testDelete ██
    // ██     → Logs a "Delete" entry (qty=0) for product id=1                 ██
    // ██                                                                      ██
    // ██  Run them in order: testSetup → testAdd → testEdit → testSale        ██
    // ██  Then open the Inventory Management page and search for the product.  ██
    // ██████████████████████████████████████████████████████████████████████████

    // ██ TEST: Create a test product so there's something to log against ██
    if ($_GET['action'] == "testSetup") {
        $stmt = $conn->prepare("INSERT INTO product (name, type, size, dept, price, qty) VALUES ('Uniform 001', 'Uniform', 'Medium', 'CICT', 350.00, 5)");
        $stmt->execute();
        $new_id = $conn->insert_id;
        echo json_encode(["status" => "success", "message" => "TEST: Product created with id=$new_id and qty=5"]);
        exit;
    }

    // ██ TEST: Simulate Product Management adding stock (like after INSERT) ██
    if ($_GET['action'] == "testAdd") {
        // This is what your teammate would call after adding a new product:
        // include("inventoryManagement.php");
        // logJournal($conn, $product_id, $incoming_qty, "Add", $created_by);
        logJournal($conn, 1, 5, "Add", "Admin");
        echo json_encode(["status" => "success", "message" => "TEST: logJournal called with notes=Add, qty=5. Product stock snapshot saved."]);
        exit;
    }

    // ██ TEST: Simulate Product Management editing stock (like after UPDATE) ██
    if ($_GET['action'] == "testEdit") {
        // First update the product qty (teammate would do this in their code)
        $conn->prepare("UPDATE product SET qty = 10 WHERE id = 1")->execute();
        // Then log the journal entry:
        // include("inventoryManagement.php");
        // logJournal($conn, $prod_id, $new_qty, "Edit", $created_by);
        logJournal($conn, 1, 10, "Edit", "Admin");
        echo json_encode(["status" => "success", "message" => "TEST: Product qty updated to 10. logJournal called with notes=Edit, qty=10."]);
        exit;
    }

    // ██ TEST: Simulate Sales Management checkout (like after successful sale) ██
    if ($_GET['action'] == "testSale") {
        // First reduce stock (teammate would do this in their checkout code)
        $conn->prepare("UPDATE product SET qty = qty - 2 WHERE id = 1")->execute();
        // Then log the journal entry:
        // include("inventoryManagement.php");
        // logJournal($conn, $prod_id, $qty_sold, "Sale", $served_by);
        logJournal($conn, 1, 2, "Sale", "Admin");
        echo json_encode(["status" => "success", "message" => "TEST: 2 units sold. logJournal called with notes=Sale, qty=2."]);
        exit;
    }

    // ██ TEST: Simulate Product Management deleting a product ██
    if ($_GET['action'] == "testDelete") {
        // Log before the actual delete (so the FK still works):
        // include("inventoryManagement.php");
        // logJournal($conn, $prod_id, 0, "Delete", $created_by);
        logJournal($conn, 1, 0, "Delete", "Admin");
        echo json_encode(["status" => "success", "message" => "TEST: logJournal called with notes=Delete, qty=0."]);
        exit;
    }

    // ██████████████████████████████████████████████████████████████████████████
    // ██  END OF TEST ACTIONS — DELETE EVERYTHING BETWEEN THE ██ BANNERS     ██
    // ██████████████████████████████████████████████████████████████████████████

    // Fallback for unknown actions
    echo json_encode(["status" => "error", "message" => "Unknown action."]);
    exit;
}
?>
