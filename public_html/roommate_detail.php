<?php
session_start();
require "config.php";

$id = intval($_GET['id'] ?? 0);

// Track views if logged in
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['viewed_roommates'])) $_SESSION['viewed_roommates'] = [];
    if (!in_array($id, $_SESSION['viewed_roommates'])) {
        $conn->query("UPDATE roommate_requests SET views = COALESCE(views,0) + 1 WHERE id = $id");
        $_SESSION['viewed_roommates'][] = $id;
    }
}

$stmt = $conn->prepare("SELECT * FROM roommate_requests WHERE id = ? AND status='approved' LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result   = $stmt->get_result();
$roommate = $result && $result->num_rows ? $result->fetch_assoc() : null;

if ($roommate) {
    $roommate['photos'] = $roommate['photos'] ? json_decode($roommate['photos'], true) : [];
}
$conn->close();

function initials($name) {
    if (!$name || $name === 'Anonymous') return '?';
    $parts = array_filter(explode(' ', $name));
    return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($parts, 0, 2))));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $roommate ? htmlspecialchars($roommate['requester_name']) . ' — HostelConnect' : 'Not Found — HostelConnect' ?></title>
  <meta name="description" content="<?= $roommate ? 'Roommate request from ' . htmlspecialchars($roommate['requester_name']) . ' near ' . htmlspecialchars($roommate['campus']) : 'Roommate not found' ?>">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
  <style>
/* ============================================================
   RESET & VARIABLES  — identical to details.php
============================================================ */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#0b68b1;
  --brand-dark:#084e87;
  --brand-light:#dff0ff;
  --accent:#cf6c17;
  --accent-dark:#a85510;
  --accent-light:#fff0e3;
  --dark:#0d1117;
  --body:#374151;
  --muted:#8b95a1;
  --border:#e4e9ef;
  --bg:#f4f7fc;
  --white:#ffffff;
  --green:#16a34a;
  --green-bg:#dcfce7;
  --red:#dc2626;
  --red-bg:#fee2e2;
  --yellow:#f59e0b;
  --shadow-sm:0 2px 12px rgba(11,104,177,.07);
  --shadow-md:0 8px 32px rgba(11,104,177,.12);
  --shadow-lg:0 20px 60px rgba(11,104,177,.18);
  --r:16px;
  --r-sm:10px;
}
html{scroll-padding-top:78px;scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);line-height:1.65;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--brand);border-radius:10px}

/* ============================================================
   HEADER  — identical to details.php
============================================================ */
header{
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(255,255,255,.94);
  backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
  padding:0 40px;height:72px;
  position:sticky;top:0;z-index:999;
  border-bottom:1px solid rgba(228,233,239,.9);
  box-shadow:0 2px 24px rgba(11,104,177,.06);
  animation:slideDown .6s cubic-bezier(.22,1,.36,1) both;
}
@keyframes slideDown{from{transform:translateY(-100%);opacity:0}to{transform:translateY(0);opacity:1}}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.logo img{height:40px;border-radius:8px}
.logo span{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--brand);letter-spacing:-.3px}
.header-right{display:flex;align-items:center;gap:10px}
.back-btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:9px 18px;border-radius:var(--r-sm);
  background:var(--brand-light);color:var(--brand);
  font-weight:600;font-size:.88rem;text-decoration:none;
  border:1.5px solid rgba(11,104,177,.15);
  transition:background .2s,transform .15s;
}
.back-btn:hover{background:#cce4f8;transform:translateY(-1px)}

/* ============================================================
   PAGE HERO BANNER  — same gradient as details.php
============================================================ */
.page-hero{
  position:relative;
  background:linear-gradient(140deg,#0b68b1 0%,#07438e 50%,#03295a 100%);
  padding:48px 40px 52px;
  overflow:hidden;
}
.page-hero::before{
  content:'';position:absolute;inset:0;
  background:url('bg.jpg') center/cover no-repeat;
  opacity:.06;z-index:0;
}
.page-hero-orb{position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none}
.page-hero-orb.o1{width:400px;height:400px;background:radial-gradient(circle,rgba(207,108,23,.22) 0%,transparent 70%);top:-100px;right:-80px}
.page-hero-orb.o2{width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,.05) 0%,transparent 70%);bottom:-60px;left:5%}
.hero-content{position:relative;z-index:1;max-width:1180px;margin:0 auto}

