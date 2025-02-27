<?php
include 'db_connection.php';

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_name = $_POST['farmer_name'];
    $farm_location = $_POST['farm_location'];
    $crop_type = $_POST['crop_type'];
    $variety = $_POST['variety'];
    $total_area_planted = $_POST['total_area_planted'];
    $seeding_method = $_POST['seeding_method'];
    $seeding_date = $_POST['seeding_date'];
    $photo_before = $_POST['photo_before'];
    $photo_during = $_POST['photo_during'];
    $photo_after = $_POST['photo_after'];

    // Insert data into the seeding table
    $sql = "INSERT INTO seeding (farmer_name, farm_location, crop_type, variety, total_area_planted, seeding_method, seeding_date, photo_before, photo_during, photo_after)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $farmer_name, $farm_location, $crop_type, $variety, $total_area_planted, $seeding_method, $seeding_date, $photo_before, $photo_during, $photo_after);

    if ($stmt->execute()) {
        echo "Seeding data submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
