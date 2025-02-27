<?php
// Database connection
include('db_connection.php');

// Get the POSTed JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is valid
if (isset($data['firstName'], $data['middleInitial'], $data['lastName'], $data['rsbaNumber'], $data['contactNumber'], $data['barangay'], $data['homeAddress'], $data['farmSize'], $data['farmUnit'], $data['farmLocation'], $data['password'])) {

    // Sanitize input data
    $firstName = $conn->real_escape_string($data['firstName']);
    $middleInitial = $conn->real_escape_string($data['middleInitial']);
    $lastName = $conn->real_escape_string($data['lastName']);
    $rsbaNumber = $conn->real_escape_string($data['rsbaNumber']);
    $contactNumber = $conn->real_escape_string($data['contactNumber']);
    $barangay = $conn->real_escape_string($data['barangay']);
    $homeAddress = $conn->real_escape_string($data['homeAddress']);
    $farmSize = $conn->real_escape_string($data['farmSize']);
    $farmUnit = $conn->real_escape_string($data['farmUnit']);
    $farmLocation = $conn->real_escape_string($data['farmLocation']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash password for security

    // Check if RSBA number already exists in the database
    $checkSql = "SELECT * FROM users WHERE rsba_number = '$rsbaNumber'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // RSBA number already exists, return error
        echo json_encode(["success" => false, "message" => "RSBA number already exists. Please use a different RSBA number."]);
    } else {
        // RSBA number is unique, insert new user data into the database
        $sql = "INSERT INTO users (first_name, middle_initial, last_name, rsba_number, contact_number, barangay, home_address, farm_size, farm_unit, farm_location, password)
                VALUES ('$firstName', '$middleInitial', '$lastName', '$rsbaNumber', '$contactNumber', '$barangay', '$homeAddress', '$farmSize', '$farmUnit', '$farmLocation', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Respond with a success message
            echo json_encode(["success" => true, "message" => "Registration successful."]);
        } else {
            // Respond with an error message
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }

} else {
    // Respond with a missing data error
    echo json_encode(["success" => false, "message" => "Missing data"]);
}
?>
