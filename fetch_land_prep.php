<?php
include 'db_connect.php';

$sql = "SELECT * FROM land_prep_records ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Land Area (ha)</th>
                <th>Method</th>
                <th>Start Date</th>
                <th>Completion Date</th>
                <th>Type</th>
                <th>Notes</th>
                <th>Photo</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['land_area']}</td>
                <td>{$row['prep_method']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['completion_date']}</td>
                <td>{$row['prep_type']}</td>
                <td>{$row['additional_notes']}</td>
                <td>";
        if (!empty($row['photo_documentation'])) {
            echo "<img src='{$row['photo_documentation']}' width='100'>";
        } else {
            echo "No Photo";
        }
        echo "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No records found.";
}

$conn->close();
?>
