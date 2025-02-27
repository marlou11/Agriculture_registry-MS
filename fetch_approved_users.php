<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'arms_db');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$query = "
    SELECT 
        u.rsbaNumber, 
        u.firstName, 
        u.lastName, 
        u.homeAddress, 
        u.contactNumber, 
        GROUP_CONCAT(l.type ORDER BY l.type ASC) AS ownership_types,
        GROUP_CONCAT(l.size ORDER BY l.type ASC) AS land_sizes,
        GROUP_CONCAT(l.location ORDER BY l.type ASC) AS land_locations
    FROM users u
    LEFT JOIN land_ownership l ON u.id = l.user_id
    WHERE u.status = 'Approved'
    GROUP BY u.id;
";

$result = $conn->query($query);

if (!$result) {
    die('Query failed: ' . $conn->error);
}

$approvedUsers = [];
while ($row = $result->fetch_assoc()) {
    // Convert comma-separated values into arrays
    $row['ownership_types'] = explode(',', $row['ownership_types']);
    $row['land_sizes'] = explode(',', $row['land_sizes']);
    $row['land_locations'] = explode(',', $row['land_locations']);
    $approvedUsers[] = $row;
}

echo json_encode($approvedUsers);

$conn->close();
?>
