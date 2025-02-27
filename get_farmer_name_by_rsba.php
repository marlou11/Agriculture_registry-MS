<?php
// Get batch data by ID
require_once 'db_connection.php';

$batch_id = $_GET['batch_id'];

$query = "SELECT * FROM batches WHERE batch_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Batch not found.']);
}
?>
