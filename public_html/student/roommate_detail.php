<?php
session_start();
require "../config.php";

$id = intval($_GET['id'] ?? 0);

// track views if logged in
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['viewed_roommates'])) {
        $_SESSION['viewed_roommates'] = [];
    }
    if (!in_array($id, $_SESSION['viewed_roommates'])) {
        $conn->query("UPDATE roommate_requests SET views = COALESCE(views,0) + 1 WHERE id = $id");
        $_SESSION['viewed_roommates'][] = $id;
    }
}

$stmt = $conn->prepare("SELECT * FROM roommate_requests WHERE id = ? AND status='approved' LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$roommate = $result && $result->num_rows ? $result->fetch_assoc() : null;

if ($roommate) {
    $roommate['photos'] = $roommate['photos'] ? json_decode($roommate['photos'], true) : [];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $roommate ? htmlspecialchars($roommate['requester_name']) : "Not Found" ?> - HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;line-height:1.6;}
    header{background:white;padding:15px 25px;position:sticky;top:0;z-index:1000;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    header .logo{display:flex;align-items:center;gap:12px;}
    header .logo img{height:50px;}
    header .logo span{font-size:1.4rem;font-weight:600;}

    .container{max-width:95%;margin:30px auto;padding:10px;background:transparent;}

    .back-link{display:inline-block;margin-bottom:20px;color:#008CBA;text-decoration:none;font-weight:600;}
    .back-link:hover{text-decoration:underline;}
    .title{font-size:2rem;margin-bottom:8px;color:#222;}
    .meta{color:#666;margin-bottom:10px;font-size:0.95rem;}
    .budget{font-size:1.2rem;font-weight:600;color:#e67e22;margin:12px 0;}

    .content-wrapper{display:flex;gap:25px;align-items:flex-start;}
    .main-content{flex:3;}
    .sidebar{flex:1;display:flex;flex-direction:column;gap:20px;}

    .main-image{width:100%;height:400px;object-fit:cover;border-radius:10px;margin-bottom:15px;cursor:pointer;}
    .thumbs{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin-bottom:25px;}
    .thumbs img{width:100%;height:80px;object-fit:cover;border-radius:8px;cursor:pointer;transition:transform .2s;}
    .thumbs img:hover{transform:scale(1.05);}

    .section{margin-bottom:25px;padding:20px;border:1px solid #eee;border-radius:8px;background:#fff;}
    .section h3{margin-bottom:12px;font-size:1.2rem;color:#444;}

    .contact-box{background:#008CBA;color:white;text-align:center;}
    .contact-box h3{color:white;}
    .contact-box a{display:inline-block;margin:8px;padding:12px 18px;border-radius:6px;background:#e67e22;color:white;text-decoration:none;font-weight:600;}
    .contact-box a:hover{background:#cf6416;}

    .safety-tips ul{font-size:0.9rem;color:#555;line-height:1.5;padding-left:18px;}

    footer{text-align:center;padding:20px;background:#222;color:#ddd;margin-top:40px;}

    /* lightbox */
    .lightbox{display:none;position:fixed;z-index:2000;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);justify-content:center;align-items:center;}
    .lightbox img{max-width:90%;max-height:90%;border-radius:8px;}
    .lightbox span.close{position:absolute;top:20px;right:30px;font-size:40px;color:white;cursor:pointer;}
    .lightbox .nav{position:absolute;top:50%;transform:translateY(-50%);font-size:50px;color:white;cursor:pointer;user-select:none;}
    .lightbox .prev{left:30px;}
    .lightbox .next{right:30px;}
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
  <a href="../roommates.php" class="back-link">← Back to Roommates</a>

  <?php if ($roommate): ?>
    <h1 class="title"><?= htmlspecialchars($roommate['requester_name']) ?></h1>
    <p class="meta">📍 <?= htmlspecialchars($roommate['campus']) ?> • <?= htmlspecialchars($roommate['area']) ?> • 👁 <?= (int)($roommate['views'] ?? 0) ?> views</p>
    <p class="budget">Budget: ₦<?= number_format($roommate['budget_min']) ?> - ₦<?= number_format($roommate['budget_max']) ?></p>

    <div class="content-wrapper">
      <!-- MAIN CONTENT -->
      <div class="main-content">
        <?php if (!empty($roommate['photos'])): ?>
          <img id="mainImage" class="main-image" src="uploads/roommates/<?= htmlspecialchars($roommate['photos'][0]) ?>" alt="Main Image" onclick="openLightbox(0)">
          <div class="thumbs">
            <?php foreach ($roommate['photos'] as $index => $img): ?>
              <img src="uploads/roommates/<?= htmlspecialchars($img) ?>" onclick="changeMainImage(<?= $index ?>)">
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <img class="main-image" src="placeholder.jpg" alt="No Image Available">
        <?php endif; ?>

        <div class="section">
          <h3>Basic Information</h3>
          <p><strong>Campus:</strong> <?= htmlspecialchars($roommate['campus']) ?></p>
          <p><strong>Area:</strong> <?= htmlspecialchars($roommate['area']) ?></p>
          <p><strong>Room Type:</strong> <?= htmlspecialchars($roommate['room_type']) ?></p>
          <p><strong>Gender Preference:</strong> <?= htmlspecialchars($roommate['gender_pref']) ?></p>
          <p><strong>Religion Preference:</strong> <?= htmlspecialchars($roommate['religion_pref']) ?></p>
          <p><strong>Move-in Date:</strong> <?= htmlspecialchars($roommate['move_in_date']) ?></p>
          <p><strong>Status:</strong> 
            <?php if ($roommate['availability'] === 'available'): ?>
              <span style="color:green;font-weight:600;">Available</span>
            <?php else: ?>
              <span style="color:red;font-weight:600;">Not Available</span>
            <?php endif; ?>
          </p>
        </div>

        <div class="section">
          <h3>Description</h3>
          <p><?= nl2br(htmlspecialchars($roommate['description'])) ?></p>
        </div>
      </div>

      <!-- SIDEBAR -->
      <aside class="sidebar">
        <div class="section contact-box">
          <h3>Interested?</h3>
          <p>Chat with this roommate directly:</p>
          <?php if (isset($_SESSION['user_id']) && !empty($roommate['requester_contact']) && $roommate['availability'] === 'available'): ?>
            <a href="https://wa.me/<?= htmlspecialchars($roommate['requester_contact']) ?>?text=Hi, I’m interested in connecting as a roommate" target="_blank">💬 WhatsApp</a>
            <a href="tel:<?= htmlspecialchars($roommate['requester_contact']) ?>">📞 Call</a>
            <p><strong>Phone:</strong> <?= htmlspecialchars($roommate['requester_contact']) ?></p>
          <?php elseif ($roommate['availability'] !== 'available'): ?>
            <p> This roommate is not available anymore.</p>
          <?php else: ?>
            <p> Please <a href="../login.php?redirect=roommate_detail.php?id=<?= $id ?>">Login</a> to see contact details.</p>
          <?php endif; ?>
        </div>

        <div class="section safety-tips">
          <h3>Safety Tips</h3>
          <ul>
            <li>Meet in a public place</li>
            <li>Verify identity before sharing personal info</li>
            <li>Discuss expectations clearly</li>
            <li>Inspect accommodation together before committing</li>
            <li>Trust your instincts</li>
          </ul>
        </div>
      </aside>
    </div>
  <?php else: ?>
    <h2>Roommate not found.</h2>
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
let photos = <?= json_encode($roommate['photos'] ?? []) ?>;
let currentIndex = 0;

function changeMainImage(index){
  document.getElementById('mainImage').src = "uploads/roommates/" + photos[index];
}
function openLightbox(index){
  currentIndex = index;
  document.getElementById('lightbox').style.display='flex';
  document.getElementById('lightboxImg').src = "uploads/roommates/" + photos[currentIndex];
}
function closeLightbox(){
  document.getElementById('lightbox').style.display='none';
}
function changePhoto(step){
  currentIndex += step;
  if (currentIndex < 0) currentIndex = photos.length - 1;
  if (currentIndex >= photos.length) currentIndex = 0;
  document.getElementById('lightboxImg').src = "uploads/roommates/" + photos[currentIndex];
}
</script>
</body>
</html>
