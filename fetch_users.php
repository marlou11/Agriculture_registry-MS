<?php
// Include database connection
require 'db_connection.php';

$sql = "
    SELECT 
        users.id, users.firstName, users.middleInitial, users.lastName, 
        users.rsbaNumber, users.contactNumber, users.barangay, users.homeAddress, 
        users.status, 
        land_ownership.type, land_ownership.size, land_ownership.location
    FROM users
    LEFT JOIN land_ownership ON users.id = land_ownership.user_id
    WHERE users.status = 'pending'
";

$result = $conn->query($sql);

$pendingUsers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];

        // Organize user data
        if (!isset($pendingUsers[$userId])) {
            $pendingUsers[$userId] = [
                "id" => $row['id'],
                "firstName" => $row['firstName'],
                "middleInitial" => $row['middleInitial'],
                "lastName" => $row['lastName'],
                "rsbaNumber" => $row['rsbaNumber'],
                "contactNumber" => $row['contactNumber'],
                "barangay" => $row['barangay'],
                "homeAddress" => $row['homeAddress'],
                "status" => $row['status'],
                "landOwnership" => []
            ];
        }

        // If land ownership exists, add it
        if (!empty($row['type'])) {
            $pendingUsers[$userId]["landOwnership"][] = [
                "type" => $row['type'],
                "size" => $row['size'],
                "location" => $row['location']
            ];
        }
    }
}

// Return the data as JSON
echo json_encode(array_values($pendingUsers));

$conn->close();
?>
