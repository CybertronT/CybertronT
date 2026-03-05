<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first'); window.location.href='../login.php';</script>";
    exit;
}

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<script>alert('Invalid request method'); window.history.back();</script>";
    exit;
}

// Session values
$user_id   = intval($_SESSION['user_id']);
$role      = $_SESSION['role'] ?? 'landlord';
$full_name = $_SESSION['name'] ?? '';
$contact   = $_SESSION['contact'] ?? '';

// --- Listing limit check ---
$listing_limit = 5;
$countStmt = $conn->prepare("SELECT COUNT(*) FROM hostels WHERE user_id = ?");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$countStmt->bind_result($current_count);
$countStmt->fetch();
$countStmt->close();

if ($current_count >= $listing_limit) {
    echo "<script>
        alert('You have reached the maximum of {$listing_limit} listings. Please contact support to upgrade.');
        window.location.href='listings.php';
    </script>";
    exit;
}

// Form inputs
$title       = $_POST['title'] ?? '';
$type        = $_POST['roomType'] ?? '';
$city        = $_POST['city'] ?? '';
$area        = $_POST['area'] ?? '';
$price = isset($_POST['price']) ? intval(str_replace(',', '', $_POST['price'])) : 0;
$period      = $_POST['period'] ?? '';
$facilities  = isset($_POST['facilities']) ? json_encode($_POST['facilities']) : '[]';
$description = $_POST['description'] ?? '';

$agentFee = ($role === 'agent' && isset($_POST['agentFee']))
    ? intval(str_replace(',', '', $_POST['agentFee']))
    : null;

// --- Generate slug from title ---
function slugify($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}
$slug = slugify($title);

// Ensure slug is unique
$baseSlug = $slug;
$counter = 1;
$check = $conn->prepare("SELECT id FROM hostels WHERE slug = ?");
$check->bind_param("s", $slug);
$check->execute();
$result = $check->get_result();
while ($result && $result->num_rows > 0) {
    $slug = $baseSlug . '-' . $counter;
    $check->bind_param("s", $slug);
    $check->execute();
    $result = $check->get_result();
    $counter++;
}
$check->close();

// --- File upload handling ---
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$allowedTypes = ['image/jpeg','image/png','image/jpg','image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadedFiles = [];
$maxPhotos = 10;

if (!empty($_FILES['photos']['name'][0])) {
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
        if (count($uploadedFiles) >= $maxPhotos) break;
        if (!is_uploaded_file($tmpName)) continue;

        $fileType = mime_content_type($tmpName);
        $fileSize = $_FILES['photos']['size'][$key];
        if (!in_array($fileType, $allowedTypes)) continue;
        if ($fileSize > $maxFileSize) continue;

        $fileName = basename($_FILES['photos']['name'][$key]);
        $newName = bin2hex(random_bytes(8)) . "_" . preg_replace("/[^A-Za-z0-9\.\-_]/", "_", $fileName);
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $uploadedFiles[] = $newName;
        }
    }
}
$photos = json_encode($uploadedFiles);

// --- Insert query ---
$sql = "INSERT INTO hostels
(user_id, role, full_name, contact, agentFee, title, slug, type, city, area, price, period, facilities, description, photos)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<script>alert('Prepare failed: " . addslashes($conn->error) . "'); window.history.back();</script>";
    exit;
}

$stmt->bind_param(
    "isssisssssissss",
    $user_id,
    $role,
    $full_name,
    $contact,
    $agentFee,
    $title,
    $slug,
    $type,
    $city,
    $area,
    $price,
    $period,
    $facilities,
    $description,
    $photos
);

if ($stmt->execute()) {
    echo "<script>
        alert('Listing added successfully and will be approved by the admin shortly.');
        setTimeout(function(){
            window.location.href = 'listings.php';
        }, 1500); // 1.5-second delay before redirect
    </script>";
    exit;


} else {
    $errorMsg = addslashes($stmt->error);
    echo "<script>alert('Error saving listing: {$errorMsg}'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
