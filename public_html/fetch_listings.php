<?php
header("Content-Type: application/json");
require "config.php";

// fetch only approved listings
$sql = "SELECT id, user_id, role, full_name, contact, agentFee, title, type, city, area, price, period, facilities, description, photos, availability, created_at, slug
        FROM hostels 
        WHERE status = 'approved' 
        ORDER BY id DESC";

$result = $conn->query($sql);

$listings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decode JSON fields
        $row['facilities'] = $row['facilities'] ? json_decode($row['facilities'], true) : [];
        $row['photos'] = $row['photos'] ? json_decode($row['photos'], true) : [];

        // Rename contact to match your frontend usage
        $row['landlordContact'] = $row['contact'];
        unset($row['contact']);

        // availability is already 'available' or 'unavailable' from DB
        $listings[] = $row;
    }
}

echo json_encode($listings);
$conn->close();
?>
