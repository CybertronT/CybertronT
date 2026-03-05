<?php
session_start();
require "../config.php";


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Only allow landlords and agents
$allowed_roles = ['landlord', 'agent'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listings.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'] ?? '';

// Collect POST data
$listing_id  = intval($_POST['listing_id']);
$title       = $_POST['title'] ?? '';
$type        = $_POST['roomType'] ?? '';
$city        = $_POST['city'] ?? '';
$area        = $_POST['area'] ?? '';
$price       = intval($_POST['price'] ?? 0);
$period      = $_POST['period'] ?? '';
$facilities  = isset($_POST['facilities']) ? json_encode($_POST['facilities']) : '[]';
$description = $_POST['description'] ?? '';
$agentFee    = ($role === 'agent' && isset($_POST['agentFee'])) ? intval($_POST['agentFee']) : null;

// --- Check ownership ---
$stmt = $conn->prepare("SELECT photos FROM hostels WHERE id = ? AND user_id = ? AND role = ?");
$stmt->bind_param("iis", $listing_id, $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();
$stmt->close();

if (!$listing) {
    echo "<script>alert('Unauthorized action.'); window.location.href='listings.php';</script>";
    exit;
}

// Existing photos
$oldPhotos = json_decode($listing['photos'], true) ?? [];

// --- Handle removals ---
if (!empty($_POST['remove_photos'])) {
    foreach ($_POST['remove_photos'] as $remove) {
        $filePath = __DIR__ . "/uploads/" . basename($remove);
        if (file_exists($filePath)) unlink($filePath);
        $oldPhotos = array_diff($oldPhotos, [$remove]);
    }
}

// --- File upload handling with validation ---
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg','image/webp'];
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

        $fileName = basename($_FILES['photos']['name'][$key]);
        $newName = time() . "_" . bin2hex(random_bytes(4)) . "_" .
                   preg_replace("/[^A-Za-z0-9\.\-_]/", "_", $fileName);
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $newPhotos[] = $newName;
        }
    }
}

// Merge remaining + new photos
$allPhotos = array_merge(array_values($oldPhotos), $newPhotos);
$photosJson = json_encode($allPhotos);

// --- Update query ---
$sql = "UPDATE hostels SET 
            agentFee = ?, 
            title = ?, 
            type = ?, 
            city = ?, 
            area = ?, 
            price = ?, 
            period = ?, 
            facilities = ?, 
            description = ?, 
            photos = ?
        WHERE id = ? AND user_id = ? AND role = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);

$stmt->bind_param(
    "issssissssiss",
    $agentFee, $title, $type, $city, $area,
    $price, $period, $facilities, $description,
    $photosJson, $listing_id, $user_id, $role
);

if ($stmt->execute()) {
    echo "<script>alert('Listing updated successfully!'); window.location.href='listings.php';</script>";
} else {
    $error = addslashes($stmt->error);
    echo "<script>alert('Error updating listing: {$error}'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
