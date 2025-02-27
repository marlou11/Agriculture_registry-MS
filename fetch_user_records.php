<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db_connection.php"; // Ensure this file connects to the DB

// Define the tables containing records
$tables = [
    "fertilizer_records", "harvesting_records", "land_prep_records",
    "seeding_records", "spraying_record", "weeding_records"
];

$data = [];
$barangayData = []; // Organizing by barangay
$countPerActivity = []; // Counting activity types

foreach ($tables as $table) {
    // Fetch records from each table
    $query = "SELECT * FROM `$table`";
    $result = $conn->query($query);

    if (!$result) {
        echo json_encode([
            "success" => false,
            "error" => "Database error in $table: " . $conn->error,
            "query" => $query
        ]);
        exit();
    }

    $records = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($records as $record) {
        $userId = $record['user_id'];

        // Fetch user details (name, RSBA number, and barangay)
        $userQuery = "SELECT 
                        CONCAT(firstName, ' ', COALESCE(middleInitial, ''), ' ', lastName) AS fullName, 
                        rsbaNumber, 
                        barangay 
                      FROM users WHERE id = ?";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $userData = $userResult->fetch_assoc();

        if ($userData) {
            $record['fullName'] = $userData['fullName'];
            $record['rsbaNumber'] = $userData['rsbaNumber'];
            $record['barangay'] = $userData['barangay'];

            // Organize by barangay
            $barangay = $userData['barangay'];
            if (!isset($barangayData[$barangay])) {
                $barangayData[$barangay] = [];
            }
            $barangayData[$barangay][$table][] = $record;

            // Count each activity type
            if (!isset($countPerActivity[$table])) {
                $countPerActivity[$table] = 0;
            }
            $countPerActivity[$table]++;
        }
    }
}

// Final JSON Response
$response = [
    "success" => true,
    "data" => $barangayData,
    "activity_counts" => $countPerActivity
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
