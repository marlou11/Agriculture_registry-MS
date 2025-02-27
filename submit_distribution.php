<?php
// Enable CORS if you're working in a cross-origin setup (optional)
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");

// Include database connection
require 'db_connection.php';

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit();
    }

    // Retrieve form data
    $batchId = isset($_POST['batchId']) ? intval($_POST['batchId']) : null;
    $distributedQuantity = isset($_POST['distributedQuantity']) ? intval($_POST['distributedQuantity']) : null;
    $rsbaNumber = isset($_POST['rsbaNumber']) ? trim($_POST['rsbaNumber']) : null;
    $distributedTo = isset($_POST['distributedTo']) ? trim($_POST['distributedTo']) : null;
    $distributionDate = isset($_POST['distributionDate']) ? trim($_POST['distributionDate']) : null;

    // Validate input
    if (!$batchId || !$distributedQuantity || !$rsbaNumber || !$distributedTo || !$distributionDate) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    // Check if the batch exists and has sufficient quantity
    $query = "SELECT total_quantity FROM batches WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $batchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $batch = $result->fetch_assoc();

    if (!$batch) {
        echo json_encode(['status' => 'error', 'message' => 'Batch not found.']);
        exit();
    }

    $availableQuantity = $batch['total_quantity'];
    if ($distributedQuantity > $availableQuantity) {
        echo json_encode(['status' => 'error', 'message' => 'Distributed quantity exceeds available quantity.']);
        exit();
    }

    // Update batch quantity and insert the distribution record
    $conn->begin_transaction();

    // Insert into distribution records
    $insertQuery = "INSERT INTO distribution_records (batch_id, distributed_quantity, rsba_number, distributed_to, distribution_date)
                    VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param('iisss', $batchId, $distributedQuantity, $rsbaNumber, $distributedTo, $distributionDate);
    $insertResult = $insertStmt->execute();

    // Update batch quantity
    $updateQuery = "UPDATE batches SET total_quantity = total_quantity - ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ii', $distributedQuantity, $batchId);
    $updateResult = $updateStmt->execute();

    if ($insertResult && $updateResult) {
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Distribution record added successfully.']);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to add distribution record.']);
    }
} catch (Exception $e) {
    // Handle unexpected errors
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
