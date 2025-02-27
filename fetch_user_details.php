<?php
session_start();
include('db_connection.php'); // Connect to database

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT rsbaNumber, firstName, lastName, homeAddress, contactNumber, profileImage FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, ...$user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();
?>
