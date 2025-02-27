<?php
// submit_batch.php

// Get the form data
$batch_name = $_POST['batch_name'];
$batch_number = $_POST['batch_number'];
$total_quantity = $_POST['total_quantity'];

// For demonstration purposes, simply log the data to a file or database
// You would typically insert this data into a database

// For now, we'll just simulate success
$response = [
    'success' => true,
    'message' => 'Batch submitted successfully!'
];

// Return a JSON response
echo json_encode($response);
?>
