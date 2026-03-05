<?php
session_start();
require "config.php";

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $slug      = trim($_POST['slug'] ?? '');
    $user_id   = intval($_SESSION['user_id']);
    $name      = trim($_SESSION['full_name'] ?? $_SESSION['name'] ?? '');
    $rating    = intval($_POST['rating']);
    $comment   = trim($_POST['comment']);

    // get hostel id from slug
    $stmt = $conn->prepare("SELECT id FROM hostels WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->bind_result($hostel_id);
    $stmt->fetch();
    $stmt->close();

    if (!$hostel_id) {
        $conn->close();
        header("Location: /?error=hostel_not_found");
        exit();
    }

    // validate rating (must be between 1 and 5)
    if ($rating < 1 || $rating > 5) {
        $conn->close();
        header("Location: /hostel/" . urlencode($slug) . "?error=invalid_rating");
        exit();
    }

    if (!empty($comment) && !empty($name)) {
        $stmt = $conn->prepare("
            INSERT INTO feedback (hostel_id, user_id, name, rating, comment, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iisis", $hostel_id, $user_id, $name, $rating, $comment);

        if (!$stmt->execute()) {
            die("Insert failed: " . $stmt->error);
        }

        $stmt->close();
    }
}

$conn->close();

//  redirect back using slug
if (!empty($slug)) {
    header("Location: /hostel/" . urlencode($slug));
} else {
    header("Location: /");
}
exit();
