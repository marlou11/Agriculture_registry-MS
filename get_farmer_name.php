<?php
include('db_connection.php');

if (isset($_GET['rsbaNumber'])) {
    $rsbaNumber = $_GET['rsbaNumber'];

    $query = "SELECT name FROM farmers WHERE rsba_number = '$rsbaNumber'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'name' => $data['name']]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'RSBA number is required.']);
}

$conn->close();
?>
