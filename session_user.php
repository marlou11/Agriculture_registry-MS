<?php
session_start();
include('db_connection.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the logged-in user's details
    $sql = "SELECT first_name, last_name, rsba_number, address, contact_number, profile_picture FROM users WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'User not logged in']);
}
?>
