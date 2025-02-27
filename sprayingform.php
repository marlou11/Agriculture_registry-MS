<?php
session_start();
include 'db_connection.php'; // Ensure this file connects to your database

// Check if user is logged in
if (!isset($_SESSION['rsbaNumber'])) {
    die("Error: User session not found!");
}

$rsbaNumber = $_SESSION['rsbaNumber'];

// Fetch user ID and Barangay based on RSBA Number
$query = "SELECT id, barangay FROM users WHERE rsbaNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rsbaNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['id']; // Store user ID
    $barangay = $row['barangay']; // Store user's barangay
} else {
    die("Error: User not found!");
}

// Validate form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $spray_type = isset($_POST['spray_type']) ? trim($_POST['spray_type']) : "";
    $application_equipment = isset($_POST['application_equipment']) ? trim($_POST['application_equipment']) : "";
    $spraying_date = isset($_POST['spraying_date']) ? $_POST['spraying_date'] : "";
    $area_sprayed = isset($_POST['area_sprayed']) ? trim($_POST['area_sprayed']) : "";

    // Validate required fields
    if (empty($spray_type) || empty($application_equipment) || empty($spraying_date) || empty($area_sprayed)) {
        die("Error: All fields are required.");
    }

    // Validate date format
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $spraying_date)) {
        die("Error: Invalid date format.");
    }

    // Handle file upload
    if (isset($_FILES['photo_documentation']) && $_FILES['photo_documentation']['error'] == 0) {
        $upload_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["photo_documentation"]["name"]);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow only image file types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        // Move uploaded file to target directory
        if (!move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $target_file)) {
            die("Error: Failed to upload file.");
        }
    } else {
        die("Error: Photo documentation is required.");
    }

    // Insert data into database including barangay
    $query = "INSERT INTO spraying_record (user_id, spray_type, application_equipment, spraying_date, area_sprayed, photo_documentation, barangay) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssss", $user_id, $spray_type, $application_equipment, $spraying_date, $area_sprayed, $target_file, $barangay);
    
    if ($stmt->execute()) {
        echo "Success: Spraying activity recorded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
