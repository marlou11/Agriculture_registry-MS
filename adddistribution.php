<?php
header('Content-Type: application/json');
require 'db_connection.php'; // Ensure this file correctly initializes `$conn`

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $batchId = isset($_POST['batchId']) ? intval($_POST['batchId']) : 0;
    $selectedFarmerIds = isset($_POST['farmerIds']) ? $_POST['farmerIds'] : [];
    $totalDistributedQuantity = isset($_POST['distributionQuantity']) ? intval($_POST['distributionQuantity']) : 0;
    $distributionDate = isset($_POST['distributionDate']) ? trim($_POST['distributionDate']) : date('Y-m-d');

    // Validate input
    if ($batchId <= 0 || empty($selectedFarmerIds) || $totalDistributedQuantity <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid input data."]);
        exit;
    }

    $numFarmers = count($selectedFarmerIds);
    $individualQuantity = floor($totalDistributedQuantity / $numFarmers); // Even distribution

    if ($individualQuantity <= 0) {
        echo json_encode(["success" => false, "message" => "Total quantity is too low to distribute evenly."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Lock the batch row for safe update
        $stmt = $conn->prepare("SELECT totalQuantity FROM batches WHERE batchId = ? FOR UPDATE");
        $stmt->bind_param("i", $batchId);
        $stmt->execute();
        $stmt->bind_result($totalQuantity);
        $stmt->fetch();
        $stmt->close();

        if (!$totalQuantity || $totalDistributedQuantity > $totalQuantity) {
            throw new Exception("Not enough stock available in the batch.");
        }

        foreach ($selectedFarmerIds as $farmerId) {
            // Fetch farmer details
            $stmt = $conn->prepare("SELECT rsbaNumber, firstName, lastName, barangay FROM users WHERE id = ?");
            $stmt->bind_param("i", $farmerId);
            $stmt->execute();
            $stmt->bind_result($rsbaNumber, $firstName, $lastName, $barangay);

            if (!$stmt->fetch()) {
                throw new Exception("Farmer ID $farmerId not found.");
            }
            $stmt->close();

            $fullName = $firstName . " " . $lastName; // Combine first and last name

            // Insert distribution record
            $stmt = $conn->prepare("INSERT INTO distributions (batchId, farmerId, rsbaNumber, name, barangay, quantity, distributionDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiissis", $batchId, $farmerId, $rsbaNumber, $fullName, $barangay, $individualQuantity, $distributionDate);
            $stmt->execute();
            $stmt->close();
        }

        // Update batch quantity
        $newTotalQuantity = $totalQuantity - $totalDistributedQuantity;
        $stmt = $conn->prepare("UPDATE batches SET totalQuantity = ? WHERE batchId = ?");
        $stmt->bind_param("ii", $newTotalQuantity, $batchId);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        echo json_encode(["success" => true, "message" => "Distribution recorded successfully."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
