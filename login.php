<?php
session_start();
include('db_connection.php'); // Include database connection

header('Content-Type: application/json'); // Set response type

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rsbaNumber = $_POST['rsba_number'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE rsbaNumber = ? AND status = 'approved' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rsbaNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rsbaNumber'] = $user['rsbaNumber'];
            $_SESSION['status'] = $user['status'];

            echo json_encode(['success' => true, 'redirect' => 'farmersportal.html']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found or not approved']);
    }
}
?>
