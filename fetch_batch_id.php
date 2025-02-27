<?php

// Include your database connection file
include 'db_connection.php'; 

// Check if batchId is provided
if (isset($_GET["batchId"]) && !empty($_GET["batchId"])) {
    $batchId = $_GET["batchId"];

    // Escape the batchId input to prevent SQL injection
    $batchId = mysqli_real_escape_string($conn, $batchId);

    // SQL query to fetch batchId
    $sql = "SELECT batchId 
            FROM batches
            WHERE batchId = '$batchId'";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $batch = mysqli_fetch_assoc($result);

        // Output as JSON
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "batchId" => $batch['batchId']]); // Return batchId

    } else {
        // Handle case where no batch is found
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Batch not found"]);
    }

} else {
    // Handle case where batchId is not provided
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Batch ID not provided"]);
}

mysqli_close($conn); 

?>