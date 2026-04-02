<?php
include "config.php";
if (isset($_POST['action'])) {
    if ($_POST['action'] == "store") {
		$payload = json_decode($_POST['payload']);

		if (!$payload) {
        echo json_encode([
            "status" => "failed",
            "message" => "Invalid data"
        ]);
        exit;
    	}
		
		$hashedPassword = password_hash($payload->regpassword, PASSWORD_DEFAULT);
		
		$statement = $conn->prepare("INSERT INTO accounts (firstName, lastName, email, password) VALUES (?,?,?,?)");
		$statement->bind_param("ssss", 
			$payload->firstName, 
			$payload->lastName, 
			$payload->regemail, 
			$hashedPassword
		);
		
		if ($statement->execute()) {
			echo json_encode([
				"status" => "success",
				"message" => "Successfully Inserted"
			]);
		} else {
			echo json_encode([
				"status" => "failed",
				"message" => "Failed to insert"
			]);
		}
	}
	
	if ($_POST['action'] == "update") {
		
	}
	
	if ($_POST['action'] == "drop") {
		
	}
}

?>