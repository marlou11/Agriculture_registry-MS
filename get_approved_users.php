<?php
// Database connection
include 'db_connection.php';

$rsbaNumber = $_GET['rsbaNumber'];

// Prepare the statement to prevent SQL injection
$stmt = $conn->prepare("SELECT rsba_number, name FROM farmers WHERE rsba_number = ? AND status = 'approved'");
$stmt->bind_param("s", $rsbaNumber);
$stmt->execute();
$result = $stmt->get_result();

$farmers = [];
while ($row = $result->fetch_assoc()) {
    $farmers[] = $row;
}

echo json_encode($farmers);

$stmt->close();
$conn->close();
?>
