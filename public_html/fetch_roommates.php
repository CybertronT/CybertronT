<?php
require "config.php";

$sql = "SELECT id, requester_name, campus, area, room_type, budget_min, budget_max, 
               gender_pref, religion_pref, move_in_date, description, photos, availability
        FROM roommate_requests
        WHERE status = 'approved'
        ORDER BY id DESC";

$res = $conn->query($sql);

$data = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        // Decode photos JSON safely
        $row['photos'] = $row['photos'] ? json_decode($row['photos'], true) : [];
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
