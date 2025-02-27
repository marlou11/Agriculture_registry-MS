<?php
require 'db_connection.php'; // Include the database connection

// Get the input data
$inputData = json_decode(file_get_contents('php://input'), true);

$firstName = $inputData['firstName'];
$middleInitial = $inputData['middleInitial'];
$lastName = $inputData['lastName'];
$rsbaNumber = $inputData['rsbaNumber'];
$contactNumber = $inputData['contactNumber'];
$barangay = $inputData['barangay'];
$homeAddress = $inputData['homeAddress'];
$farmLocation = $inputData['farmLocation'];
$password = password_hash($inputData['password'], PASSWORD_BCRYPT);

$inputData = json_decode(file_get_contents('php://input'), true);

if (!$inputData || !is_array($inputData)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$query = "INSERT INTO users (first_name, middle_initial, last_name, rsba_number, contact_number, barangay, home_address, farm_location, password, status)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($query);
$stmt->bind_param('sssssssss', $firstName, $middleInitial, $lastName, $rsbaNumber, $contactNumber, $barangay, $homeAddress, $farmLocation, $password);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
