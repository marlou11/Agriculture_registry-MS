<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Ensure this file properly connects to the database

if (isset($_GET["barangay"]) && !empty($_GET["barangay"])) {
    $barangay = mysqli_real_escape_string($conn, $_GET["barangay"]);

    // Fetch farmers with RSBA number
    $sql = "SELECT id, firstName, lastName, rsbaNumber FROM users WHERE barangay = '$barangay'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $farmers = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $farmers[] = $row; // Add each farmer's details to the array
        }

        echo json_encode(["success" => true, "farmers" => $farmers]);
    } else {
        echo json_encode(["success" => false, "message" => "No farmers found for this barangay"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Barangay parameter missing"]);
}

mysqli_close($conn);
?>
