<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include database connection file

$sql = "SELECT * FROM users WHERE status = 'approved'";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
$conn->close();
?>
