<?php
session_start();
include 'db_connection.php'; // Ensure this file connects to your database

header('Content-Type: application/json'); // Send JSON response

// Check if user is logged in
if (!isset($_SESSION['rsbaNumber'])) {
    echo json_encode(["status" => "error", "message" => "User session not found!"]);
    exit;
}

$rsbaNumber = $_SESSION['rsbaNumber'];

// Fetch user ID based on RSBA Number
$query = "SELECT id, barangay FROM users WHERE rsbaNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rsbaNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $barangay = $row['barangay'];
} else {
    echo json_encode(["status" => "error", "message" => "User not found!"]);
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $harvest_quantity = $_POST['harvest_quantity'];
    $harvesting_method = $_POST['harvesting_method'];
    $harvest_date = $_POST['harvest_date'];

    // Validate numeric input for quantity
    if (!is_numeric($harvest_quantity) || $harvest_quantity <= 0) {
        echo json_encode(["status" => "error", "message" => "Harvest quantity must be a positive number."]);
        exit;
    }

    // Validate date (ensure it's not a future date)
    if (strtotime($harvest_date) > strtotime(date("Y-m-d"))) {
        echo json_encode(["status" => "error", "message" => "Harvest date cannot be in the future."]);
        exit;
    }

    // Handle file upload
    if (isset($_FILES['photo_documentation']) && $_FILES['photo_documentation']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["photo_documentation"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        if (!in_array($file_ext, $allowed_types)) {
            echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, JPEG, and PNG are allowed."]);
            exit;
        }

        $unique_file_name = $upload_dir . uniqid("harvest_", true) . "." . $file_ext;
        if (move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $unique_file_name)) {
            // Insert data into the database
            $query = "INSERT INTO harvesting_records (user_id, harvest_quantity, harvesting_method, harvest_date, photo_documentation, barangay) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("idssss", $user_id, $harvest_quantity, $harvesting_method, $harvest_date, $unique_file_name, $barangay);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Harvesting activity recorded successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Database Error: " . $stmt->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading file."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Please upload a valid image."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
