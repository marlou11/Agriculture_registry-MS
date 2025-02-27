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

// SQL query
$sql = "SELECT * FROM batches";
$result = $conn->query($sql);
$batches = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $conn->error]);
    exit();
}

// Send JSON response with only batch data
header('Content-Type: application/json');
echo json_encode($batches, JSON_PRETTY_PRINT);

$conn->close();
?>
