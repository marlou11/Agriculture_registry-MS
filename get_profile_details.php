<?php
// Enable CORS and set content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Mock session or authentication for logged-in user
// Replace this with your session authentication logic
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Database connection (replace with your credentials)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'arms_db';

try {
    // Create a new database connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get the logged-in user's ID from the session
    $userId = $_SESSION['user_id'];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("
        SELECT fname, mname, lname, extension, sex, dob, address, barangay, mobile, ownership_type
        FROM farmers
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch data as an associative array
        $data = $result->fetch_assoc();
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        // No record found for the user
        echo json_encode(["success" => false, "message" => "No profile found for the user."]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Catch and return any errors
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    exit();
}
?>
