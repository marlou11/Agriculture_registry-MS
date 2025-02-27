<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    header('Content-Type: application/json');
    
    // Set the directory for uploads
    $upload_dir = __DIR__ . '/uploads/';

    // Check if the uploads directory exists
    if (!file_exists($upload_dir)) {
        echo json_encode(['error' => 'Upload directory does not exist.']);
        exit;
    }

    // Check for file upload errors
    if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Error uploading file: ' . $_FILES['profile_picture']['error']]);
        exit;
    }

    // Check MIME type
    $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
    if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    // Check file size
    $max_file_size = 2 * 1024 * 1024; // 2 MB
    if ($_FILES['profile_picture']['size'] > $max_file_size) {
        echo json_encode(['error' => 'File size exceeds 2 MB limit.']);
        exit;
    }

    // Sanitize and move the uploaded file
    $filename = basename($_FILES['profile_picture']['name']);
    $sanitized_filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $filename);
    $unique_filename = uniqid('profile_', true) . '.' . pathinfo($sanitized_filename, PATHINFO_EXTENSION);
    $target_file = $upload_dir . $unique_filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        // Update user profile picture in the database
        $user_id = $_SESSION['user_id'];
        $sql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $unique_filename, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'File uploaded successfully.', 'profile_picture' => 'uploads/' . $unique_filename]);
        } else {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'Error uploading file. Please check the upload directory.']);
    }
}
?>
