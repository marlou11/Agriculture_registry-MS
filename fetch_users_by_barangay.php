<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include database connection file

// Get the barangay parameter from the request (default empty string if not provided)
$barangay = $_GET['barangay'] ?? '';

// Check if barangay is empty, return an error message if so
if (empty($barangay)) {
    echo json_encode(['error' => 'No barangay provided']);
    exit;
}

// Prepare the SQL query to fetch approved farmers for the given barangay
$sql = "SELECT rsba_number, first_name, last_name FROM users WHERE barangay = ? AND status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $barangay); // Bind the barangay parameter to the query
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to store user data
$users = [];

// Fetch the users if any records are found
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'rsba_number' => $row['rsba_number'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name']
        ];
    }
} else {
    error_log("No users found for barangay: " . $barangay);
}

// Output the result as JSON
echo json_encode($users);

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>