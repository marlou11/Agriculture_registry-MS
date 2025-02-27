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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $land_area = floatval($_POST['land_area']);
    $prep_method = trim($_POST['prep_method']);
    $start_date = $_POST['start_date'];
    $completion_date = $_POST['completion_date'];
    $prep_type = trim($_POST['prep_type']);
    $additional_notes = trim($_POST['additional_notes'] ?? '');

    // Validate required fields
    if (empty($land_area) || empty($prep_method) || empty($start_date) || empty($completion_date) || empty($prep_type)) {
        header("Location: land-prep.php?status=error&message=All fields are required.");
        exit;
    }

    // Ensure date validation
    if ($start_date > $completion_date) {
        header("Location: land-prep.php?status=error&message=Start date cannot be later than completion date.");
        exit;
    }

    $target_dir = "uploads/";
    
    // Ensure uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    // Handle file upload
    $file_name = NULL;
    if (!empty($_FILES["photo_documentation"]["name"])) {
        $file_name = time() . "_" . basename($_FILES["photo_documentation"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            header("Location: land-prep.php?status=error&message=Invalid file type. Only JPG, JPEG, PNG & GIF are allowed.");
            exit;
        }

        // Check for upload errors
        if ($_FILES["photo_documentation"]["error"] !== UPLOAD_ERR_OK) {
            header("Location: land-prep.php?status=error&message=File upload error.");
            exit;
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $target_file)) {
            header("Location: land-prep.php?status=error&message=Error uploading file.");
            exit;
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO land_prep_records 
        (land_area, prep_method, start_date, completion_date, prep_type, additional_notes, photo_documentation) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("dssssss", 
        $land_area, 
        $prep_method, 
        $start_date, 
        $completion_date, 
        $prep_type, 
        $additional_notes, 
        $file_name
    );

    if ($stmt->execute()) {
        header("Location: land-prep.php?status=success");
    } else {
        header("Location: land-prep.php?status=error&message=" . urlencode($stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: land-prep.php?status=error&message=Invalid request.");
}
?>
