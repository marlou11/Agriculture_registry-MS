<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$database = "arms_db"; // Replace with your database name

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = [
    'pending_count' => 0,
    'approved_count' => 0,
    'owned_count' => 0,
    'leased_count' => 0,
    'rented_count' => 0,
    'barangay_count' => []
];

// Query for pending applications (no change needed)
$result = $conn->query("SELECT status, COUNT(*) AS count FROM users WHERE status = 'Pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $response['pending_count'] = $row['count'];
    $result->free_result();
}


// Query for approved applications (this is for the approved_count)
$result = $conn->query("SELECT status, COUNT(*) AS count FROM users WHERE status = 'Approved'");
if ($result) {
    $row = $result->fetch_assoc();
    $response['approved_count'] = $row['count'];
    $result->free_result();
}

// Query for land ownership types for APPROVED users only
$result = $conn->query("SELECT lo.type, COUNT(*) AS count 
                        FROM land_ownership lo
                        JOIN users u ON lo.user_id = u.id  -- Assuming a user_id link
                        WHERE u.status = 'Approved'
                        GROUP BY lo.type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'Owned') {
            $response['owned_count'] = $row['count'];
        } elseif ($row['type'] === 'Leased') {
            $response['leased_count'] = $row['count'];
        } elseif ($row['type'] === 'Rented') {
            $response['rented_count'] = $row['count'];
        }
    }
    $result->free_result();
}


// Query for farmers registered per barangay for APPROVED users only
$result = $conn->query("SELECT u.barangay, COUNT(*) AS count 
                        FROM users u
                        WHERE u.status = 'Approved'
                        GROUP BY u.barangay");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $response['barangay_count'][$row['barangay']] = $row['count'];
    }
    $result->free_result();
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>