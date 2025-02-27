<?php
session_start();
include 'db_connection.php'; // Ensure this file contains the DB connection

// Check if the user is logged in
if (!isset($_SESSION['rsbaNumber'])) {
    die("Error: User not logged in!");
}

$rsbaNumber = $_SESSION['rsbaNumber']; // Get RSBA Number from session

// Fetch user ID and barangay from the database
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $land_area = floatval($_POST['land_area']); // Ensure correct type
    $prep_method = $_POST['prep_method'];
    $start_date = $_POST['start_date'];
    $completion_date = $_POST['completion_date'];
    $prep_type = $_POST['prep_type'];
    $additional_notes = $_POST['additional_notes'];

    // Handle file upload
    if (!empty($_FILES['photo_documentation']['name'])) {  
        $target_dir = "uploads/";
        $file_name = basename($_FILES["photo_documentation"]["name"]); // Use 'photo_documentation'
        $file_path = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif']; 
    
        // Create directory if it does not exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            die("Error: Only JPG, JPEG, PNG, and GIF files are allowed!");
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["photo_documentation"]["tmp_name"], $file_path)) {
            $photo_documentation = $file_path;
        } else {
            die("Error: Photo upload failed!");
        }
    }

    // Prepare SQL query to insert data
    $stmt = $conn->prepare("INSERT INTO land_prep_records 
        (user_id, land_area, prep_method, start_date, completion_date, prep_type, additional_notes, photo_documentation, barangay) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("idsssssss", 
        $user_id, 
        $land_area, 
        $prep_method, 
        $start_date, 
        $completion_date, 
        $prep_type, 
        $additional_notes, 
        $photo_documentation, 
        $barangay
    );

    // Execute query
    if ($stmt->execute()) {
        echo '<script>
                setTimeout(function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Land preparation record successfully added!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "land-prep.html";
                    });
                }, 200);
              </script>';
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong: ' . $stmt->error . '",
                    icon: "error",
                    confirmButtonText: "OK"
                });
              </script>';
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
