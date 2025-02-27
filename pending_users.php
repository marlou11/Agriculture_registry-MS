<?php
require 'db_connection.php';

// Fetch users with pending status
$sql = "SELECT u.*, lo.type, lo.size, lo.location 
        FROM users u 
        LEFT JOIN land_ownership lo ON u.id = lo.user_id
        WHERE u.status = 'pending'";

$result = $conn->query($sql);
$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Return as JSON
echo json_encode($users);

// Close connection
$conn->close();
?>
