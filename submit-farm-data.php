<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');

// Database credentials (use environment variables or a config file instead of hardcoding!)
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "arms_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Handle DB connection errors
if ($conn->connect_error) {
    sendJsonResponse(['status' => 'error', 'message' => "Database connection failed: " . $conn->connect_error]);
}
// Check user login
if (!isset($_SESSION) || !session_id()) {
    sendJsonResponse(['status' => 'error', 'message' => 'Session not started.']);
}

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    sendJsonResponse(['status' => 'error', 'message' => 'User ID not found in session.']);
}

// Verify user exists in the database
$userCheckSql = "SELECT 1 FROM users WHERE user_id = ?";
$userCheckStmt = $conn->prepare($userCheckSql);
if (!$userCheckStmt) {
    sendJsonResponse(['status' => 'error', 'message' => "Error preparing statement: " . $conn->error]);
}
$userCheckStmt->bind_param("i", $userId);
$userCheckStmt->execute();
$userCheckResult = $userCheckStmt->get_result();
if ($userCheckResult->num_rows === 0) {
    sendJsonResponse(['status' => 'error', 'message' => 'User not found.']);
}
$userCheckStmt->close();


$conn->autocommit(FALSE); // Start transaction

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = filter_input_array(INPUT_POST, [
            'activity_type' => ['filter' => FILTER_SANITIZE_STRING],
            'land_area' => ['filter' => FILTER_VALIDATE_FLOAT],
            'prep_method' => ['filter' => FILTER_SANITIZE_STRING],
            'start_date' => ['filter' => FILTER_SANITIZE_STRING],
            'completion_date' => ['filter' => FILTER_SANITIZE_STRING],
            'prep_type' => ['filter' => FILTER_SANITIZE_STRING],
            'additional_notes' => ['filter' => FILTER_SANITIZE_STRING],
            'seed_type' => ['filter' => FILTER_SANITIZE_STRING],
            'seed_quantity' => ['filter' => FILTER_SANITIZE_STRING],
            'seeding_method' => ['filter' => FILTER_SANITIZE_STRING],
            'seeding_date' => ['filter' => FILTER_SANITIZE_STRING],
            'weeding_method' => ['filter' => FILTER_SANITIZE_STRING],
            'weeding_start_date' => ['filter' => FILTER_SANITIZE_STRING],
            'weeding_end_date' => ['filter' => FILTER_SANITIZE_STRING],
            'fertilizer_type' => ['filter' => FILTER_SANITIZE_STRING],
            'fertilizer_quantity' => ['filter' => FILTER_VALIDATE_FLOAT],
            'application_method' => ['filter' => FILTER_SANITIZE_STRING],
            'application_date' => ['filter' => FILTER_SANITIZE_STRING],
            'area_covered' => ['filter' => FILTER_VALIDATE_FLOAT],
            'effectiveness' => ['filter' => FILTER_SANITIZE_STRING],
            'spray_type' => ['filter' => FILTER_SANITIZE_STRING],
            'application_equipment' => ['filter' => FILTER_SANITIZE_STRING],
            'spraying_date' => ['filter' => FILTER_SANITIZE_STRING],
            'area_sprayed' => ['filter' => FILTER_VALIDATE_FLOAT],
            'harvest_quantity' => ['filter' => FILTER_SANITIZE_STRING],
            'harvesting_method' => ['filter' => FILTER_SANITIZE_STRING],
            'harvest_date' => ['filter' => FILTER_SANITIZE_STRING],
            'activity_set_id' => ['filter' => FILTER_SANITIZE_NUMBER_INT],
            'stage_index' => ['filter' => FILTER_SANITIZE_NUMBER_INT]
        ]);

        //Handle missing data gracefully
        foreach ($data as $key => $value) {
            if ($value === null) {
                $data[$key] = ''; // Or a default value as appropriate
            }
        }


        //Validate activity type
        if (!in_array($data['activity_type'], ['Land Preparation', 'Seeding', 'Weeding', 'Fertilizing', 'Spraying', 'Harvesting'])) {
            throw new Exception('Invalid activity type.');
        }

        $activitySetId = (int)$data['activity_set_id'];
        $stageIndex = (int)$data['stage_index'];

        //Dynamic SQL query generation based on activity type
        $sql = "INSERT INTO activities (user_id, activity_set_id, stage_index, activity_type, ";
        $values = "(?, ?, ?, ?";
        $params = [];
        $paramTypes = "iiii";

        //Add relevant columns and parameters depending on activity type
