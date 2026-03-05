<?php
session_start();
require "config.php";

// Accept slug or id
$slug = $_GET['slug'] ?? null;
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch hostel (approved only)
if ($slug) {
    $stmt = $conn->prepare("SELECT * FROM hostels WHERE slug = ? AND status = 'approved' LIMIT 1");
    $stmt->bind_param("s", $slug);
} else {
    $stmt = $conn->prepare("SELECT * FROM hostels WHERE id = ? AND status = 'approved' LIMIT 1");
    $stmt->bind_param("i", $id);
}

$stmt->execute();
$result  = $stmt->get_result();
$listing = $result && $result->num_rows ? $result->fetch_assoc() : null;

// Track views
if ($listing && isset($_SESSION['user_id'])) {
    $hostelId = (int) $listing['id'];
    if (!isset($_SESSION['viewed_hostels'])) $_SESSION['viewed_hostels'] = [];
    if (!in_array($hostelId, $_SESSION['viewed_hostels'])) {
        $conn->query("UPDATE hostels SET views = views + 1 WHERE id = $hostelId");
        $_SESSION['viewed_hostels'][] = $hostelId;
    }
}

if ($listing) {
    // Facilities
    $listing['facilities'] = !empty($listing['facilities'])
        ? (json_last_error() === JSON_ERROR_NONE
            ? array_map('trim', json_decode($listing['facilities'], true))
            : array_map('trim', explode(",", $listing['facilities'])))
        : [];

    // Photos
    $listing['photos'] = $listing['photos'] ? json_decode($listing['photos'], true) : [];

    // Fetch feedback
    $feedbacks = [];
    $fstmt = $conn->prepare("
        SELECT f.*, u.full_name
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        WHERE f.hostel_id = ?
        ORDER BY f.created_at DESC
    ");
    $fstmt->bind_param("i", $listing['id']);
    $fstmt->execute();
    $fres = $fstmt->get_result();
    while ($row = $fres->fetch_assoc()) $feedbacks[] = $row;

    // Avg rating
    $avgRating = 0;
    if (!empty($feedbacks)) {
        $avgRating = round(array_sum(array_column($feedbacks, 'rating')) / count($feedbacks), 1);
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $listing ? htmlspecialchars($listing['title']) : "Not Found" ?> — HostelConnect</title>
  <meta name="description" content="<?= $listing ? htmlspecialchars(substr($listing['description'] ?? '', 0, 155)) : 'Hostel not found' ?>">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
  <style>
/* ============================================================
   RESET & VARIABLES  (same as index.php)
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
   HEADER
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
   PAGE HERO BANNER
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
.page-hero-orb{
  position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none;
}
.page-hero-orb.o1{width:400px;height:400px;background:radial-gradient(circle,rgba(207,108,23,.22) 0%,transparent 70%);top:-100px;right:-80px}
.page-hero-orb.o2{width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,.05) 0%,transparent 70%);bottom:-60px;left:5%}
.hero-content{position:relative;z-index:1;max-width:1180px;margin:0 auto}
.hero-breadcrumb{
  display:flex;align-items:center;gap:8px;
  font-size:.78rem;color:rgba(255,255,255,.5);
  margin-bottom:16px;letter-spacing:.3px;
}
.hero-breadcrumb a{color:rgba(255,255,255,.5);text-decoration:none;transition:color .2s}
.hero-breadcrumb a:hover{color:rgba(255,255,255,.9)}
.hero-breadcrumb i{font-size:.6rem}
.hero-type-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
  color:rgba(255,255,255,.9);font-size:.75rem;font-weight:600;
  padding:5px 14px;border-radius:100px;margin-bottom:14px;
  backdrop-filter:blur(8px);letter-spacing:.5px;text-transform:uppercase;
}
.page-hero h1{
  font-family:'Playfair Display',serif;
  font-size:clamp(1.8rem,4vw,2.8rem);
  font-weight:800;color:#fff;
  line-height:1.18;letter-spacing:-.8px;
  margin-bottom:16px;
  animation:fadeSlideUp .7s .1s cubic-bezier(.22,1,.36,1) both;
}
.hero-meta{
  display:flex;flex-wrap:wrap;align-items:center;gap:16px;
  animation:fadeSlideUp .7s .25s cubic-bezier(.22,1,.36,1) both;
}
.hero-meta-item{
  display:flex;align-items:center;gap:6px;
  color:rgba(255,255,255,.7);font-size:.87rem;
}
.hero-meta-item i{color:rgba(255,255,255,.5);font-size:.82rem}
.hero-price-row{
  margin-top:20px;
  display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap;
  animation:fadeSlideUp .7s .4s cubic-bezier(.22,1,.36,1) both;
}
.hero-price{
  font-family:'Playfair Display',serif;
  font-size:clamp(2rem,4vw,2.8rem);
  font-weight:800;color:#fbbf24;letter-spacing:-.5px;line-height:1;
}
.hero-price sub{
  font-family:'DM Sans',sans-serif;
  font-size:.95rem;font-weight:400;
  color:rgba(255,255,255,.55);letter-spacing:0;
}
.avail-pill{
  display:inline-flex;align-items:center;gap:6px;
  padding:6px 14px;border-radius:100px;
  font-size:.8rem;font-weight:700;
}
.avail-pill.yes{background:rgba(22,163,74,.2);color:#4ade80;border:1px solid rgba(22,163,74,.3)}
.avail-pill.no{background:rgba(220,38,38,.2);color:#f87171;border:1px solid rgba(220,38,38,.3)}
.views-pill{
  display:inline-flex;align-items:center;gap:6px;
  padding:6px 14px;border-radius:100px;
  background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);
  font-size:.78rem;border:1px solid rgba(255,255,255,.15);
}

/* ============================================================
   LAYOUT
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
   GALLERY
============================================================ */
.gallery-wrap{margin-bottom:28px}
.main-photo{
  width:100%;height:460px;object-fit:cover;
  border-radius:var(--r);cursor:zoom-in;
  box-shadow:var(--shadow-md);
  transition:transform .3s ease;
  display:block;
}
.main-photo:hover{transform:scale(1.008)}
.no-photo-box{
  width:100%;height:460px;border-radius:var(--r);
  background:var(--brand-light);display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  color:var(--muted);gap:12px;
}
.no-photo-box i{font-size:3rem;opacity:.25}
.thumbs{
  display:flex;gap:10px;margin-top:12px;
  overflow-x:auto;padding-bottom:4px;
}
.thumbs::-webkit-scrollbar{height:4px}
.thumbs::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.thumb{
  width:90px;min-width:90px;height:66px;
  object-fit:cover;border-radius:10px;cursor:pointer;
  border:2.5px solid transparent;
  transition:border-color .2s,transform .2s;
  flex-shrink:0;
}
.thumb:hover{transform:scale(1.05)}
.thumb.active{border-color:var(--brand)}

/* ============================================================
   SECTION CARDS
============================================================ */
.card-section{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:28px;
  margin-bottom:22px;
  box-shadow:var(--shadow-sm);
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

/* Info grid */
.info-grid{
  display:grid;grid-template-columns:1fr 1fr;gap:14px;
}
.info-item{background:var(--bg);border-radius:var(--r-sm);padding:14px 16px}
.info-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:4px}
.info-value{font-size:.95rem;font-weight:600;color:var(--dark)}
.info-value.available{color:var(--green)}
.info-value.unavailable{color:var(--red)}

/* Description */
.description-text{color:var(--body);font-size:.95rem;line-height:1.85;font-weight:300}

/* Facilities */
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

/* ============================================================
   FEEDBACK / REVIEWS
============================================================ */
.review-summary{
  display:flex;align-items:center;gap:20px;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border-radius:var(--r-sm);padding:20px 24px;
  margin-bottom:24px;
}
.review-score{
  text-align:center;
}
.review-score .big{
  font-family:'Playfair Display',serif;
  font-size:3rem;font-weight:800;color:#fff;line-height:1;
}
.review-score .out{font-size:.8rem;color:rgba(255,255,255,.5);margin-top:2px}
.review-stars-row{display:flex;gap:3px;margin:6px 0}
.review-stars-row i{color:#fbbf24;font-size:.9rem}
.review-count{color:rgba(255,255,255,.6);font-size:.78rem}
.review-divider{width:1px;background:rgba(255,255,255,.15);align-self:stretch}
.review-breakdown{flex:1}
.breakdown-row{display:flex;align-items:center;gap:10px;margin-bottom:5px}
.breakdown-label{font-size:.75rem;color:rgba(255,255,255,.6);width:30px;text-align:right}
.breakdown-bar{flex:1;height:6px;background:rgba(255,255,255,.12);border-radius:3px;overflow:hidden}
.breakdown-fill{height:100%;background:#fbbf24;border-radius:3px;transition:width .8s ease}
.breakdown-count{font-size:.72rem;color:rgba(255,255,255,.5);width:16px}

.feedback-item{
  padding:18px 0;border-bottom:1px solid var(--border);
}
.feedback-item:last-child{border-bottom:none}
.feedback-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;gap:12px}
.feedback-author-wrap{display:flex;align-items:center;gap:12px}
.feedback-avatar{
  width:42px;height:42px;border-radius:50%;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-family:'Playfair Display',serif;
  font-size:1.1rem;font-weight:700;flex-shrink:0;
}
.feedback-name{font-weight:700;font-size:.92rem;color:var(--dark)}
.feedback-date{font-size:.75rem;color:var(--muted);margin-top:1px}
.feedback-stars{display:flex;gap:2px}
.feedback-stars i{font-size:.82rem}
.feedback-text{color:var(--body);font-size:.9rem;line-height:1.7;font-weight:300}
.no-reviews{
  text-align:center;padding:40px 20px;color:var(--muted);
}
.no-reviews i{font-size:2.5rem;opacity:.2;display:block;margin-bottom:10px}

/* Feedback form */
.review-form-wrap{
  border-top:2px solid var(--bg);margin-top:24px;padding-top:24px;
}
.review-form-wrap h4{
  font-family:'Playfair Display',serif;
  font-size:1.05rem;font-weight:700;color:var(--dark);
  margin-bottom:16px;
}
.star-picker{display:flex;gap:6px;margin-bottom:16px;cursor:pointer}
.star-pick{font-size:1.8rem;color:var(--border);transition:color .15s,transform .15s;line-height:1}
.star-pick.lit{color:var(--yellow)}
.star-pick:hover{transform:scale(1.2)}
.form-textarea{
  width:100%;min-height:100px;
  padding:14px 16px;
  border:1.5px solid var(--border);
  border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;
  font-size:.9rem;color:var(--body);
  background:var(--bg);
  resize:vertical;outline:none;
  transition:border-color .2s,box-shadow .2s;
}
.form-textarea:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white)}
.submit-btn{
  margin-top:12px;
  display:inline-flex;align-items:center;gap:8px;
  padding:12px 26px;border:none;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;font-family:'DM Sans',sans-serif;
  font-weight:700;font-size:.9rem;cursor:pointer;
  box-shadow:0 3px 12px rgba(11,104,177,.25);
  transition:transform .2s,box-shadow .2s;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 6px 22px rgba(11,104,177,.38)}
.login-notice{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--bg);border:1.5px solid var(--border);
  border-radius:var(--r-sm);padding:12px 16px;
  color:var(--muted);font-size:.88rem;
}
.login-notice a{color:var(--brand);font-weight:600;text-decoration:none}
.login-notice a:hover{text-decoration:underline}

/* ============================================================
   SIDEBAR
============================================================ */
.sidebar{display:flex;flex-direction:column;gap:20px}

/* Contact card */
.contact-card{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border-radius:var(--r);padding:26px;
  box-shadow:var(--shadow-md);
  position:sticky;top:92px;
}
.contact-card.unavail{background:linear-gradient(135deg,#374151,#1f2937)}
.contact-card-title{
  font-family:'Playfair Display',serif;
  font-size:1.2rem;font-weight:800;color:#fff;
  margin-bottom:6px;
}
.contact-card-sub{color:rgba(255,255,255,.55);font-size:.82rem;margin-bottom:22px}
.contact-btns{display:flex;flex-direction:column;gap:10px}
.contact-wa{
  display:flex;align-items:center;justify-content:center;gap:10px;
  padding:14px;border-radius:var(--r-sm);
  background:#25D366;color:#fff;
  font-weight:700;font-size:.95rem;text-decoration:none;
  transition:background .2s,transform .2s,box-shadow .2s;
  box-shadow:0 4px 16px rgba(37,211,102,.3);
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
.contact-lock{
  display:flex;flex-direction:column;align-items:center;
  gap:8px;padding:20px 0;text-align:center;
}
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

/* Posted by */
.posted-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;
  box-shadow:var(--shadow-sm);
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
.agent-fee{
  display:flex;align-items:center;gap:8px;
  background:var(--accent-light);border:1px solid rgba(207,108,23,.15);
  border-radius:var(--r-sm);padding:10px 14px;
  font-size:.85rem;color:var(--accent-dark);font-weight:600;
  margin-top:12px;
}
.agent-fee i{color:var(--accent)}

/* Safety tips */
.safety-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;
  box-shadow:var(--shadow-sm);
}
.safety-card h4{
  font-family:'Playfair Display',serif;
  font-size:1rem;font-weight:700;color:var(--dark);
  margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.safety-card h4 i{color:var(--accent);font-size:.9rem}
.safety-list{list-style:none;display:flex;flex-direction:column;gap:8px}
.safety-list li{
  display:flex;align-items:flex-start;gap:10px;
  font-size:.86rem;color:var(--body);line-height:1.5;
}
.safety-list li::before{
  content:'✓';
  display:flex;align-items:center;justify-content:center;
  width:20px;height:20px;min-width:20px;
  background:var(--green-bg);color:var(--green);
  border-radius:50%;font-size:.68rem;font-weight:700;
  margin-top:1px;
}

/* Share row */
.share-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px;
  box-shadow:var(--shadow-sm);
}
.share-card h4{
  font-family:'Playfair Display',serif;
  font-size:1rem;font-weight:700;color:var(--dark);
  margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.share-card h4 i{color:var(--brand);font-size:.9rem}
.share-btns{display:flex;gap:8px;flex-wrap:wrap}
.share-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:9px 14px;border-radius:var(--r-sm);
  font-size:.82rem;font-weight:600;text-decoration:none;
  transition:transform .15s,box-shadow .15s;cursor:pointer;border:none;font-family:'DM Sans',sans-serif;
}
.share-btn:hover{transform:translateY(-2px)}
.share-btn.wa{background:#dcfce7;color:#16a34a}
.share-btn.tw{background:#dbeafe;color:#1d4ed8}
.share-btn.cp{background:var(--bg);color:var(--brand);border:1.5px solid var(--border)}

/* ============================================================
   LIGHTBOX
============================================================ */
.lightbox{
  display:none;position:fixed;z-index:2000;
  inset:0;background:rgba(0,0,0,.92);
  justify-content:center;align-items:center;
  animation:fadeIn .25s ease;
}
.lightbox.active{display:flex}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.lb-img{
  max-width:90vw;max-height:86vh;
  border-radius:var(--r);object-fit:contain;
  box-shadow:0 24px 80px rgba(0,0,0,.5);
  animation:lbIn .3s cubic-bezier(.22,1,.36,1) both;
}
@keyframes lbIn{from{opacity:0;transform:scale(.94)}to{opacity:1;transform:scale(1)}}
.lb-close{
  position:absolute;top:20px;right:24px;
  width:44px;height:44px;border-radius:50%;
  background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
  color:#fff;font-size:1.1rem;display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:background .2s;
}
.lb-close:hover{background:rgba(255,255,255,.2)}
.lb-nav{
  position:absolute;top:50%;transform:translateY(-50%);
  width:48px;height:48px;border-radius:50%;
  background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
  color:#fff;font-size:1.1rem;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:background .2s;
}
.lb-nav:hover{background:rgba(255,255,255,.22)}
.lb-prev{left:24px}
.lb-next{right:24px}
.lb-counter{
  position:absolute;bottom:20px;left:50%;transform:translateX(-50%);
  color:rgba(255,255,255,.5);font-size:.82rem;letter-spacing:.5px;
}

/* ============================================================
   COOKIE NOTICE
============================================================ */
#cookieNotice{
  position:fixed;bottom:20px;left:20px;right:20px;max-width:520px;
  background:var(--dark);color:rgba(255,255,255,.8);
  padding:20px 22px;border-radius:var(--r);
  box-shadow:0 12px 40px rgba(0,0,0,.4);
  display:none;z-index:9999;
  border:1px solid rgba(255,255,255,.08);
  animation:cookieIn .4s cubic-bezier(.22,1,.36,1) both;
}
@keyframes cookieIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
#cookieNotice p{font-size:.84rem;line-height:1.7;margin-bottom:12px}
#cookieNotice a{color:var(--accent);text-decoration:none;font-weight:600}
#cookieNotice a:hover{text-decoration:underline}
.cookie-btns{display:flex;gap:8px}
.cookie-accept{
  padding:9px 20px;border:none;border-radius:var(--r-sm);
  background:var(--brand);color:#fff;
  font-family:'DM Sans',sans-serif;font-weight:700;font-size:.85rem;
  cursor:pointer;transition:background .2s;
}
.cookie-accept:hover{background:var(--brand-dark)}
.cookie-decline{
  padding:9px 16px;border:1.5px solid rgba(255,255,255,.12);border-radius:var(--r-sm);
  background:transparent;color:rgba(255,255,255,.5);
  font-family:'DM Sans',sans-serif;font-size:.85rem;
  cursor:pointer;transition:border-color .2s;
}
.cookie-decline:hover{border-color:rgba(255,255,255,.25)}

/* ============================================================
   404 / NOT FOUND
============================================================ */
.not-found{
  text-align:center;padding:100px 24px;
}
.not-found i{font-size:4rem;color:var(--border);display:block;margin-bottom:20px}
.not-found h2{font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--dark);margin-bottom:10px}
.not-found p{color:var(--muted);margin-bottom:24px}

/* ============================================================
   FOOTER
============================================================ */
footer{
  background:#090d13;
  padding:32px 24px;
  text-align:center;
  color:rgba(255,255,255,.28);
  font-size:.82rem;
  border-top:1px solid rgba(255,255,255,.04);
}
footer a{color:rgba(255,255,255,.4);text-decoration:none}
footer a:hover{color:#fff}

/* ============================================================
   ANIMATIONS
============================================================ */
.reveal{opacity:0;transform:translateY(20px);transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal-d1{transition-delay:.1s}
.reveal-d2{transition-delay:.2s}
.reveal-d3{transition-delay:.3s}
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:960px){
  .page-body{grid-template-columns:1fr}
  .contact-card{position:static}
  .sidebar{flex-direction:column}
  /* On mobile put sidebar before reviews */
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
  .hero-meta-item{font-size:.8rem}
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
    <a href="index.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> All Listings
    </a>
  </div>
</header>

<?php if ($listing): ?>

<!-- ============================================================
     HERO BANNER
============================================================ -->
<div class="page-hero">
  <div class="page-hero-orb o1"></div>
  <div class="page-hero-orb o2"></div>
  <div class="hero-content">
    <div class="hero-breadcrumb">
      <a href="index.php">Home</a>
      <i class="fas fa-chevron-right"></i>
      <a href="index.php#listings">Listings</a>
      <i class="fas fa-chevron-right"></i>
      <span><?= htmlspecialchars($listing['city']) ?></span>
    </div>
    <div class="hero-type-badge">
      <i class="fas fa-door-open"></i>
      <?= htmlspecialchars($listing['type']) ?>
    </div>
    <h1><?= htmlspecialchars($listing['title']) ?></h1>
    <div class="hero-meta">
      <div class="hero-meta-item">
        <i class="fas fa-map-marker-alt"></i>
        <?= htmlspecialchars($listing['city']) ?>, <?= htmlspecialchars($listing['area']) ?>
      </div>
      <div class="hero-meta-item">
        <i class="fas fa-eye"></i>
        <?= (int)$listing['views'] ?> views
      </div>
      <?php if (!empty($feedbacks)): ?>
      <div class="hero-meta-item">
        <i class="fas fa-star" style="color:#fbbf24"></i>
        <?= $avgRating ?> (<?= count($feedbacks) ?> review<?= count($feedbacks) !== 1 ? 's' : '' ?>)
      </div>
      <?php endif; ?>
    </div>
    <div class="hero-price-row">
      <div class="hero-price">
        ₦<?= number_format($listing['price']) ?>
        <sub>/ <?= htmlspecialchars($listing['period']) ?></sub>
      </div>
      <div class="avail-pill <?= $listing['availability'] === 'available' ? 'yes' : 'no' ?>">
        <i class="fas fa-<?= $listing['availability'] === 'available' ? 'check-circle' : 'times-circle' ?>"></i>
        <?= $listing['availability'] === 'available' ? 'Available Now' : 'Not Available' ?>
      </div>
      <div class="views-pill">
        <i class="fas fa-eye"></i> <?= (int)$listing['views'] ?> views
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     MAIN BODY
============================================================ -->
<div class="page-body">

  <!-- ── MAIN COLUMN ── -->
  <div class="main-col">

    <!-- Gallery -->
    <div class="gallery-wrap reveal">
      <?php if (!empty($listing['photos'])): ?>
        <img id="mainImage" class="main-photo"
             src="listing/uploads/<?= htmlspecialchars($listing['photos'][0]) ?>"
             alt="<?= htmlspecialchars($listing['title']) ?>"
             onclick="openLightbox(0)">
        <?php if (count($listing['photos']) > 1): ?>
        <div class="thumbs" id="thumbRow">
          <?php foreach ($listing['photos'] as $i => $img): ?>
            <img class="thumb <?= $i === 0 ? 'active' : '' ?>"
                 src="listing/uploads/<?= htmlspecialchars($img) ?>"
                 alt="Photo <?= $i+1 ?>"
                 onclick="changeMainImage(<?= $i ?>)">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="no-photo-box">
          <i class="fas fa-image"></i>
          <span style="color:var(--muted);font-size:.9rem">No photos uploaded yet</span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Basic Info -->
    <div class="card-section reveal reveal-d1">
      <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">City</div>
          <div class="info-value"><?= htmlspecialchars($listing['city']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Area / Suburb</div>
          <div class="info-value"><?= htmlspecialchars($listing['area']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Room Type</div>
          <div class="info-value"><?= htmlspecialchars($listing['type']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Billing Period</div>
          <div class="info-value"><?= htmlspecialchars($listing['period']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Price</div>
          <div class="info-value" style="color:var(--accent)">₦<?= number_format($listing['price']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Availability</div>
          <div class="info-value <?= $listing['availability'] === 'available' ? 'available' : 'unavailable' ?>">
            <?= $listing['availability'] === 'available' ? '✅ Available' : '❌ Unavailable' ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Description -->
    <div class="card-section reveal reveal-d2">
      <h3><i class="fas fa-align-left"></i> Description</h3>
      <p class="description-text"><?= nl2br(htmlspecialchars($listing['description'])) ?></p>
    </div>

    <!-- Facilities -->
    <?php if (!empty($listing['facilities'])): ?>
    <div class="card-section reveal">
      <h3><i class="fas fa-th-large"></i> Facilities &amp; Amenities</h3>
      <div class="facility-tags">
        <?php
        $facilityIcons = [
          'wifi' => 'fa-wifi', 'water' => 'fa-tint', 'electricity' => 'fa-bolt',
          'light' => 'fa-bolt', 'security' => 'fa-shield-alt', 'fence' => 'fa-fence',
          'kitchen' => 'fa-utensils', 'bathroom' => 'fa-bath', 'toilet' => 'fa-toilet',
          'parking' => 'fa-car', 'gate' => 'fa-door-closed', 'borehole' => 'fa-water',
          'generator' => 'fa-plug', 'solar' => 'fa-sun', 'cctv' => 'fa-video',
          'ac' => 'fa-snowflake', 'air' => 'fa-snowflake', 'furnished' => 'fa-couch',
          'wardrobe' => 'fa-door-open', 'ceiling' => 'fa-fan', 'fan' => 'fa-fan',
        ];
        foreach ($listing['facilities'] as $f):
          $f = trim($f);
          $icon = 'fa-check-circle';
          foreach ($facilityIcons as $key => $ico) {
            if (stripos($f, $key) !== false) { $icon = $ico; break; }
          }
        ?>
        <span class="facility-tag">
          <i class="fas <?= $icon ?>"></i>
          <?= htmlspecialchars($f) ?>
        </span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Reviews -->
    <div class="card-section reveal">
      <h3><i class="fas fa-star"></i> Reviews &amp; Feedback</h3>

      <?php if (!empty($feedbacks)): ?>
        <!-- Summary bar -->
        <div class="review-summary">
          <div class="review-score">
            <div class="big"><?= $avgRating ?></div>
            <div class="review-stars-row">
              <?php for($i=1;$i<=5;$i++): ?>
                <i class="fas fa-star<?= $i <= round($avgRating) ? '' : ($i - $avgRating < 1 ? '-half-alt' : '') ?>"
                   style="color:<?= $i <= $avgRating ? '#fbbf24' : 'rgba(255,255,255,.2)' ?>"></i>
              <?php endfor; ?>
            </div>
            <div class="out">out of 5</div>
            <div class="review-count"><?= count($feedbacks) ?> review<?= count($feedbacks) !== 1 ? 's' : '' ?></div>
          </div>
          <div class="review-divider"></div>
          <div class="review-breakdown">
            <?php
            for($star = 5; $star >= 1; $star--):
              $cnt = count(array_filter($feedbacks, fn($f) => (int)$f['rating'] === $star));
              $pct = count($feedbacks) > 0 ? round($cnt / count($feedbacks) * 100) : 0;
            ?>
            <div class="breakdown-row">
              <span class="breakdown-label"><?= $star ?>★</span>
              <div class="breakdown-bar"><div class="breakdown-fill" style="width:<?= $pct ?>%"></div></div>
              <span class="breakdown-count"><?= $cnt ?></span>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Individual reviews -->
        <?php foreach ($feedbacks as $fb): ?>
        <div class="feedback-item">
          <div class="feedback-header">
            <div class="feedback-author-wrap">
              <div class="feedback-avatar"><?= strtoupper(substr($fb['full_name'], 0, 1)) ?></div>
              <div>
                <div class="feedback-name"><?= htmlspecialchars($fb['full_name']) ?></div>
                <div class="feedback-date"><?= date("M d, Y", strtotime($fb['created_at'])) ?></div>
              </div>
            </div>
            <div class="feedback-stars">
              <?php for($i=1;$i<=5;$i++): ?>
                <i class="fas fa-star" style="color:<?= $i <= (int)$fb['rating'] ? '#f59e0b' : 'var(--border)' ?>"></i>
              <?php endfor; ?>
            </div>
          </div>
          <p class="feedback-text"><?= nl2br(htmlspecialchars($fb['comment'])) ?></p>
        </div>
        <?php endforeach; ?>

      <?php else: ?>
        <div class="no-reviews">
          <i class="fas fa-comment-slash"></i>
          <p>No reviews yet — be the first to leave one!</p>
        </div>
      <?php endif; ?>

      <!-- Review form -->
      <div class="review-form-wrap">
        <?php if (isset($_SESSION['user_id'])): ?>
          <h4>Leave a Review</h4>
          <form action="submit_feedback.php" method="post" onsubmit="return validateFeedback()">
            <input type="hidden" name="slug" value="<?= htmlspecialchars($listing['slug']) ?>">
            <input type="hidden" name="rating" id="rating" value="0">
            <div class="star-picker" id="ratingContainer">
              <?php for($i=1;$i<=5;$i++): ?>
                <span class="star-pick" data-v="<?= $i ?>" onclick="setRating(<?= $i ?>)">&#9733;</span>
              <?php endfor; ?>
            </div>
            <textarea class="form-textarea" name="comment" id="comment" placeholder="Share your experience with this hostel…" required></textarea>
            <button type="submit" class="submit-btn">
              <i class="fas fa-paper-plane"></i> Submit Review
            </button>
          </form>
        <?php else: ?>
          <div class="login-notice">
            <i class="fas fa-lock" style="color:var(--muted)"></i>
            <span><a href="/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Login</a> to leave a review for this hostel</span>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /main-col -->

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">

    <!-- Contact / CTA card -->
    <?php if ($listing['availability'] === 'available'): ?>
    <div class="contact-card reveal">
      <div class="contact-card-title">Interested in this hostel?</div>
      <div class="contact-card-sub">Contact the <?= htmlspecialchars(ucfirst($listing['role'])) ?> directly</div>
      <?php if (isset($_SESSION['user_id']) && !empty($listing['contact'])): ?>
        <div class="contact-btns">
          <a class="contact-wa"
             href="https://wa.me/<?= htmlspecialchars($listing['contact']) ?>?text=Hi, I'm interested in your hostel: <?= urlencode($listing['title']) ?>"
             target="_blank" rel="noopener noreferrer">
            <i class="fab fa-whatsapp" style="font-size:1.2rem"></i> Chat on WhatsApp
          </a>
          <a class="contact-call" href="tel:<?= htmlspecialchars($listing['contact']) ?>">
            <i class="fas fa-phone"></i> Call Now
          </a>
        </div>
      <?php else: ?>
        <div class="contact-lock">
          <i class="fas fa-lock"></i>
          <p>Login to view contact details and reach the <?= htmlspecialchars(ucfirst($listing['role'])) ?></p>
          <a href="/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
            <i class="fas fa-sign-in-alt"></i> Login to Contact
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="contact-card unavail reveal">
      <div class="contact-card-title">Currently Unavailable</div>
      <div class="contact-card-sub">This hostel is no longer accepting new tenants at this time.</div>
      <div class="contact-lock">
        <i class="fas fa-ban" style="color:rgba(255,255,255,.2)"></i>
        <p>Check back later or browse other available listings</p>
        <a href="index.php#listings"><i class="fas fa-search"></i> Browse Listings</a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Posted by -->
    <div class="posted-card reveal reveal-d1">
      <h4><i class="fas fa-user-circle"></i> Posted By</h4>
      <div class="posted-row">
        <div class="posted-icon"><i class="fas fa-user"></i></div>
        <div class="posted-info">
          <div class="label"><?= htmlspecialchars(ucfirst($listing['role'])) ?></div>
          <div class="value"><?= htmlspecialchars($listing['full_name']) ?></div>
        </div>
      </div>
      <?php if (isset($_SESSION['user_id']) && !empty($listing['contact'])): ?>
      <div class="posted-row">
        <div class="posted-icon"><i class="fas fa-phone"></i></div>
        <div class="posted-info">
          <div class="label">Contact</div>
          <div class="value"><?= htmlspecialchars($listing['contact']) ?></div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($listing['role'] === 'agent' && !empty($listing['agentFee'])): ?>
        <div class="agent-fee">
          <i class="fas fa-receipt"></i>
          Agent Fee: ₦<?= number_format($listing['agentFee']) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Safety tips -->
    <div class="safety-card reveal reveal-d2">
      <h4><i class="fas fa-shield-alt"></i> Safety Tips</h4>
      <ul class="safety-list">
        <li>Always inspect the hostel in person before making any payment</li>
        <li>Meet the landlord or agent in a public place first</li>
        <li>Do not share sensitive personal information upfront</li>
        <li>Verify the landlord or agent's identity and credentials</li>
        <li>Use secure and traceable payment methods only</li>
      </ul>
    </div>

    <!-- Share -->
    <div class="share-card reveal reveal-d3">
      <h4><i class="fas fa-share-alt"></i> Share This Listing</h4>
      <div class="share-btns">
        <a class="share-btn wa"
           href="https://wa.me/?text=Check out this hostel on HostelConnect: <?= urlencode('https://hostelconnect.com.ng' . $_SERVER['REQUEST_URI']) ?>"
           target="_blank" rel="noopener noreferrer">
          <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <a class="share-btn tw"
           href="https://twitter.com/intent/tweet?text=Found a great hostel on HostelConnect!&url=<?= urlencode('https://hostelconnect.com.ng' . $_SERVER['REQUEST_URI']) ?>"
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
<!-- Not Found -->
<div class="not-found">
  <i class="fas fa-search"></i>
  <h2>Listing Not Found</h2>
  <p>This hostel may have been removed or the link is invalid.</p>
  <a href="index.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:var(--r-sm);background:var(--brand);color:#fff;text-decoration:none;font-weight:700;transition:background .2s">
    <i class="fas fa-arrow-left"></i> Back to Listings
  </a>
</div>
<?php endif; ?>

<!-- ============================================================
     LIGHTBOX
============================================================ -->
<div id="lightbox" class="lightbox" onclick="closeLightboxOutside(event)">
  <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <button class="lb-nav lb-prev" onclick="changePhoto(-1)"><i class="fas fa-chevron-left"></i></button>
  <img id="lightboxImg" class="lb-img" src="" alt="Photo">
  <button class="lb-nav lb-next" onclick="changePhoto(1)"><i class="fas fa-chevron-right"></i></button>
  <div class="lb-counter" id="lbCounter"></div>
</div>

<!-- ============================================================
     FOOTER
============================================================ -->
<footer>
  <p>&copy; <?= date('Y') ?> HostelConnect &mdash; All rights reserved &nbsp;|&nbsp;
     <a href="/privacy.php">Privacy Policy</a> &nbsp;|&nbsp;
     <a href="index.php">Back to Home</a>
  </p>
</footer>

<!-- ============================================================
     COOKIE NOTICE
============================================================ -->
<div id="cookieNotice">
  <p>We use cookies to improve your experience, track views, and keep you logged in.
     By using HostelConnect, you agree to our <a href="/privacy.php">Privacy Policy</a>.</p>
  <div class="cookie-btns">
    <button class="cookie-accept" onclick="acceptCookies()">Accept</button>
    <button class="cookie-decline" onclick="acceptCookies()">Decline</button>
  </div>
</div>

<!-- ============================================================
     SCRIPTS  —  ALL ORIGINAL LOGIC PRESERVED
============================================================ -->
<script>
/* ── Gallery ── */
let photos = <?= json_encode($listing['photos'] ?? []) ?>;
let currentIndex = 0;

function changeMainImage(index) {
  currentIndex = index;
  document.getElementById('mainImage').src = 'listing/uploads/' + photos[index];
  // update active thumb
  document.querySelectorAll('.thumb').forEach((t, i) => t.classList.toggle('active', i === index));
}

function openLightbox(index) {
  currentIndex = index;
  const lb = document.getElementById('lightbox');
  document.getElementById('lightboxImg').src = 'listing/uploads/' + photos[currentIndex];
  updateLbCounter();
  lb.classList.add('active');
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
  currentIndex = (currentIndex + step + photos.length) % photos.length;
  document.getElementById('lightboxImg').src = 'listing/uploads/' + photos[currentIndex];
  updateLbCounter();
}

function updateLbCounter() {
  document.getElementById('lbCounter').textContent = (currentIndex + 1) + ' / ' + photos.length;
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', e => {
  if (!document.getElementById('lightbox').classList.contains('active')) return;
  if (e.key === 'ArrowLeft')  changePhoto(-1);
  if (e.key === 'ArrowRight') changePhoto(1);
  if (e.key === 'Escape')     closeLightbox();
});

/* ── Star rating ── */
function setRating(value) {
  document.getElementById('rating').value = value;
  document.querySelectorAll('.star-pick').forEach((s, i) => {
    s.classList.toggle('lit', i < value);
  });
}

// Hover preview
document.querySelectorAll('.star-pick').forEach(s => {
  s.addEventListener('mouseenter', () => {
    const v = parseInt(s.dataset.v);
    document.querySelectorAll('.star-pick').forEach((x, i) => x.style.color = i < v ? 'var(--yellow)' : '');
  });
  s.addEventListener('mouseleave', () => {
    const sel = parseInt(document.getElementById('rating').value);
    document.querySelectorAll('.star-pick').forEach((x, i) => {
      x.style.color = '';
      x.classList.toggle('lit', i < sel);
    });
  });
});

/* ── Feedback validation (unchanged logic) ── */
function validateFeedback() {
  const rating  = parseInt(document.getElementById('rating').value);
  const comment = document.getElementById('comment').value.trim();
  const box     = document.getElementById('ratingContainer');
  box.style.outline = 'none';
  if (rating < 1 || rating > 5) {
    box.style.outline = '2px solid var(--red)';
    box.style.borderRadius = '8px';
    alert('Please select a star rating before submitting.');
    return false;
  }
  if (!comment) {
    alert('Please enter a comment.');
    return false;
  }
  return true;
}

/* ── Cookie notice ── */
if (!localStorage.getItem('cookiesAccepted')) {
  document.getElementById('cookieNotice').style.display = 'block';
}
function acceptCookies() {
  localStorage.setItem('cookiesAccepted', 'yes');
  document.getElementById('cookieNotice').style.display = 'none';
}

/* ── Copy link ── */
function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const el = document.getElementById('cpyTxt');
    el.textContent = 'Copied!';
    setTimeout(() => el.textContent = 'Copy Link', 2000);
  });
}

/* ── Scroll reveal ── */
const ro = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.07, rootMargin: '0px 0px -30px 0px' });
document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

/* ── Animate breakdown bars on reveal ── */
const barObserver = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.querySelectorAll('.breakdown-fill').forEach(b => {
        const w = b.style.width;
        b.style.width = '0';
        setTimeout(() => b.style.width = w, 100);
      });
    }
  });
}, { threshold: 0.3 });
const reviewSummary = document.querySelector('.review-summary');
if (reviewSummary) barObserver.observe(reviewSummary);
</script>
</body>
</html>