/* Breadcrumb — same as details.php */
.hero-breadcrumb{display:flex;align-items:center;gap:8px;font-size:.78rem;color:rgba(255,255,255,.5);margin-bottom:16px;letter-spacing:.3px}
.hero-breadcrumb a{color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s}
.hero-breadcrumb a:hover{color:rgba(255,255,255,.9)}
.hero-breadcrumb i{font-size:.6rem}

/* Type badge — same as details.php */
.hero-type-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
  color:rgba(255,255,255,.9);font-size:.75rem;font-weight:600;
  padding:5px 14px;border-radius:100px;margin-bottom:14px;
  backdrop-filter:blur(8px);letter-spacing:.5px;text-transform:uppercase;
}

/* Name as h1 — same as details.php */
.page-hero h1{
  font-family:'Playfair Display',serif;
  font-size:clamp(1.8rem,4vw,2.8rem);
  font-weight:800;color:#fff;
  line-height:1.18;letter-spacing:-.8px;
  margin-bottom:16px;
  animation:fadeSlideUp .7s .1s cubic-bezier(.22,1,.36,1) both;
}

/* Meta row — same as details.php */
.hero-meta{
  display:flex;flex-wrap:wrap;align-items:center;gap:16px;
  animation:fadeSlideUp .7s .25s cubic-bezier(.22,1,.36,1) both;
}
.hero-meta-item{display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.7);font-size:.87rem}
.hero-meta-item i{color:rgba(255,255,255,.5);font-size:.82rem}

/* Budget + avail row — mirrors price row in details.php */
.hero-price-row{
  margin-top:20px;
  display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap;
  animation:fadeSlideUp .7s .4s cubic-bezier(.22,1,.36,1) both;
}
.hero-price{
  font-family:'Playfair Display',serif;
  font-size:clamp(1.8rem,4vw,2.6rem);
  font-weight:800;color:#fbbf24;letter-spacing:-.5px;line-height:1;
}
.hero-price sub{
  font-family:'DM Sans',sans-serif;
  font-size:.9rem;font-weight:400;
  color:rgba(255,255,255,.55);letter-spacing:0;
}
.avail-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:100px;font-size:.8rem;font-weight:700}
.avail-pill.yes{background:rgba(22,163,74,.2);color:#4ade80;border:1px solid rgba(22,163,74,.3)}
.avail-pill.no{background:rgba(220,38,38,.2);color:#f87171;border:1px solid rgba(220,38,38,.3)}
.views-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:100px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);font-size:.78rem;border:1px solid rgba(255,255,255,.15)}

/* ============================================================
   LAYOUT — identical to details.php
============================================================ */
.page-body{
  max-width:1180px;margin:0 auto;
  padding:40px 24px 80px;
  display:grid;
  grid-template-columns:1fr 340px;
  gap:28px;
  align-items:start;
}

/* ============================================================
   GALLERY / AVATAR — identical to details.php
============================================================ */
.gallery-wrap{margin-bottom:28px}
.main-photo{
  width:100%;height:460px;object-fit:cover;
  border-radius:var(--r);cursor:zoom-in;
  box-shadow:var(--shadow-md);
  transition:transform .3s ease;display:block;
}
.main-photo:hover{transform:scale(1.008)}
/* When no photos: show a large avatar placeholder same height */
.no-photo-box{
  width:100%;height:460px;border-radius:var(--r);
  background:var(--brand-light);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:16px;box-shadow:var(--shadow-sm);
}
.avatar-initials{
  width:100px;height:100px;border-radius:50%;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  display:flex;align-items:center;justify-content:center;
  font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:800;color:#fff;
  box-shadow:0 8px 28px rgba(11,104,177,.3);
}
.no-photo-box span{color:var(--muted);font-size:.9rem}
.thumbs{display:flex;gap:10px;margin-top:12px;overflow-x:auto;padding-bottom:4px}
.thumbs::-webkit-scrollbar{height:4px}
.thumbs::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.thumb{width:90px;min-width:90px;height:66px;object-fit:cover;border-radius:10px;cursor:pointer;border:2.5px solid transparent;transition:border-color .2s,transform .2s;flex-shrink:0}
.thumb:hover{transform:scale(1.05)}
.thumb.active{border-color:var(--brand)}

