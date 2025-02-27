<?php
session_start();
include('db_connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error_message' => 'User not logged in.']);
    exit();
}

// Get the activity ID (assuming it's passed as a parameter or form data)
$activity_id = $_POST['activity_id'];

// Check if any files are uploaded
if (isset($_FILES['photos'])) {
    $files = $_FILES['photos'];
    $photo_urls = [];  // Array to hold the paths/URLs of uploaded photos

    // Loop through each uploaded file
    foreach ($files['name'] as $index => $file_name) {
        // Handle file upload
        $file_tmp = $files['tmp_name'][$index];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_new_name = uniqid() . '.' . $file_extension;
        $file_upload_path = 'uploads/photos/' . $file_new_name;

        // Move the uploaded file to the desired location
        if (move_uploaded_file($file_tmp, $file_upload_path)) {
            // Add the file path to the photo_urls array
            $photo_urls[] = $file_upload_path;

            // Insert each photo URL into the database
            $sql = "INSERT INTO photos (activity_id, photo_url) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $activity_id, $file_upload_path);
            $stmt->execute();
        }
    }

    if (!empty($photo_urls)) {
        echo json_encode(['success_message' => 'Photos uploaded successfully!']);
    } else {
        echo json_encode(['error_message' => 'No photos uploaded.']);
    }
} else {
    echo json_encode(['error_message' => 'No photos were uploaded.']);
}
?>
