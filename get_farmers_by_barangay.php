<?php
include('db_connection.php');

// Check if the barangay is set
if (isset($_GET['barangay'])) {
    $barangay = $_GET['barangay'];

    // Query to fetch farmers based on barangay
    $query = "SELECT * FROM farmers WHERE barangay = '$barangay'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $farmers = [];
        while ($row = $result->fetch_assoc()) {
            $farmers[] = $row; // Store each farmer's details
        }
        echo json_encode(['success' => true, 'farmers' => $farmers]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No farmers found in this barangay.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Barangay is required.']);
}

$conn->close();
?>
