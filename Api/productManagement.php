<?php
    require_once "config.php";
    require_once "inventoryManagement.php";
    if (isset($_POST['action'])) {
   if ($_POST['action'] == "store") {

    $payload = json_decode($_POST['payload']);
    $account_id = $_SESSION['account_id'] ?? null;

    if (!$account_id) {
        echo json_encode([
            "status" => "error",
            "message" => "User not logged in"
        ]);
        exit;
    }

    $stmt = $conn->prepare("CALL add_product(?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("ssssiidsi",
        $payload->code,
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->quantity,
        $payload->incoming_qty,
        $payload->price,
        $payload->status,
        $account_id
    );

    if ($stmt->execute()) {

        // IMPORTANT for stored procedure
        while ($conn->more_results() && $conn->next_result()) {}

        echo json_encode([
            "status" => "success",
            "message" => "Product added Successfully"
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
    exit;
}

      if ($_POST['action'] == "get") {
    $result = $conn->query("SELECT * FROM v_products");

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row['product_code'],
            $row['product_type'],
            $row['size'],
            $row['department'],
            $row['quantity'],
            $row['incoming_qty'], 
            $row['price'],
            $row['status'],
            '<button class="btn btn-sm btn-warning" onclick="update(this)">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteRow(this)">Delete</button>
            <button class="btn btn-sm btn-success" onclick="receiveStock(this)">Receive</button>'
        ];
    }

    echo json_encode([
        "data" => $data
    ]);
    exit;
}

if ($_POST['action'] == "update") {

    $payload = json_decode($_POST['payload']);
    $account_id = $_SESSION['account_id'] ?? null;

    // GET product info (for logging only)
    $stmtId = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ?");
    $stmtId->bind_param("s", $payload->code);
    $stmtId->execute();
    $result = $stmtId->get_result()->fetch_assoc();

    $product_id = $result['product_id'] ?? null;
    $quantity = $result['quantity'] ?? 0;

    if (!$product_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Product not found"
        ]);
        exit;
    }

    // CALL PROCEDURE
    $stmt = $conn->prepare("CALL update_product(?,?,?,?,?,?,?)");

    $stmt->bind_param("sssssii",
        $payload->code,
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->incoming_qty,
        $payload->price,
        $account_id
    );

    if ($stmt->execute()) {

        // IMPORTANT for stored procedures
        while ($conn->more_results() && $conn->next_result()) {}

        // ONLY ONE LOG HERE (PHP SIDE)
        logJournal(
            $conn,
            $product_id,
            $payload->incoming_qty,
            0,
            "Edit",
            $quantity,
            $account_id
        );

        echo json_encode([
            "status" => "success",
            "message" => "Updated successfully"
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
    exit;
}
	
if ($_POST['action'] == "drop") {

    $code = $_POST['code'];
    $account_id = $_SESSION['account_id'] ?? null;

    if (!$account_id) {
        echo json_encode([
            "status" => "error",
            "message" => "User not logged in"
        ]);
        exit;
    }

    $stmt = $conn->prepare("CALL delete_product(?,?)");

    $stmt->bind_param("si",
        $code,
        $account_id
    );

    if ($stmt->execute()) {

        // IMPORTANT (fix JSON / DataTables error)
        while ($conn->more_results() && $conn->next_result()) {}

        echo json_encode([
            "status" => "success",
            "message" => "Product archived successfully"
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
}
if ($_POST['action'] == "receive") {

    $code = $_POST['code'];
    $account_id = $_SESSION['account_id'] ?? null;

    if (!$account_id) {
        echo json_encode([
            "status" => "error",
            "message" => "User not logged in"
        ]);
        exit;
    }

    $stmt = $conn->prepare("CALL receive_product(?,?)");

    $stmt->bind_param("si",
        $code,
        $account_id
    );

    if ($stmt->execute()) {

        // 🔥 FIX DataTables / JSON issue
        while ($conn->more_results() && $conn->next_result()) {}

        echo json_encode([
            "status" => "success",
            "message" => "Stock received successfully"
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
}
    }
?>