//Add relevant columns and parameters depending on activity type
switch($data['activity_type']) {
    case 'Land Preparation':
        $columns = ['land_area', 'prep_method', 'start_date', 'completion_date', 'prep_type', 'additional_notes'];
        $paramTypes .= "sdsss";
        $params = array_merge($params, [$data['land_area'], $data['prep_method'], $data['start_date'], $data['completion_date'], $data['prep_type'], $data['additional_notes']]);
        break;
    case 'Seeding':
        $columns = ['seed_type', 'seed_quantity', 'seeding_method', 'seeding_date'];
        $paramTypes .= "ssss";
        $params = array_merge($params, [$data['seed_type'], $data['seed_quantity'], $data['seeding_method'], $data['seeding_date']]);
        break;
    case 'Weeding':
        $columns = ['weeding_method', 'weeding_start_date', 'weeding_end_date'];
        $paramTypes .= "sss";
        $params = array_merge($params, [$data['weeding_method'], $data['weeding_start_date'], $data['weeding_end_date']]);
        break;
    case 'Fertilizing':
        $columns = ['fertilizer_type', 'fertilizer_quantity', 'application_method', 'application_date', 'area_covered', 'effectiveness'];
        $paramTypes .= "sdssds";
        $params = array_merge($params, [$data['fertilizer_type'], $data['fertilizer_quantity'], $data['application_method'], $data['application_date'], $data['area_covered'], $data['effectiveness']]);
        break;
    case 'Spraying':
        $columns = ['spray_type', 'application_equipment', 'spraying_date', 'area_sprayed'];
        $paramTypes .= "ssss";
        $params = array_merge($params, [$data['spray_type'], $data['application_equipment'], $data['spraying_date'], $data['area_sprayed']]);
        break;
    case 'Harvesting':
        $columns = ['harvest_quantity', 'harvesting_method', 'harvest_date'];
        $paramTypes .= "sss";
        $params = array_merge($params, [$data['harvest_quantity'], $data['harvesting_method'], $data['harvest_date']]);
        break;
    default:
        throw new Exception("Unknown activity type: " . $data['activity_type']);
}
        $sql .= implode(',', array: $columns) . ") VALUES " . $values . ")";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
              throw new Exception("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param($paramTypes, $userId, $activitySetId, $stageIndex, $data['activity_type'], ...$params);


        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }

        $activityId = $conn->insert_id;
        $uploadedPhotos = handlePhotoUploads($_FILES['photo_documentation'], $activityId, $conn);

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Activity submitted successfully!', 'photos' => $uploadedPhotos]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error: " . $e->getMessage()); // Log the error; use a robust logging solution in production
    echo json_encode(['status' => 'error', 'message' => 'An error occurred.']);
} finally {
    $conn->close();
}

// ... (handlePhotoUploads function from previous response, improved as below) ...


function handlePhotoUploads($files, $activityId, $conn) {
    $uploadedPhotos = [];
    $uploadDir = 'uploads/photos/'; // Set the upload directory.  MAKE SURE THIS DIRECTORY EXISTS AND IS WRITABLE BY THE WEBSERVER!
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB maximum file size

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Could not create upload directory: $uploadDir"); // Throw exception for better error handling
        }
    }

    if (!isset($files['name']) || !is_array($files['name'])) {
        return $uploadedPhotos; // No files uploaded
    }

    foreach ($files['name'] as $i => $fileName) {
        try {
            // Check for upload errors
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload error (" . $files['error'][$i] . "): $fileName");
            }

            // Check file size
            if ($files['size'][$i] > $maxSize) {
                throw new Exception("File too large: $fileName (Max size: 5MB)");
            }

            // Validate file type
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type: $fileName");
            }

            // Sanitize filename to prevent directory traversal attacks
            $newName = sanitizeFileName(uniqid() . '.' . $fileType);
            $uploadPath = $uploadDir . $newName;

            // Move the uploaded file
            if (!move_uploaded_file($files['tmp_name'][$i], $uploadPath)) {
                throw new Exception("Error uploading file: $fileName");
            }

            // Insert photo URL into database using prepared statement
            $photoSql = "INSERT INTO photos (activity_id, photo_url) VALUES (?, ?)";
            $photoStmt = $conn->prepare($photoSql);
            if (!$photoStmt) {
                // Remove uploaded file if statement preparation fails
                unlink($uploadPath);
                throw new Exception("Error preparing photo statement: " . $conn->error);
            }
            $photoStmt->bind_param("is", $activityId, $uploadPath);
            if (!$photoStmt->execute()) {
                // Remove uploaded file if database insertion fails
                unlink($uploadPath);
                throw new Exception("Error inserting photo into database: " . $photoStmt->error);
            }
            $photoStmt->close();
            $uploadedPhotos[] = $uploadPath;
        } catch (Exception $e) {
            // Handle errors within the loop, logging and throwing exception
            $errorMessage = "Error processing file '$fileName': " . $e->getMessage();
            error_log($errorMessage);
            throw new Exception($errorMessage); // Re-throw the exception to be caught in the main try...catch block
        }
    }
    return $uploadedPhotos;
}

function sanitizeFileName($fileName) {
    // Remove potentially harmful characters
    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
    return $fileName;
}

function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8'); // Specify charset for better compatibility
    http_response_code($data['status'] === 'success' ? 200 : 500); // Set appropriate HTTP status code
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // Use JSON_PRETTY_PRINT for readability, JSON_UNESCAPED_UNICODE for Unicode characters
    exit;
}

?>