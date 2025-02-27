<?php
session_start();
include('db_connection.php'); // Database connection
header('Content-Type: application/json');



// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['activity_type'])) {
    $activity_type = $_POST['activity_type'];

    // Ensure valid activity type
    $valid_activities = ['land-prep', 'seeding', 'weeding', 'fertilizing', 'spraying', 'harvesting'];
    if (!in_array($activity_type, $valid_activities)) {
        echo json_encode(['error' => 'Invalid activity type']);
        exit;
    }

    // Initialize common data
    $user_id = $_SESSION['user_id'];
    $photo_path = '';  // Placeholder for the photo path

    // Validate and handle photo upload (if provided)
    if (isset($_FILES['photo_documentation']) && $_FILES['photo_documentation']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        $filename = basename($_FILES['photo_documentation']['name']);
        $sanitized_filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $filename);
        $unique_filename = uniqid('activity_', true) . '.' . pathinfo($sanitized_filename, PATHINFO_EXTENSION);
        $target_file = $upload_dir . $unique_filename;

        // Check for file type and size (image validation)
        $allowed_file_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['photo_documentation']['type'], $allowed_file_types)) {
            echo json_encode(['error' => 'Invalid file type. Only JPEG and PNG images are allowed.']);
            exit;
        }

        $max_file_size = 5 * 1024 * 1024; // Max 5MB
        if ($_FILES['photo_documentation']['size'] > $max_file_size) {
            echo json_encode(['error' => 'File size exceeds the maximum limit of 5MB.']);
            exit;
        }

        if (move_uploaded_file($_FILES['photo_documentation']['tmp_name'], $target_file)) {
            $photo_path = 'uploads/' . $unique_filename; // Store the file path in the database
        } else {
            echo json_encode(['error' => 'Failed to upload photo documentation.']);
            exit;
        }
    }

    // Validate required fields for each activity type
    $sql = '';
    $params = [];
    $types = '';  // For parameter binding in MySQL

    switch ($activityType) {
        case 'land-prep':
            $fields = array_merge($fields, ['land_area', 'prep_method', 'start_date', 'completion_date', 'prep_type', 'additional_notes']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'land_area', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'prep_method', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'completion_date', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'prep_type', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'additional_notes', FILTER_SANITIZE_STRING)
            ]);
            $types .= "ssssss";
            break;
        case 'seeding':
            $fields = array_merge($fields, ['seed_type', 'seed_quantity', 'seeding_method', 'seeding_date']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'seed_type', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'seed_quantity', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'seeding_method', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'seeding_date', FILTER_SANITIZE_STRING)
            ]);
            $types .= "ssss";
            break;
        case 'weeding':
            $fields = array_merge($fields, ['weeding_method', 'weeding_start_date', 'weeding_end_date']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'weeding_method', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'weeding_start_date', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'weeding_end_date', FILTER_SANITIZE_STRING)
            ]);
            $types .= "sss";
            break;
        case 'fertilizing':
            $fields = array_merge($fields, ['fertilizer_type', 'fertilizer_quantity', 'application_method', 'application_date', 'area_covered', 'effectiveness']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'fertilizer_type', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'fertilizer_quantity', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'application_method', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'application_date', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'area_covered', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'effectiveness', FILTER_SANITIZE_STRING)
            ]);
            $types .= "ssssss";
            break;
        case 'spraying':
            $fields = array_merge($fields, ['spray_type', 'application_equipment', 'spraying_date', 'area_sprayed']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'spray_type', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'application_equipment', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'spraying_date', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'area_sprayed', FILTER_SANITIZE_STRING)
            ]);
            $types .= "ssss";
            break;
        case 'harvesting':
            $fields = array_merge($fields, ['harvest_quantity', 'harvesting_method', 'harvest_date']);
            $values = array_merge($values, [
                filter_input(INPUT_POST, 'harvest_quantity', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'harvesting_method', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'harvest_date', FILTER_SANITIZE_STRING)
            ]);
            $types .= "sss";
            break;
    }

    // Build and execute the SQL query
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO activities (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        sendError("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Activity recorded successfully.']);
    } else {
        sendError('Database error: ' . $stmt->error);
    }
    $stmt->close();
} else {
    sendError('Invalid request method.');
}
$conn->close();
?>