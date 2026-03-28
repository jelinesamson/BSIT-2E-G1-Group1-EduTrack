<?php
    require_once "config.php";
    require_once "inventoryManagement.php";
    if (isset($_POST['action'])) {
    if ($_POST['action'] == "store") {
		$payload = json_decode($_POST['payload']);
		$statement = $conn->prepare("
INSERT INTO products 
(product_code, product_type, size, department, quantity,incoming_qty, price, status) 
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
		if ($statement->execute()) {
            logJournal($conn, $payload->code, $payload->incoming_qty, 0, "Add", "Admin");
			echo json_encode([
				"status" => "success",
				"message" => "Product added Successfully"
			]);
		} else {
			echo json_encode([
				"status" => "failed",
				"message" => "Failed to insert"
			]);
		}
	}

      if ($_POST['action'] == "get") {
        $result = $conn->query("SELECT * FROM products");

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
if ($_POST['action'] == "receive") {
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT incoming_qty, quantity FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $incoming = (int)$row['incoming_qty'];
    $qty = (int)$row['quantity'];

    if ($incoming <= 0) {
        echo json_encode([
            "status" => "error",
            "message" => "No incoming stock"
        ]);
        exit;
    }

    $newQty = $qty + $incoming;

    $update = $conn->prepare("
        UPDATE products 
        SET quantity = ?, incoming_qty = 0, status = 'Successfully'
        WHERE product_code = ?
    ");
    $update->bind_param("is", $newQty, $code);
    $update->execute();

    logJournal($conn, $code, $incoming, 0, "Receive", "Admin");

    echo json_encode([
        "status" => "success",
        "message" => "Stock moved to current quantity"
    ]);
}

if ($_POST['action'] == "update") {
    $payload = json_decode($_POST['payload']);

    $status = ($payload->incoming_qty > 0) ? "On the Way" : "Successfully";

    $stmt = $conn->prepare("
        UPDATE products 
        SET product_type = ?, 
            size = ?, 
            department = ?, 
            incoming_qty = ?, 
            price = ?, 
            status = ?
        WHERE product_code = ?
    ");

    $stmt->bind_param("sssisss",
        $payload->product_type,
        $payload->size,
        $payload->department,
        $payload->incoming_qty,
        $payload->price,
        $status,
        $payload->code
    );

    if ($stmt->execute()) {
        logJournal($conn, $payload->code, $payload->incoming_qty, 0, "Edit", "Admin");
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

    $stmt = $conn->prepare("DELETE FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $code);

    if ($stmt->execute()) {
        logJournal($conn, $code, 0, 0, "Delete", "Admin");
        echo json_encode([
            "status" => "success",
            "message" => "Product deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Delete failed"
        ]);
    }
	}
}
?>