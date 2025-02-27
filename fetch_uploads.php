<?php
require 'db_connection.php'; // Include DB connection setup

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['userId'];

    // Fetch user uploads
    $stmt = $pdo->prepare("SELECT file_name, file_path FROM user_uploads WHERE user_id = ?");
    $stmt->execute([$userId]);
    $uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($uploads);
}
?>
