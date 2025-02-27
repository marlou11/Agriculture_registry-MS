<?php
session_start();
header('Content-Type: application/json');

require_once 'db_connection.php'; // Include your database connection file

$userId = $_SESSION['user_id'];

try {
    // Fetch user data and land ownership details in a single query
    $sql = "SELECT 
                u.id, u.firstName, u.middleInitial, u.lastName, u.rsbaNumber, u.contactNumber, u.barangay, u.homeAddress, u.profilePicture,
                l.type, l.size, l.location
            FROM users u
            LEFT JOIN land_ownership l ON u.id = l.user_id
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user['land_ownership'] = [
            'type' => $user['type'],
            'size' => $user['size'],
            'location' => $user['location']
        ];
        unset($user['type'], $user['size'], $user['location']); // Remove redundant keys
        echo json_encode(['user' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    error_log("Database error: " . $e->getMessage()); //Log the error for debugging
    echo json_encode(['error' => 'Database error']);
} finally {
    $conn->close();
}

?>