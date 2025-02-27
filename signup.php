<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize user inputs (add error handling)
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $middleInitial = filter_input(INPUT_POST, 'middleInitial', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $rsbaNumber = filter_input(INPUT_POST, 'rsbaNumber', FILTER_SANITIZE_STRING);
    $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
    $barangay = filter_input(INPUT_POST, 'barangay', FILTER_SANITIZE_STRING);
    $homeAddress = filter_input(INPUT_POST, 'homeAddress', FILTER_SANITIZE_STRING);
    $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);

    if (!$firstName || !$lastName || !$rsbaNumber || !$contactNumber || !$barangay || !$password) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Insert user data into users table
        $stmt = $conn->prepare("INSERT INTO users (firstName, middleInitial, lastName, rsbaNumber, contactNumber, barangay, homeAddress, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $middleInitial, $lastName, $rsbaNumber, $contactNumber, $barangay, $homeAddress, $password);
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();

        // Handle land ownership details (improved error handling)
        if (isset($_POST['landOwnership'])) {
            $landOwnershipData = json_decode($_POST['landOwnership'], true);
            if (is_array($landOwnershipData)) {
                $stmt = $conn->prepare("INSERT INTO land_ownership (user_id, type, size, location) VALUES (?, ?, ?, ?)");
                foreach ($landOwnershipData as $land) {
                    $type = trim($land['type']);
                    $size = (float)$land['size'];
                    $location = trim($land['location']);
                    $stmt->bind_param("isds", $userId, $type, $size, $location);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert land ownership: " . $stmt->error);
                    }
                }
                $stmt->close();
            } else {
                throw new Exception("Invalid land ownership data format.");
            }
        }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "User registered successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>