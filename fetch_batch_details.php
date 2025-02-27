<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Ensure db_connection.php initializes $conn properly

if (isset($_GET["batchId"]) && !empty($_GET["batchId"])) {
    $batchId = mysqli_real_escape_string($conn, $_GET["batchId"]);

    // Fetch batch details
    $sql = "SELECT batchId, batchNumber FROM batches WHERE batchId = '$batchId'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $batch = mysqli_fetch_assoc($result);
        echo json_encode(["success" => true, "batch" => $batch]);
    } else {
        echo json_encode(["success" => false, "message" => "Batch not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Batch ID not provided"]);
}

mysqli_close($conn);
?>
