<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to get user details including barangay
$query = "SELECT rsbaNumber, firstName, lastName, homeAddress, contactNumber, barangay, profileImage FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Query to get land ownership details
    $land_query = "SELECT type, size, location FROM land_ownership WHERE user_id = ?";
    $land_stmt = $conn->prepare($land_query);
    $land_stmt->bind_param("i", $user_id);
    $land_stmt->execute();
    $land_result = $land_stmt->get_result();

    if ($land_result->num_rows > 0) {
        $land = $land_result->fetch_assoc();
        $user['landType'] = $land['type'];
        $user['landSize'] = $land['size'];
        $user['landLocation'] = $land['location'];
    } else {
        // If no land ownership details found, return empty values
        $user['landType'] = "N/A";
        $user['landSize'] = "0";
        $user['landLocation'] = "N/A";
    }

    echo json_encode(['success' => true] + $user);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();
?>
