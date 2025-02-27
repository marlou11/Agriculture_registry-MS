<?php
// Set the header to indicate JSON response
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "arms_db";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return a JSON error message if connection fails
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Retrieve the selected Barangay from the query parameter
$barangay = $_GET['barangay'] ?? '';

// If the Barangay is not provided, return an error
if (empty($barangay)) {
    echo json_encode(['error' => 'Barangay is required.']);
    exit();
}

// Prepare and execute the SQL query to fetch farmers by Barangay
$sql = "SELECT rsba_number, first_name, last_name FROM farmers WHERE barangay = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barangay);
$stmt->execute();
$result = $stmt->get_result();

// Check if farmers are found
if ($result->num_rows > 0) {
    // Fetch the farmers and return them as JSON
    $farmers = [];
    while ($row = $result->fetch_assoc()) {
        $farmers[] = ['rsba_number' => $row['rsba_number'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name']];
    }

    echo json_encode($farmers);
} else {
    echo json_encode(['error' => 'No farmers found for this Barangay.']);
}

// Close the database connection
$conn->close();
?>
