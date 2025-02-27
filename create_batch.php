<?php
include 'db_connection.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Input sanitization and validation
        $batchName = $conn->real_escape_string(filter_input(INPUT_POST, 'batchName', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $batchNumber = filter_input(INPUT_POST, 'batchNumber', FILTER_SANITIZE_NUMBER_INT);
        $totalQuantity = filter_input(INPUT_POST, 'totalQuantity', FILTER_SANITIZE_NUMBER_INT);
        // Validate inputs
        if (!$batchName || !$batchNumber || !$totalQuantity || $totalQuantity <= 0) {
            throw new Exception("Invalid batch data. Please fill in all fields correctly.");
        }
        // SQL query (vulnerable to SQL injection without prepared statements - use PDO for better security!)
        $sql = "INSERT INTO batches (batchName, batchNumber, totalQuantity) VALUES ('$batchName', $batchNumber, $totalQuantity)";
        if ($conn->query($sql) === TRUE) {
            $batchId = $conn->insert_id;
            // Success response
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'batchId' => $batchId]);
        } else {
            // Error response
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
        }
    } catch (Exception $e) {
        // Error handling
        http_response_code(500); // Set appropriate HTTP status code
        header('Content-Type: application/json');
        error_log("Error in create_batch.php: " . $e->getMessage()); // Log the error for debugging
        echo json_encode(['success' => false, 'message' => 'Error creating batch: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
$conn->close();
?>