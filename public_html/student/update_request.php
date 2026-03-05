<?php
session_start();
require "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$allowed_roles = ['student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = intval($_POST['id']);
    $campus        = trim($_POST['campus']);
    $area          = trim($_POST['area']);
    $room_type     = trim($_POST['room_type']);
    $budget_min    = intval($_POST['budget_min']);
    $budget_max    = intval($_POST['budget_max']);
    $gender_pref   = trim($_POST['gender_pref']);   
    $religion_pref = trim($_POST['religion_pref']); 
    $move_in_date  = $_POST['move_in_date'];
    $description   = trim($_POST['description']);



    // --- Fetch existing photos ---
    $stmt = $conn->prepare("SELECT photos FROM roommate_requests WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $existingPhotos = $res['photos'] ? json_decode($res['photos'], true) : [];

    // Remove selected photos
    $removePhotos = $_POST['remove_photos'] ?? [];
    $keptPhotos = array_diff($existingPhotos, $removePhotos);

    // --- Upload new photos with validation ---
    $uploadDir = "uploads/roommates/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $maxFileSize  = 5 * 1024 * 1024; // 5MB
    $newPhotos    = [];

    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
            if (!is_uploaded_file($tmpName)) continue;

            $fileType = mime_content_type($tmpName);
            $fileSize = $_FILES['photos']['size'][$key];

            if (!in_array($fileType, $allowedTypes)) {
                echo "<script>alert('File type not allowed. Only JPG, PNG, WEBP.'); window.history.back();</script>";
                exit;
            }

            if ($fileSize > $maxFileSize) {
                echo "<script>alert('File too large. Max 5MB per file.'); window.history.back();</script>";
                exit;
            }

            $ext = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));
            $newName = uniqid("roommate_", true) . "." . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $newPhotos[] = $newName;
            }
        }
    }

    // Merge old + new photos
    $finalPhotos = array_merge($keptPhotos, $newPhotos);
    $photosJson = json_encode(array_values($finalPhotos));

    // --- Update request ---
    $stmt = $conn->prepare("UPDATE roommate_requests 
        SET campus=?, area=?, room_type=?, budget_min=?, budget_max=?, gender_pref=?, religion_pref=?, move_in_date=?, description=?, photos=? 
        WHERE id=? AND user_id=?");

    $stmt->bind_param(
        "sssiisssssii",   // gender_pref is string (s)
        $campus,
        $area,
        $room_type,
        $budget_min,
        $budget_max,
        $gender_pref,
        $religion_pref,
        $move_in_date,
        $description,
        $photosJson,
        $id,
        $user_id
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('Request updated successfully!');
            window.location.href = 'student_request.php';
        </script>";
    } else {
        echo "<script>
            alert('Error: " . addslashes($stmt->error) . "');
            window.history.back();
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
