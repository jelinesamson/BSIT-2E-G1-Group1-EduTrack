<?php
include "config.php";

if (isset($_POST['action'])) {
	if ($_POST['action'] == "store") {
		
	}
	
	if ($_POST['action'] == "update") {
		
	}
	
	if ($_POST['action'] == "drop") {
		
	}
	
	if ($_POST['action'] == "postOne") {
		$payload = json_decode($_POST['payload']);
		
		$statement = $conn->prepare("SELECT * FROM accounts where email = ?");
		$statement->bind_param("s", $payload->email);
		$statement->execute();
		$result = $statement->get_result();
		
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();

			if (password_verify($payload->password, $user['password'])) {
				
				echo json_encode([
					"status" => "success",
					"message" => "Succesfully login"
				]);
			} else {
				echo json_encode([
					"status" => "failed",
					"message" => "Invalid password"
				]);
			}
		} else {
			echo json_encode([
				"status" => "failed",
				"message" => "Account does not exists"
			]);
		}
	}
}


if (isset($_GET['action'])) {
	if ($_GET['action'] == "get") {
		
	}
	
	if ($_GET['action'] == "getOne") {
		
	}
	
	
}