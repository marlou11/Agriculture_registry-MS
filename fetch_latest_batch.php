<?php
include 'db_connection.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        $batchName = $conn->real_escape_string(filter_input(INPUT_GET, 'batchName', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        if (!$batchName) {
            throw new Exception("Batch name is required.");
        }

        $sql = "SELECT MAX(batchNumber) as latestBatch FROM batches WHERE batchName = '$batchName'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $latestBatch = $row["latestBatch"];
            header('Content-Type: application/json');
            echo json_encode(['latestBatch' => $latestBatch]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['latestBatch' => 0]); // Return 0 if no batches found for that name

        }
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        error_log("Error in fetch_latest_batch.php: " . $e->getMessage()); //Log the error
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>