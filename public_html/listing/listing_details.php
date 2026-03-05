<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Redirect if role does not match (only landlord + agent allowed here)
$allowed_roles = ['landlord', 'agent'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}
$user_id = intval($_SESSION['user_id']);
$role    = $_SESSION['role'] ?? "guest";

// Securely fetch hostel by id and ensure it belongs to this user
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM hostels WHERE id = ? AND user_id = ? AND role = ? LIMIT 1");
$stmt->bind_param("iis", $id, $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result && $result->num_rows ? $result->fetch_assoc() : null;

// If listing exists, decode facilities and photos
if ($listing) {
    $listing['facilities'] = !empty($listing['facilities'])
        ? (json_last_error() === JSON_ERROR_NONE
            ? array_map('trim', json_decode($listing['facilities'], true))
            : array_map('trim', explode(",", $listing['facilities'])))
        : [];

    $listing['photos'] = $listing['photos'] ? json_decode($listing['photos'], true) : [];

    // Optional: use session info if full_name/contact is not stored
    if (empty($listing['full_name'])) $listing['full_name'] = $_SESSION['name'] ?? 'User';
    if (empty($listing['contact']))   $listing['contact']   = $_SESSION['contact'] ?? '';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $listing ? htmlspecialchars($listing['title']) : "Not Found" ?> - HostelConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
 * {box-sizing:border-box;margin:0;padding:0;}
    body {font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;line-height:1.6;}
    header {background:white;padding:15px 25px;position:sticky;top:0;z-index:1000;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    header .logo {display:flex;align-items:center;gap:12px;}
    header .logo img {height:55px;}
    header .logo span {font-size:1.4rem;font-weight:600;color:#222;}
    .container {max-width:1100px;margin:30px auto;padding:25px;background:white;
      border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
    .back-link {display:inline-block;margin-bottom:20px;color:#008CBA;text-decoration:none;font-weight:600;}
    .back-link:hover {text-decoration:underline;}
    .title {font-size:2rem;margin-bottom:8px;color:#222;}
    .meta {color:#666;margin-bottom:5px;font-size:0.95rem;}
    .price {font-size:1.6rem;font-weight:600;color:#e67e22;margin:15px 0;}
    
    /* Main image */
    .main-image {width:100%;height:420px;object-fit:cover;border-radius:10px;margin-bottom:15px;cursor:pointer;}
    /* Thumbs */
    .thumbs {display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin-bottom:25px;}
    .thumbs img {width:100%;height:80px;object-fit:cover;border-radius:8px;cursor:pointer;transition:transform .2s;}
    .thumbs img:hover {transform:scale(1.05);}
    
    .section {margin-bottom:25px;padding:20px;border:1px solid #eee;border-radius:8px;background:#fafafa;}
    .section h3 {margin-bottom:12px;font-size:1.2rem;color:#444;}
    .section p,.section ul {color:#555;}
    .tags span {background:#eee;padding:4px 8px;margin:2px;border-radius:4px;font-size:12px;display:inline-block;}
    
    .contact-box {background:#008CBA;color:white;text-align:center;}
    .contact-box h3 {color:white;}
    .contact-box a {display:inline-block;margin:8px;padding:12px 18px;border-radius:6px;
      background:#e67e22;color:white;text-decoration:none;font-weight:600;}
    .contact-box a:hover {background:#cf6416;}
    footer {text-align:center;padding:20px;background:#222;color:#ddd;margin-top:40px;}

    /* Lightbox */
    .lightbox {display:none;position:fixed;z-index:2000;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);justify-content:center;align-items:center;}
    .lightbox img {max-width:90%;max-height:90%;border-radius:8px;}
    .lightbox span.close {position:absolute;top:20px;right:30px;font-size:40px;color:white;cursor:pointer;}
    .lightbox .nav {position:absolute;top:50%;transform:translateY(-50%);font-size:50px;color:white;cursor:pointer;user-select:none;}
    .lightbox .prev {left:30px;}
    .lightbox .next {right:30px;}
</style>
</head>
<body>
<header>
  <div class="logo">
    <img src="../logo.png" alt="HostelConnect">
    <span>HostelConnect</span>
  </div>
</header>

<div class="container">
  <a href="listings.php" class="back-link">← Back to My Listings</a>

  <?php if ($listing): ?>
    <h1 class="title"><?= htmlspecialchars($listing['title']) ?></h1>
    <p class="meta">📍 <?= htmlspecialchars($listing['city']) ?> • <?= htmlspecialchars($listing['area']) ?> • <?= htmlspecialchars($listing['type']) ?></p>
    <p class="price">₦<?= number_format($listing['price']) ?> / <?= htmlspecialchars($listing['period']) ?></p>

    <?php if (!empty($listing['photos'])): ?>
      <img id="mainImage" class="main-image" src="uploads/<?= htmlspecialchars($listing['photos'][0]) ?>" alt="Main Image" onclick="openLightbox(0)">
      <div class="thumbs">
        <?php foreach ($listing['photos'] as $index => $img): ?>
          <img src="uploads/<?= htmlspecialchars($img) ?>" onclick="changeMainImage(<?= $index ?>)">
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <img class="main-image" src="placeholder.jpg" alt="No Image Available">
    <?php endif; ?>

    <div class="section">
      <h3>Basic Information</h3>
      <p><strong>City:</strong> <?= htmlspecialchars($listing['city']) ?></p>
      <p><strong>Area:</strong> <?= htmlspecialchars($listing['area']) ?></p>
      <p><strong>Room Type:</strong> <?= htmlspecialchars($listing['type']) ?></p>
      <p><strong>Billing Period:</strong> <?= htmlspecialchars($listing['period']) ?></p>
      <p><strong>Price:</strong> ₦<?= number_format($listing['price']) ?></p>
    </div>

    <div class="section">
      <h3>Posted By</h3>
      <p><strong><?= htmlspecialchars(ucfirst($listing['role'])) ?>:</strong> <?= htmlspecialchars($listing['full_name']) ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($listing['contact']) ?></p>
      <?php if ($listing['role'] === 'agent' && !empty($listing['agentFee'])): ?>
        <p><strong>Agent Fee:</strong> ₦<?= number_format($listing['agentFee']) ?></p>
      <?php endif; ?>
    </div>

    <div class="section">
      <h3>Description</h3>
      <p><?= nl2br(htmlspecialchars($listing['description'])) ?></p>
    </div>

    <?php if (!empty($listing['facilities'])): ?>
    <div class="section">
      <h3>Facilities</h3>
      <div class="tags">
        <?php foreach ($listing['facilities'] as $f): ?>
          <span><?= htmlspecialchars(trim($f)) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="section contact-box">
      <h3>Contact Info</h3>
      <?php if (!empty($listing['contact'])): ?>
        <a href="https://wa.me/<?= htmlspecialchars($listing['contact']) ?>?text=I'm interested in <?= urlencode($listing['title']) ?>" target="_blank">💬 WhatsApp</a>
        <a href="tel:<?= htmlspecialchars($listing['contact']) ?>">📞 Call</a>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <h2>Listing not found or you don’t have permission to view it.</h2>
  <?php endif; ?>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
  <span class="close" onclick="closeLightbox()">&times;</span>
  <span class="nav prev" onclick="changePhoto(-1)">&#10094;</span>
  <img id="lightboxImg" src="">
  <span class="nav next" onclick="changePhoto(1)">&#10095;</span>
</div>

<footer>
  <p>&copy; 2025 HostelConnect</p>
</footer>

<script>
let photos = <?= json_encode($listing['photos'] ?? []) ?>;
let currentIndex = 0;

function changeMainImage(index){
  document.getElementById('mainImage').src = "uploads/" + photos[index];
}
function openLightbox(index){
  currentIndex = index;
  document.getElementById('lightbox').style.display='flex';
  document.getElementById('lightboxImg').src = "uploads/" + photos[currentIndex];
}
function closeLightbox(){
  document.getElementById('lightbox').style.display='none';
}
function changePhoto(step){
  currentIndex += step;
  if (currentIndex < 0) currentIndex = photos.length - 1;
  if (currentIndex >= photos.length) currentIndex = 0;
  document.getElementById('lightboxImg').src = "uploads/" + photos[currentIndex];
}
</script>
</body>
</html>
