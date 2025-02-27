<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "arms_db";

// Use try-catch for better error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(["success" => false, "error" => $e->getMessage()]));
}
?>
