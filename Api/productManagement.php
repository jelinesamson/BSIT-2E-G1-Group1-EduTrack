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

    $statement = $conn->prepare("
        INSERT INTO products 
        (product_code, product_type, size, department, quantity, incoming_qty, price, status) 
        VALUES (?,?,?,?,?,?,?,?)
    ");

    $statement->bind_param("ssssiids",
        $payload->code,
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->quantity,
        $payload->incoming_qty,
        $payload->price,
        $payload->status
    );

    $quantity = $payload->quantity;

    if ($statement->execute()) {

        $product_id = $conn->insert_id;

        logJournal($conn, $product_id, $payload->incoming_qty, 0, "Add", $quantity, $account_id);

        echo json_encode([
            "status" => "success",
            "message" => "Product added Successfully"
        ]);
    }
}

      if ($_POST['action'] == "get") {
    $result = $conn->query("SELECT * FROM products WHERE is_deleted = 0");

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
}

if ($_POST['action'] == "update") {
    $payload = json_decode($_POST['payload']);
    $account_id = $_SESSION['account_id'] ?? null;

    $status = ($payload->incoming_qty > 0) ? "On the Way" : "Successfully";

    // 🔹 First, get the product_id from product_code
    $stmtId = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ?");
    $stmtId->bind_param("s", $payload->code);
    $stmtId->execute();
    $result = $stmtId->get_result()->fetch_assoc();
    $product_id = $result['product_id'] ?? null;
    $quantity =  $result['quantity'] ?? null;

    if (!$product_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Product not found"
        ]);
        exit;
    }

    // 🔹 Update product using product_id
    $stmt = $conn->prepare("
        UPDATE products 
        SET product_type = ?, 
            size = ?, 
            department = ?, 
            incoming_qty = ?, 
            price = ?, 
            status = ?
        WHERE product_id = ?
    ");

    $stmt->bind_param("sssissi",
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->incoming_qty,
        $payload->price,
        $status,
        $product_id
    );

    if ($stmt->execute()) {
        // 🔹 Log journal with product_id
        logJournal($conn, $product_id, $payload->incoming_qty, 0, "Edit", $quantity, $account_id);

        echo json_encode([
            "status" => "success",
            "message" => "Updated successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Update failed"
        ]);
    }
}
	
	if ($_POST['action'] == "drop") {
    $code = $_POST['code'];
    $account_id = $_SESSION['account_id'] ?? null;
    // get product_id
    $stmtId = $conn->prepare("SELECT product_id, quantity FROM products WHERE product_code = ?");
    $stmtId->bind_param("s", $code);
    $stmtId->execute();
    $result = $stmtId->get_result()->fetch_assoc();
    $product_id = $result['product_id'] ?? null;
    $quantity = $result['quantity'] ?? null;

    if (!$product_id) {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
        exit;
    }

    // 🔥 SOFT DELETE (not real delete)
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {

        // log journal
        logJournal($conn, $product_id, 0, 0, "Delete", $quantity, $account_id);

        echo json_encode([
            "status" => "success",
            "message" => "Product archived successfully"
        ]);
    }
}
if ($_POST['action'] == "receive") {
    $code = $_POST['code'];
    $account_id = $_SESSION['account_id'] ?? null;

    // 🔹 Get product_id, incoming_qty, quantity
    $stmt = $conn->prepare("SELECT product_id, incoming_qty, quantity FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $product_id = $row['product_id'];
    $incoming = (int)$row['incoming_qty'];
    $qty = (int)$row['quantity'];

    if ($incoming <= 0) {
        echo json_encode([
            "status" => "error",
            "message" => "No incoming stock"
        ]);
        exit;
    }

    // 🔹 Move incoming → quantity
    $newQty = $qty + $incoming;

    $update = $conn->prepare("
        UPDATE products 
        SET quantity = ?, incoming_qty = 0, status = 'Successfully'
        WHERE product_id = ?
    ");
    $update->bind_param("ii", $newQty, $product_id);
    $update->execute();

    // 🔥 Log the actual received stock
    logJournal($conn, $product_id, 0, 0, "Receive", $newQty, $account_id);

    echo json_encode([
        "status" => "success",
        "message" => "Stock moved to current quantity"
    ]);
}
}
?>