<?php
// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input values from the form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Both username and password are required.'
        ]);
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Check if the username already exists
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
        $stmtCheck->bind_param("s", $username);
        $stmtCheck->execute();
        $stmtCheck->bind_result($userExists);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($userExists > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'This username is already taken.'
            ]);
            exit();
        }

        // Insert the new admin account
        $stmt = $conn->prepare("INSERT INTO admins (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Admin account added successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add admin account. Please try again.'
            ]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    } finally {
        $conn->close(); // Close database connection
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
