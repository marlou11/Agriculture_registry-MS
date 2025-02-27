<?php
include 'db_connection.php';
$result = $conn->query("SELECT * FROM barangays");
$barangays = [];
while ($row = $result->fetch_assoc()) {
    $barangays[] = $row;
}
echo json_encode($barangays);
?>
