<?php
include('db_connection.php');

header('Content-Type: application/json');
session_start(); // Ensure session is started at the beginning of your script


$rsba_number = $_SESSION['rsba_number']; // Ensure session is started and the variable is set
$sql = "SELECT * FROM activities WHERE rsba_number = ? ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL preparation failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $rsba_number);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'SQL execution failed: ' . $stmt->error]);
    exit;
}
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

echo json_encode(['activities' => $activities]);

$stmt->close();
$conn->close();
?>
