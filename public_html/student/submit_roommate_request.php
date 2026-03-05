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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id   = $_SESSION['user_id'];
    $name      = $_SESSION['name'] ?? '';
    $contact   = $_SESSION['contact'] ?? '';

    $campus        = trim($_POST['campus']);
    $area          = trim($_POST['area']);
    $room_type     = trim($_POST['room_type']);
    $budget_min    = intval($_POST['budget_min']);
    $budget_max    = intval($_POST['budget_max']);
    $gender_pref   = trim($_POST['gender_pref']);   // Male/Female/Any
    $religion_pref = trim($_POST['religion_pref']); // Christianity/Islam/Other
    $move_in_date  = $_POST['move_in_date'];
    $description   = trim($_POST['description']);

    // --- File upload handling ---
    $uploadDir = "uploads/roommates/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg','image/png', 'image/jpg','image/webp'];
    $maxFileSize  = 5 * 1024 * 1024; // 5MB
    $photoFiles   = [];

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
                $photoFiles[] = $newName;
            }
        }
    }

    $photosJson = json_encode($photoFiles);

    // --- Insert into DB ---
    $stmt = $conn->prepare("INSERT INTO roommate_requests 
        (user_id, requester_name, requester_contact, campus, area, room_type, budget_min, budget_max, gender_pref, religion_pref, move_in_date, description, photos) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isssssiisssss",
        $user_id,
        $name,
        $contact,
        $campus,
        $area,
        $room_type,
        $budget_min,
        $budget_max,
        $gender_pref,
        $religion_pref,
        $move_in_date,
        $description,
        $photosJson
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('Request submitted successfully!');
            window.location.href = 'student_dashboard.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Error submitting request: " . addslashes($stmt->error) . "');
            window.history.back();
        </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: request_roommate.php");
    exit;
}
?>
