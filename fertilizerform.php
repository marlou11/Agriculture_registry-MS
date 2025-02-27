<?php
session_start();
include 'db_connection.php'; // Ensure this connects to your database

// Check if user is logged in
if (!isset($_SESSION['rsbaNumber'])) {
    die("Error: User session not found!");
}

$rsbaNumber = $_SESSION['rsbaNumber'];

// Fetch user ID and Barangay
$query = "SELECT id, barangay FROM users WHERE rsbaNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rsbaNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['id']; // Store user ID
    $barangay = !empty($row['barangay']) ? $row['barangay'] : "Unknown"; // Handle NULL barangay
} else {
    die("Error: User not found!");
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fertilizer_type = trim($_POST['fertilizer_type']);
    $fertilizer_quantity = floatval($_POST['fertilizer_quantity']);
    $application_method = trim($_POST['application_method']);
    $application_date = $_POST['application_date'];
    $area_covered = trim($_POST['area_covered']);
    $effectiveness = trim($_POST['effectiveness'] ?? '');

    // Validate required fields
    if (empty($fertilizer_type) || empty($fertilizer_quantity) || empty($application_method) || empty($application_date) || empty($area_covered)) {
        die("Error: All fields are required.");
    }

    // Handle file upload
    if (isset($_FILES['photo_documentation']) && $_FILES['photo_documentation']['error'] == 0) {
        $upload_dir = "uploads/";
        
        // Ensure the directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }

        $file_name = basename($_FILES["photo_documentation"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        if (!in_array($file_ext, $allowed_types)) {
            die("Error: Invalid file type. Only JPG, JPEG, and PNG are allowed.");
        }

        // Unique file name
        $unique_file_name = $upload_dir . time() . "_" . $file_name;
        
        if (move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $unique_file_name)) {
            // Insert data into the database
            $query = "INSERT INTO fertilizer_records (user_id, fertilizer_type, fertilizer_quantity, application_method, application_date, area_covered, effectiveness, photo_documentation, barangay) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isdsssdss", $user_id, $fertilizer_type, $fertilizer_quantity, $application_method, $application_date, $area_covered, $effectiveness, $unique_file_name, $barangay);

            if ($stmt->execute()) {
                echo "Fertilizer application recorded successfully!";
            } else {
                echo "Database Error: " . $stmt->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Error: Please upload a valid image.";
    }
} else {
    echo "Invalid request.";
}

?>
