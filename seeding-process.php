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
    $user_id = $row['id']; 
    $barangay = $row['barangay']; 
} else {
    die("Error: User not found!");
}

// Handle file upload
$targetDir = "uploads/"; 
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["photo_documentation"]["name"]);
$targetFilePath = $targetDir . time() . "_" . $fileName;
$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

// Allow only specific image formats
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($fileType), $allowedTypes)) {
    die("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
}

// Move the uploaded file
if (!move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $targetFilePath)) {
    die("Error: Failed to upload image.");
}

// Get form inputs
$seed_type = $_POST['seed_type'];
$seed_quantity = $_POST['seed_quantity'];
$seeding_method = $_POST['seeding_method'];
$seeding_date = $_POST['seeding_date'];

// Insert data into `seeding_records` table
$query = "INSERT INTO seeding_records (user_id, seed_type, seed_quantity, seeding_method, seeding_date, photo_documentation, barangay, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);
$stmt->bind_param("issssss", $user_id, $seed_type, $seed_quantity, $seeding_method, $seeding_date, $targetFilePath, $barangay);

if ($stmt->execute()) {
    echo "Seeding activity recorded successfully!";
    header("Location: dashboard.html"); // Redirect after successful submission
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
