<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'arms_db');

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the barangay parameter is passed
if (isset($_GET['barangay'])) {
    $barangay = $_GET['barangay'];

    // Fetch farmers based on the selected barangay
    $query = "SELECT rsba_number, name FROM farmers WHERE barangay = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$barangay]);

    $farmers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode(['farmers' => $farmers]);
}
?>