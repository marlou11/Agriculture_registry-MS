<?php
require 'db_connection.php'; // Include DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['userId'];

    // Get upload progress
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS completed, 
               (SELECT COUNT(*) FROM required_uploads) AS total
        FROM user_uploads
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $completed = $result['completed'];
        $total = $result['total'];
        $canProceed = $completed == $total;
        echo json_encode(['completed' => $completed, 'total' => $total, 'canProceed' => $canProceed]);
    } else {
        echo json_encode(['error' => 'Failed to fetch progress']);
    }
}
?>
