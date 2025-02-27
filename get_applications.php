<?php
include('db_connection.php');

// Query to get only pending applications
$query = "SELECT * FROM application_form WHERE status = 'pending'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($applications);
} else {
    echo json_encode([]);  // Return an empty array if no pending applications are found
}

mysqli_close($conn);
?>