/* ============================================================
   SECTION CARDS — identical to details.php
============================================================ */
.card-section{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:28px;
  margin-bottom:22px;box-shadow:var(--shadow-sm);
  animation:fadeSlideUp .6s cubic-bezier(.22,1,.36,1) both;
}
.card-section h3{
  font-family:'Playfair Display',serif;
  font-size:1.15rem;font-weight:700;color:var(--dark);
  margin-bottom:18px;padding-bottom:12px;
  border-bottom:2px solid var(--bg);
  display:flex;align-items:center;gap:9px;
}
.card-section h3 i{color:var(--brand);font-size:1rem}

/* Info grid — identical to details.php */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.info-item{background:var(--bg);border-radius:var(--r-sm);padding:14px 16px}
.info-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:4px}
.info-value{font-size:.95rem;font-weight:600;color:var(--dark)}
.info-value.available{color:var(--green)}
.info-value.unavailable{color:var(--red)}

/* Facility tags — reused as preference tags */
.facility-tags{display:flex;flex-wrap:wrap;gap:8px}
.facility-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--brand-light);color:var(--brand);
  border:1px solid rgba(11,104,177,.15);
  padding:7px 14px;border-radius:100px;
  font-size:.82rem;font-weight:600;
  transition:background .2s,transform .15s;
}
.facility-tag:hover{background:#cce4f8;transform:translateY(-1px)}
.facility-tag i{font-size:.8rem}

/* Description */
.description-text{color:var(--body);font-size:.95rem;line-height:1.85;font-weight:300}

/* ============================================================
   SIDEBAR — identical to details.php
============================================================ */
.sidebar{display:flex;flex-direction:column;gap:20px}

/* Contact card — same blue gradient as details.php */
.contact-card{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border-radius:var(--r);padding:26px;
  box-shadow:var(--shadow-md);
  position:sticky;top:92px;
}
.contact-card.unavail{background:linear-gradient(135deg,#374151,#1f2937)}
.contact-card-title{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#fff;margin-bottom:6px}
.contact-card-sub{color:rgba(255,255,255,.55);font-size:.82rem;margin-bottom:22px}
.contact-btns{display:flex;flex-direction:column;gap:10px}
.contact-wa{
  display:flex;align-items:center;justify-content:center;gap:10px;
  padding:14px;border-radius:var(--r-sm);
  background:#25D366;color:#fff;font-weight:700;font-size:.95rem;
  text-decoration:none;box-shadow:0 4px 16px rgba(37,211,102,.3);
  transition:background .2s,transform .2s,box-shadow .2s;
}
.contact-wa:hover{background:#1da851;transform:translateY(-2px);box-shadow:0 8px 28px rgba(37,211,102,.4);color:#fff}
.contact-call{
  display:flex;align-items:center;justify-content:center;gap:10px;
  padding:13px;border-radius:var(--r-sm);
  background:rgba(255,255,255,.12);color:#fff;
  border:1.5px solid rgba(255,255,255,.2);
  font-weight:600;font-size:.9rem;text-decoration:none;
  transition:background .2s,transform .2s;
}
.contact-call:hover{background:rgba(255,255,255,.2);transform:translateY(-1px);color:#fff}
.contact-num{text-align:center;margin-top:8px;font-size:.8rem;color:rgba(255,255,255,.4)}
.contact-lock{display:flex;flex-direction:column;align-items:center;gap:8px;padding:20px 0;text-align:center}
.contact-lock i{font-size:1.8rem;color:rgba(255,255,255,.3)}
.contact-lock p{color:rgba(255,255,255,.55);font-size:.85rem;line-height:1.6}
.contact-lock a{
  display:inline-flex;align-items:center;gap:6px;
  margin-top:6px;padding:11px 22px;border-radius:var(--r-sm);
  background:var(--accent);color:#fff;
  font-weight:700;font-size:.88rem;text-decoration:none;
  transition:background .2s,transform .2s;
}
.contact-lock a:hover{background:var(--accent-dark);transform:translateY(-1px)}

/* Posted by card — same as details.php */
.posted-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;box-shadow:var(--shadow-sm);
}
.posted-card h4{
  font-family:'Playfair Display',serif;
  font-size:1rem;font-weight:700;color:var(--dark);
  margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.posted-card h4 i{color:var(--brand);font-size:.9rem}
.posted-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.posted-icon{width:38px;height:38px;border-radius:10px;background:var(--brand-light);display:flex;align-items:center;justify-content:center;color:var(--brand);font-size:.9rem;flex-shrink:0}
.posted-info .label{font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;font-weight:600}
.posted-info .value{font-size:.9rem;font-weight:600;color:var(--dark)}

/* Safety card — same as details.php */
.safety-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;box-shadow:var(--shadow-sm);
}
.safety-card h4{
  font-family:'Playfair Display',serif;
  font-size:1rem;font-weight:700;color:var(--dark);
  margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.safety-card h4 i{color:var(--accent);font-size:.9rem}
.safety-list{list-style:none;display:flex;flex-direction:column;gap:8px}
.safety-list li{display:flex;align-items:flex-start;gap:10px;font-size:.86rem;color:var(--body);line-height:1.5}
.safety-list li::before{content:'✓';display:flex;align-items:center;justify-content:center;width:20px;height:20px;min-width:20px;background:var(--green-bg);color:var(--green);border-radius:50%;font-size:.68rem;font-weight:700;margin-top:1px}

/* Share card — same as details.php */
.share-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;box-shadow:var(--shadow-sm);
}
.share-card h4{font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:var(--dark);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.share-card h4 i{color:var(--brand);font-size:.9rem}
.share-btns{display:flex;gap:8px;flex-wrap:wrap}
.share-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:var(--r-sm);font-size:.82rem;font-weight:600;text-decoration:none;cursor:pointer;border:none;font-family:'DM Sans',sans-serif;transition:transform .15s}
.share-btn:hover{transform:translateY(-2px)}
.share-btn.wa{background:#dcfce7;color:#16a34a}
.share-btn.tw{background:#dbeafe;color:#1d4ed8}
.share-btn.cp{background:var(--bg);color:var(--brand);border:1.5px solid var(--border)}

/* ============================================================
   LIGHTBOX — identical to details.php
============================================================ */
.lightbox{display:none;position:fixed;z-index:2000;inset:0;background:rgba(0,0,0,.92);justify-content:center;align-items:center;animation:fadeIn .25s ease}
.lightbox.active{display:flex}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.lb-img{max-width:90vw;max-height:86vh;border-radius:var(--r);object-fit:contain;box-shadow:0 24px 80px rgba(0,0,0,.5);animation:lbIn .3s cubic-bezier(.22,1,.36,1) both}
@keyframes lbIn{from{opacity:0;transform:scale(.94)}to{opacity:1;transform:scale(1)}}
.lb-close{position:absolute;top:20px;right:24px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s}
.lb-close:hover{background:rgba(255,255,255,.2)}
.lb-nav{position:absolute;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s}
.lb-nav:hover{background:rgba(255,255,255,.22)}
.lb-prev{left:24px}.lb-next{right:24px}
.lb-counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.45);font-size:.8rem;letter-spacing:.5px}

/* ============================================================
   NOT FOUND — identical to details.php
============================================================ */
.not-found{text-align:center;padding:100px 24px}
.not-found i{font-size:4rem;color:var(--border);display:block;margin-bottom:20px}
.not-found h2{font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--dark);margin-bottom:10px}
.not-found p{color:var(--muted);margin-bottom:24px}

/* ============================================================
   FOOTER — identical to details.php
============================================================ */
footer{background:#090d13;padding:32px 24px;text-align:center;color:rgba(255,255,255,.28);font-size:.82rem;border-top:1px solid rgba(255,255,255,.04)}
footer a{color:rgba(255,255,255,.4);text-decoration:none}
footer a:hover{color:#fff}

/* ============================================================
   ANIMATIONS — identical to details.php
============================================================ */
.reveal{opacity:0;transform:translateY(20px);transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal-d1{transition-delay:.1s}.reveal-d2{transition-delay:.2s}.reveal-d3{transition-delay:.3s}
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* ============================================================
   RESPONSIVE — identical to details.php
============================================================ */
@media(max-width:960px){
  .page-body{grid-template-columns:1fr}
  .contact-card{position:static}
}
@media(max-width:768px){
  header{padding:0 18px;height:66px}
  .page-hero{padding:36px 20px 42px}
  .page-hero h1{font-size:1.7rem}
  .hero-price{font-size:1.8rem}
  .page-body{padding:24px 18px 60px;gap:20px}
  .main-photo,.no-photo-box{height:280px}
  .info-grid{grid-template-columns:1fr}
  .hero-meta{gap:10px}
}
@media(max-width:480px){
  .hero-price-row{flex-direction:column;align-items:flex-start}
}
  </style>
</head>
<body>

<!-- ============================================================
     HEADER
============================================================ -->
<header>
  <a href="index.php" class="logo">
    <img src="logo.png" alt="HostelConnect Logo">
    <span>HostelConnect</span>
  </a>
  <div class="header-right">
    <a href="roommates.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> All Roommates
    </a>
  </div>
</header>

<?php if ($roommate): ?>

<?php
  $isAvail     = $roommate['availability'] === 'available';
  $genderPref  = strtolower($roommate['gender_pref'] ?? '');
  $hasPhotos   = !empty($roommate['photos']);
?>

<!-- ============================================================
     HERO BANNER — same structure as details.php
============================================================ -->
<div class="page-hero">
  <div class="page-hero-orb o1"></div>
  <div class="page-hero-orb o2"></div>
  <div class="hero-content">

    <div class="hero-breadcrumb">
      <a href="index.php">Home</a>
      <i class="fas fa-chevron-right"></i>
      <a href="roommates.php">Roommates</a>
      <i class="fas fa-chevron-right"></i>
      <span><?= htmlspecialchars($roommate['campus']) ?></span>
    </div>

    <div class="hero-type-badge">
      <i class="fas fa-user-friends"></i>
      Roommate Request
    </div>

    <h1><?= htmlspecialchars($roommate['requester_name']) ?></h1>

    <div class="hero-meta">
      <div class="hero-meta-item">
        <i class="fas fa-graduation-cap"></i>
        <?= htmlspecialchars($roommate['campus']) ?>
      </div>
      <div class="hero-meta-item">
        <i class="fas fa-map-marker-alt"></i>
        <?= htmlspecialchars($roommate['area']) ?>
      </div>
      <div class="hero-meta-item">
        <i class="fas fa-eye"></i>
        <?= (int)($roommate['views'] ?? 0) ?> views
      </div>
      <?php if (!empty($roommate['room_type'])): ?>
      <div class="hero-meta-item">
        <i class="fas fa-door-open"></i>
        <?= htmlspecialchars($roommate['room_type']) ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="hero-price-row">
      <div class="hero-price">
        ₦<?= number_format($roommate['budget_min']) ?> – ₦<?= number_format($roommate['budget_max']) ?>
        <sub>/ budget range</sub>
      </div>
      <div class="avail-pill <?= $isAvail ? 'yes' : 'no' ?>">
        <i class="fas fa-<?= $isAvail ? 'check-circle' : 'times-circle' ?>"></i>
        <?= $isAvail ? 'Available Now' : 'Not Available' ?>
      </div>
      <div class="views-pill">
        <i class="fas fa-eye"></i> <?= (int)($roommate['views'] ?? 0) ?> views
      </div>
    </div>

  </div>
</div>

<!-- ============================================================
     PAGE BODY
============================================================ -->
<div class="page-body">

  <!-- ── MAIN COLUMN ── -->
  <div class="main-col">

    <!-- Gallery / Avatar -->
    <div class="gallery-wrap reveal">
      <?php if ($hasPhotos): ?>
        <img id="mainImage" class="main-photo"
             src="student/uploads/roommates/<?= htmlspecialchars($roommate['photos'][0]) ?>"
             alt="<?= htmlspecialchars($roommate['requester_name']) ?>"
             onclick="openLightbox(0)">
        <?php if (count($roommate['photos']) > 1): ?>
        <div class="thumbs">
          <?php foreach ($roommate['photos'] as $i => $img): ?>
            <img class="thumb <?= $i === 0 ? 'active' : '' ?>"
                 src="student/uploads/roommates/<?= htmlspecialchars($img) ?>"
                 alt="Photo <?= $i+1 ?>"
                 onclick="changeMainImage(<?= $i ?>)">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="no-photo-box">
          <div class="avatar-initials"><?= initials($roommate['requester_name']) ?></div>
          <span>No photos uploaded</span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Basic Info -->
    <div class="card-section reveal reveal-d1">
      <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Campus</div>
          <div class="info-value"><?= htmlspecialchars($roommate['campus']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Area / Location</div>
          <div class="info-value"><?= htmlspecialchars($roommate['area']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Room Type</div>
          <div class="info-value"><?= htmlspecialchars($roommate['room_type'] ?? 'Any') ?></div>
        </div>
        <?php if (!empty($roommate['move_in_date'])): ?>
        <div class="info-item">
          <div class="info-label">Move-in Date</div>
          <div class="info-value"><?= htmlspecialchars($roommate['move_in_date']) ?></div>
        </div>
        <?php endif; ?>
        <div class="info-item">
          <div class="info-label">Budget Range</div>
          <div class="info-value" style="color:var(--accent)">
            ₦<?= number_format($roommate['budget_min']) ?> – ₦<?= number_format($roommate['budget_max']) ?>
          </div>
        </div>
        <div class="info-item">
          <div class="info-label">Availability</div>
          <div class="info-value <?= $isAvail ? 'available' : 'unavailable' ?>">
            <?= $isAvail ? '✅ Available' : '❌ Not Available' ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Preferences — uses same facility-tags style as details.php -->
    <div class="card-section reveal reveal-d2">
      <h3><i class="fas fa-sliders-h"></i> Preferences</h3>
      <div class="facility-tags">
        <?php if (!empty($roommate['gender_pref'])): ?>
          <span class="facility-tag">
            <i class="fas fa-<?= $genderPref === 'female' ? 'venus' : ($genderPref === 'male' ? 'mars' : 'genderless') ?>"></i>
            <?= htmlspecialchars($roommate['gender_pref']) ?> preferred
          </span>
        <?php endif; ?>
        <?php if (!empty($roommate['religion_pref'])): ?>
          <span class="facility-tag">
            <i class="fas fa-praying-hands"></i>
            <?= htmlspecialchars($roommate['religion_pref']) ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($roommate['room_type'])): ?>
          <span class="facility-tag">
            <i class="fas fa-door-open"></i>
            <?= htmlspecialchars($roommate['room_type']) ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($roommate['move_in_date'])): ?>
          <span class="facility-tag">
            <i class="fas fa-calendar-check"></i>
            Move in: <?= htmlspecialchars($roommate['move_in_date']) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Description -->
    <?php if (!empty($roommate['description'])): ?>
    <div class="card-section reveal">
      <h3><i class="fas fa-align-left"></i> About Me</h3>
      <p class="description-text"><?= nl2br(htmlspecialchars($roommate['description'])) ?></p>
    </div>
    <?php endif; ?>

  </div><!-- /main-col -->

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">

    <!-- Contact card -->
    <?php if ($isAvail): ?>
    <div class="contact-card reveal">
      <div class="contact-card-title">Interested in connecting?</div>
      <div class="contact-card-sub">Reach out to this student directly</div>
      <?php if (isset($_SESSION['user_id']) && !empty($roommate['requester_contact'])): ?>
        <div class="contact-btns">
          <a class="contact-wa"
             href="https://wa.me/<?= htmlspecialchars($roommate['requester_contact']) ?>?text=Hi <?= urlencode($roommate['requester_name']) ?>, I found your roommate request on HostelConnect and I'm interested!"
             target="_blank" rel="noopener noreferrer">
            <i class="fab fa-whatsapp" style="font-size:1.2rem"></i> Chat on WhatsApp
          </a>
          <a class="contact-call" href="tel:<?= htmlspecialchars($roommate['requester_contact']) ?>">
            <i class="fas fa-phone"></i> Call Now
          </a>
          <div class="contact-num">
            <i class="fas fa-phone" style="font-size:.7rem;margin-right:4px"></i>
            <?= htmlspecialchars($roommate['requester_contact']) ?>
          </div>
        </div>
      <?php else: ?>
        <div class="contact-lock">
          <i class="fas fa-lock"></i>
          <p>Login to see contact details and connect with this student</p>
          <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
            <i class="fas fa-sign-in-alt"></i> Login to Connect
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="contact-card unavail reveal">
      <div class="contact-card-title">No Longer Available</div>
      <div class="contact-card-sub">This person has already found a roommate or is no longer accepting connections.</div>
      <div class="contact-lock">
        <i class="fas fa-ban" style="color:rgba(255,255,255,.2)"></i>
        <p>Browse other active requests below</p>
        <a href="roommates.php"><i class="fas fa-search"></i> Browse Roommates</a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Posted by — same style as details.php -->
    <div class="posted-card reveal reveal-d1">
      <h4><i class="fas fa-user-circle"></i> Request By</h4>
      <div class="posted-row">
        <div class="posted-icon"><i class="fas fa-user"></i></div>
        <div class="posted-info">
          <div class="label">Student</div>
          <div class="value"><?= htmlspecialchars($roommate['requester_name']) ?></div>
        </div>
      </div>
      <div class="posted-row">
        <div class="posted-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="posted-info">
          <div class="label">Campus</div>
          <div class="value"><?= htmlspecialchars($roommate['campus']) ?></div>
        </div>
      </div>
      <?php if (isset($_SESSION['user_id']) && !empty($roommate['requester_contact'])): ?>
      <div class="posted-row">
        <div class="posted-icon"><i class="fas fa-phone"></i></div>
        <div class="posted-info">
          <div class="label">Contact</div>
          <div class="value"><?= htmlspecialchars($roommate['requester_contact']) ?></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Safety tips — same style as details.php -->
    <div class="safety-card reveal reveal-d2">
      <h4><i class="fas fa-shield-alt"></i> Safety Tips</h4>
      <ul class="safety-list">
        <li>Always meet in a public place before committing</li>
        <li>Verify their identity before sharing personal info</li>
        <li>Discuss house rules and expectations clearly upfront</li>
        <li>Inspect the accommodation together before paying</li>
        <li>Trust your instincts — never ignore red flags</li>
      </ul>
    </div>

    <!-- Share — same style as details.php -->
    <div class="share-card reveal reveal-d3">
      <h4><i class="fas fa-share-alt"></i> Share This Request</h4>
      <div class="share-btns">
        <a class="share-btn wa"
           href="https://wa.me/?text=Check out this roommate request on HostelConnect: <?= urlencode('https://hostelconnect.com.ng' . $_SERVER['REQUEST_URI']) ?>"
           target="_blank" rel="noopener noreferrer">
          <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <a class="share-btn tw"
           href="https://twitter.com/intent/tweet?text=Looking for a student roommate? Check HostelConnect!&url=<?= urlencode('https://hostelconnect.com.ng' . $_SERVER['REQUEST_URI']) ?>"
           target="_blank" rel="noopener noreferrer">
          <i class="fab fa-twitter"></i> Tweet
        </a>
        <button class="share-btn cp" onclick="copyLink()">
          <i class="fas fa-link"></i> <span id="cpyTxt">Copy Link</span>
        </button>
      </div>
    </div>

  </aside>
</div><!-- /page-body -->

<?php else: ?>
<div class="not-found">
  <i class="fas fa-user-slash"></i>
  <h2>Roommate Not Found</h2>
  <p>This request may have been removed or the link is no longer valid.</p>
  <a href="roommates.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:var(--r-sm);background:var(--brand);color:#fff;text-decoration:none;font-weight:700;transition:background .2s">
    <i class="fas fa-arrow-left"></i> Back to Roommates
  </a>
</div>
<?php endif; ?>

<!-- ============================================================
     LIGHTBOX — identical to details.php
============================================================ -->
<div id="lightbox" class="lightbox" onclick="closeLightboxOutside(event)">
  <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <button class="lb-nav lb-prev" onclick="changePhoto(-1)"><i class="fas fa-chevron-left"></i></button>
  <img id="lightboxImg" class="lb-img" src="" alt="Photo">
  <button class="lb-nav lb-next" onclick="changePhoto(1)"><i class="fas fa-chevron-right"></i></button>
  <div class="lb-counter" id="lbCounter"></div>
</div>

<!-- ============================================================
     FOOTER — identical to details.php
============================================================ -->
<footer>
  <p>&copy; <?= date('Y') ?> HostelConnect &mdash; All rights reserved &nbsp;|&nbsp;
     <a href="/privacy.php">Privacy Policy</a> &nbsp;|&nbsp;
     <a href="roommates.php">Back to Roommates</a>
  </p>
</footer>

<!-- ============================================================
     SCRIPTS — all original logic preserved
============================================================ -->
<script>
let photos = <?= json_encode($roommate['photos'] ?? []) ?>;
let currentIndex = 0;

function changeMainImage(index) {
  currentIndex = index;
  const mi = document.getElementById('mainImage');
  if (mi) mi.src = 'student/uploads/roommates/' + photos[index];
  document.querySelectorAll('.thumb').forEach((t, i) => t.classList.toggle('active', i === index));
}

function openLightbox(index) {
  currentIndex = index;
  document.getElementById('lightboxImg').src = 'student/uploads/roommates/' + photos[currentIndex];
  updateLbCounter();
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}

function closeLightboxOutside(e) {
  if (e.target === document.getElementById('lightbox')) closeLightbox();
}

function changePhoto(step) {
  if (!photos.length) return;
  currentIndex = (currentIndex + step + photos.length) % photos.length;
  document.getElementById('lightboxImg').src = 'student/uploads/roommates/' + photos[currentIndex];
  updateLbCounter();
}

function updateLbCounter() {
  const el = document.getElementById('lbCounter');
  if (el) el.textContent = (currentIndex + 1) + ' / ' + photos.length;
}

document.addEventListener('keydown', e => {
  if (!document.getElementById('lightbox').classList.contains('active')) return;
  if (e.key === 'ArrowLeft')  changePhoto(-1);
  if (e.key === 'ArrowRight') changePhoto(1);
  if (e.key === 'Escape')     closeLightbox();
});

function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const el = document.getElementById('cpyTxt');
    el.textContent = 'Copied!';
    setTimeout(() => el.textContent = 'Copy Link', 2000);
  });
}

const ro = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
document.querySelectorAll('.reveal').forEach(el => ro.observe(el));
</script>
</body>
</html>