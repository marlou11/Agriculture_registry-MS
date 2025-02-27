<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batch_id = $_POST['batch_id'];
    $distributed_quantity = (int)$_POST['distributed_quantity'];
    $distribution_date = $_POST['distribution_date'];
    $barangay = $_POST['barangay'];
    $farmer_rsba_numbers = json_decode($_POST['farmer_rsba_numbers'], true);

    // Validate data
    if (!$batch_id || !$distributed_quantity || !$distribution_date || !$barangay || empty($farmer_rsba_numbers)) {
        http_response_code(400);
        echo "Invalid data.";
        exit;
    }

    // Fetch the batch's remaining quantity
    $stmt = $conn->prepare("SELECT remaining_quantity FROM distribution_batches WHERE batch_id = ?");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $stmt->bind_result($remaining_quantity);
    $stmt->fetch();
    $stmt->close();

    // Ensure distributed quantity does not exceed remaining quantity
    if ($distributed_quantity > $remaining_quantity) {
        http_response_code(400);
        echo "Distributed quantity exceeds available batch quantity.";
        exit;
    }

    // Update the batch's remaining quantity
    $new_remaining_quantity = $remaining_quantity - $distributed_quantity;
    $stmt = $conn->prepare("UPDATE distribution_batches SET remaining_quantity = ? WHERE batch_id = ?");
    $stmt->bind_param("ii", $new_remaining_quantity, $batch_id);
    $stmt->execute();
    $stmt->close();

    // Log the distribution record
    $farmer_rsba_numbers_str = implode(',', $farmer_rsba_numbers);
    $stmt = $conn->prepare("INSERT INTO distribution_records (batch_id, distributed_quantity, barangay, distribution_date, farmer_rsba_numbers) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $batch_id, $distributed_quantity, $barangay, $distribution_date, $farmer_rsba_numbers_str);
    $stmt->execute();
    $stmt->close();

    echo "Distribution added successfully.";
}

$conn->close();
?>
