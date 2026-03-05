<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Student Hostels | HostelConnect</title>
  <meta name="description" content="HostelConnect helps students find safe, affordable, and verified hostels near their campuses. Browse listings, connect with landlords, or find a roommate today.">
  <meta name="keywords" content="student hostels, hostel accommodation, cheap hostels, student housing, roommate finder, landlord listing">
  <meta name="robots" content="index, follow">
  <meta property="og:title" content="HostelConnect - Student Hostel Finder">
  <meta property="og:description" content="Find affordable student hostels, connect with landlords, or discover roommates easily.">
  <meta property="og:image" content="hostelconnect.com.ng/logo.png">
  <meta property="og:url" content="https://hostelconnect.com.ng/hostel.php">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="HostelConnect - Find Student Hostels">
  <meta name="twitter:description" content="Affordable student hostels and roommate finder.">
  <meta name="twitter:image" content="https://hostelconnect.com.ng/logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="/logo.png">
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","url":"https://hostelconnect.com.ng","logo":"https://hostelconnect.com.ng/logo.png","name":"HostelConnect"}</script>

  <style>
/* ============================================================
   RESET & VARIABLES
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
  --shadow-sm:0 2px 12px rgba(11,104,177,.07);
  --shadow-md:0 8px 32px rgba(11,104,177,.12);
  --shadow-lg:0 20px 60px rgba(11,104,177,.18);
  --r:16px;
  --r-sm:10px;
}
html{scroll-padding-top:76px;scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);line-height:1.65;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--brand);border-radius:10px}

/* ============================================================
   HEADER
============================================================ */
header{
  display:flex;justify-content:space-between;align-items:center;
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
nav ul{display:flex;list-style:none;gap:2px;align-items:center}
nav ul li a{color:var(--body);font-weight:500;font-size:.88rem;text-decoration:none;padding:8px 13px;border-radius:8px;transition:color .2s,background .2s;white-space:nowrap}
nav ul li a:hover{color:var(--brand);background:var(--brand-light)}
nav ul li a.nav-cta{background:var(--brand);color:#fff!important;padding:9px 18px;border-radius:9px;font-weight:600;margin-left:4px;transition:background .2s,transform .15s,box-shadow .2s}
nav ul li a.nav-cta:hover{background:var(--brand-dark);transform:translateY(-1px);box-shadow:0 4px 16px rgba(11,104,177,.3)}
nav ul li a.active{color:var(--brand);font-weight:600;background:var(--brand-light)}
.menu-toggle{display:none;font-size:1.3rem;cursor:pointer;color:var(--brand);width:42px;height:42px;border-radius:10px;background:var(--brand-light);border:1.5px solid rgba(11,104,177,.15);align-items:center;justify-content:center;transition:transform .25s}
.menu-toggle.open{transform:rotate(90deg)}

/* ============================================================
   PAGE HERO / SEARCH BANNER
============================================================ */
.search-hero{
  position:relative;
  background:linear-gradient(140deg,#0b68b1 0%,#07438e 50%,#03295a 100%);
  padding:56px 24px 64px;
  overflow:hidden;
  text-align:center;
}
.search-hero::before{content:'';position:absolute;inset:0;background:url('bg.jpg') center/cover no-repeat;opacity:.07;z-index:0}
.sh-orb{position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none;z-index:0}
.sh-orb.o1{width:500px;height:500px;background:radial-gradient(circle,rgba(207,108,23,.22) 0%,transparent 70%);top:-150px;right:-100px;animation:orbDrift 12s ease-in-out infinite alternate}
.sh-orb.o2{width:350px;height:350px;background:radial-gradient(circle,rgba(255,255,255,.05) 0%,transparent 70%);bottom:-80px;left:5%;animation:orbDrift 18s ease-in-out infinite alternate-reverse}
@keyframes orbDrift{from{transform:translate(0,0) scale(1)}to{transform:translate(30px,20px) scale(1.08)}}

.sh-inner{position:relative;z-index:1;max-width:700px;margin:0 auto}
.sh-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.85);font-size:.76rem;font-weight:600;padding:6px 16px;border-radius:100px;margin-bottom:20px;backdrop-filter:blur(8px);letter-spacing:.5px;text-transform:uppercase;animation:fadeSlideUp .7s .05s cubic-bezier(.22,1,.36,1) both}
.sh-inner h1{font-family:'Playfair Display',serif;font-size:clamp(1.9rem,5vw,3rem);font-weight:800;color:#fff;line-height:1.15;letter-spacing:-1px;margin-bottom:12px;animation:fadeSlideUp .7s .15s cubic-bezier(.22,1,.36,1) both}
.sh-inner h1 em{font-style:italic;color:#fbbf24}
.sh-inner p{color:rgba(255,255,255,.65);font-size:.97rem;margin-bottom:34px;font-weight:300;animation:fadeSlideUp .7s .25s cubic-bezier(.22,1,.36,1) both}

/* Inline search bar inside hero */
.sh-search{
  display:flex;max-width:560px;margin:0 auto;
  background:rgba(255,255,255,.96);
  border-radius:14px;padding:6px;
  box-shadow:0 20px 60px rgba(0,0,0,.25);
  animation:fadeSlideUp .7s .35s cubic-bezier(.22,1,.36,1) both;
}
.sh-search:focus-within{box-shadow:0 20px 60px rgba(0,0,0,.3),0 0 0 3px rgba(207,108,23,.3)}
.sh-search input{flex:1;padding:13px 18px;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.93rem;color:var(--dark);outline:none;min-width:0}
.sh-search input::placeholder{color:var(--muted)}
.sh-search button{padding:12px 24px;border:none;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.88rem;cursor:pointer;display:flex;align-items:center;gap:7px;white-space:nowrap;transition:transform .2s,box-shadow .2s;box-shadow:0 4px 14px rgba(207,108,23,.35)}
.sh-search button:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(207,108,23,.45)}

/* Stats row */
.sh-stats{display:flex;justify-content:center;gap:0;margin-top:40px;animation:fadeSlideUp .7s .5s cubic-bezier(.22,1,.36,1) both}
.sh-stat{text-align:center;padding:0 28px;position:relative}
.sh-stat:not(:last-child)::after{content:'';position:absolute;right:0;top:10%;bottom:10%;width:1px;background:rgba(255,255,255,.18)}
.sh-stat strong{display:block;font-size:1.6rem;font-weight:800;color:#fff;font-family:'Playfair Display',serif;line-height:1;margin-bottom:3px}
.sh-stat span{font-size:.72rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px}

/* ============================================================
   MAIN LAYOUT (sidebar + grid)
============================================================ */
.page-wrap{
  max-width:1260px;
  margin:0 auto;
  padding:40px 24px 80px;
  display:grid;
  grid-template-columns:280px 1fr;
  gap:28px;
  align-items:start;
}

/* ============================================================
   FILTER SIDEBAR
============================================================ */
.filter-sidebar{
  background:var(--white);
  border:1px solid var(--border);
  border-radius:var(--r);
  padding:24px;
  box-shadow:var(--shadow-sm);
  position:sticky;
  top:92px;
  animation:fadeSlideUp .6s .1s cubic-bezier(.22,1,.36,1) both;
}
.filter-sidebar h3{
  font-family:'Playfair Display',serif;
  font-size:1.1rem;font-weight:700;color:var(--dark);
  margin-bottom:20px;
  display:flex;align-items:center;justify-content:space-between;
}
.filter-clear{
  font-family:'DM Sans',sans-serif;
  font-size:.75rem;font-weight:600;color:var(--accent);
  cursor:pointer;background:none;border:none;padding:0;
  transition:color .2s;
}
.filter-clear:hover{color:var(--accent-dark)}
.filter-group{margin-bottom:18px}
.filter-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);margin-bottom:8px;display:block}
.filter-input{
  width:100%;padding:10px 13px;
  border:1.5px solid var(--border);border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;font-size:.87rem;color:var(--body);
  background:var(--bg);outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;
}
.filter-input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white)}
.filter-row{display:grid;grid-template-columns:1fr 1fr;gap:8px}

/* Type chips */
.type-chips{display:flex;flex-wrap:wrap;gap:6px}
.type-chip{
  padding:6px 12px;border-radius:100px;
  font-size:.78rem;font-weight:600;cursor:pointer;
  border:1.5px solid var(--border);
  background:var(--bg);color:var(--body);
  transition:all .2s;user-select:none;
}
.type-chip:hover{border-color:var(--brand);color:var(--brand);background:var(--brand-light)}
.type-chip.active{background:var(--brand);color:#fff;border-color:var(--brand)}

/* Sort select */
.sort-row{display:flex;align-items:center;gap:8px;margin-top:4px}
.sort-row label{font-size:.82rem;color:var(--muted);white-space:nowrap}

/* Active filter pills */
.active-filters{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;min-height:0}
.af-pill{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--brand-light);color:var(--brand);
  border:1px solid rgba(11,104,177,.2);
  font-size:.75rem;font-weight:600;
  padding:4px 10px;border-radius:100px;
}
.af-pill button{background:none;border:none;cursor:pointer;color:var(--brand);font-size:.8rem;line-height:1;padding:0;margin:0;display:flex;align-items:center}

/* ============================================================
   RESULTS PANEL
============================================================ */
.results-panel{animation:fadeSlideUp .6s .15s cubic-bezier(.22,1,.36,1) both}
.results-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:22px;flex-wrap:wrap;gap:12px;
}
.results-count{
  font-size:.9rem;color:var(--muted);
}
.results-count strong{color:var(--dark);font-weight:700}
.view-toggle{display:flex;gap:6px}
.vt-btn{
  width:36px;height:36px;border-radius:8px;
  border:1.5px solid var(--border);
  background:var(--white);color:var(--muted);
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:.88rem;transition:all .2s;
}
.vt-btn.active,.vt-btn:hover{background:var(--brand);color:#fff;border-color:var(--brand)}

/* ============================================================
   GRID & CARDS
============================================================ */
#grid{
  display:grid;
  gap:22px;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  transition:all .3s;
}
#grid.list-view{grid-template-columns:1fr}

.card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s;
  position:relative;
  animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.card:hover{transform:translateY(-7px);box-shadow:var(--shadow-lg)}

/* List view card adjustments */
#grid.list-view .card{display:grid;grid-template-columns:260px 1fr}
#grid.list-view .cover{height:100%;min-height:180px}
#grid.list-view .card-body{display:flex;flex-direction:column;justify-content:space-between}

.cover{height:200px;background:var(--brand-light);overflow:hidden;position:relative}
.cover img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.card:hover .cover img{transform:scale(1.07)}
.cover .no-photo{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--muted);font-size:.82rem;gap:8px}
.cover .no-photo i{font-size:2.2rem;opacity:.22}
.card-type-badge{position:absolute;top:12px;left:12px;background:rgba(11,104,177,.88);color:#fff;font-size:.68rem;font-weight:700;padding:4px 10px;border-radius:100px;backdrop-filter:blur(6px);letter-spacing:.5px;text-transform:uppercase}
.card-new-badge{position:absolute;top:12px;right:12px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-size:.66rem;font-weight:700;padding:4px 10px;border-radius:100px;letter-spacing:.4px;text-transform:uppercase}

.card-body{padding:18px}
.price{font-weight:800;color:var(--accent);font-size:1.15rem;margin-bottom:5px;font-family:'Playfair Display',serif}
.price .per{font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:400;color:var(--muted)}
.card-title{font-weight:600;color:var(--dark);font-size:.97rem;margin-bottom:6px;line-height:1.4}
.card-loc{display:flex;align-items:center;gap:5px;color:var(--muted);font-size:.82rem;margin-bottom:10px}
.card-loc i{color:var(--brand);font-size:.76rem}
.avail-badge{display:inline-flex;align-items:center;gap:4px;font-size:.74rem;font-weight:700;padding:4px 10px;border-radius:100px;margin-bottom:10px}
.avail-badge.yes{background:var(--green-bg);color:var(--green)}
.avail-badge.no{background:var(--red-bg);color:var(--red)}
.tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px}
.tag{font-size:.72rem;border:1px solid var(--border);padding:3px 9px;border-radius:100px;color:var(--body);background:var(--bg)}
.actions{display:flex;gap:8px}
.btn,.ghost{
  flex:1;text-align:center;padding:10px 14px;border-radius:var(--r-sm);
  font-size:.85rem;font-weight:600;text-decoration:none;
  transition:transform .2s,box-shadow .2s,background .2s;
  cursor:pointer;border:none;font-family:'DM Sans',sans-serif;
  display:inline-flex;align-items:center;justify-content:center;gap:6px;
}
.btn{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:white;box-shadow:0 3px 10px rgba(11,104,177,.22)}
.btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(11,104,177,.35)}
.ghost{background:var(--brand-light);color:var(--brand);border:1.5px solid rgba(11,104,177,.15)}
.ghost:hover{background:#cce4f8;transform:translateY(-1px)}

/* Empty state */
.empty-state{
  grid-column:1/-1;
  text-align:center;padding:80px 20px;
}
.empty-state i{font-size:3.5rem;color:var(--border);display:block;margin-bottom:18px}
.empty-state h3{font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--dark);margin-bottom:8px}
.empty-state p{color:var(--muted);font-size:.9rem;margin-bottom:20px}
.empty-state button{
  padding:11px 24px;border:none;border-radius:var(--r-sm);
  background:var(--brand);color:#fff;
  font-family:'DM Sans',sans-serif;font-weight:700;font-size:.9rem;
  cursor:pointer;transition:background .2s;
}
.empty-state button:hover{background:var(--brand-dark)}

/* Loading skeleton */
.skeleton-grid{display:grid;gap:22px;grid-template-columns:repeat(auto-fill,minmax(260px,1fr))}
.skeleton-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);overflow:hidden}
.skel-img{height:200px;background:linear-gradient(90deg,var(--bg) 25%,var(--border) 50%,var(--bg) 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
.skel-body{padding:18px}
.skel-line{height:12px;border-radius:6px;margin-bottom:10px;background:linear-gradient(90deg,var(--bg) 25%,var(--border) 50%,var(--bg) 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
.skel-line.w60{width:60%}
.skel-line.w40{width:40%}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* ============================================================
   PAGINATION
============================================================ */
#pagination{display:flex;justify-content:center;align-items:center;gap:8px;margin-top:48px;flex-wrap:wrap}
#pagination button{
  cursor:pointer;padding:10px 20px;
  border-radius:var(--r-sm);
  border:1.5px solid var(--border);
  background:var(--white);color:var(--brand);
  font-family:'DM Sans',sans-serif;font-weight:600;font-size:.86rem;
  transition:all .2s;
}
#pagination button:hover:not(:disabled){background:var(--brand);color:#fff;border-color:var(--brand);transform:translateY(-1px);box-shadow:0 4px 14px rgba(11,104,177,.22)}
#pagination button:disabled{opacity:.38;cursor:not-allowed}
#pagination button.active{background:var(--accent);color:white;border-color:var(--accent)}
#pagination span{color:var(--muted);font-size:.85rem;padding:0 4px}

/* ============================================================
   FLOATING WHATSAPP
============================================================ */
.float-wa{
  position:fixed;bottom:30px;right:28px;z-index:990;
  display:flex;align-items:center;gap:9px;
  background:#25D366;color:#fff;
  border-radius:100px;padding:12px 18px 12px 14px;
  text-decoration:none;font-weight:700;font-size:.86rem;
  box-shadow:0 6px 28px rgba(37,211,102,.4);
  transition:transform .25s,box-shadow .25s;
  animation:floatIn .6s 2s cubic-bezier(.22,1,.36,1) both;
}
.float-wa:hover{transform:translateY(-4px) scale(1.04);box-shadow:0 12px 40px rgba(37,211,102,.55);color:#fff}
.float-wa i{font-size:1.3rem}
.float-wa .wa-ping{position:absolute;top:-4px;right:-4px;width:13px;height:13px;border-radius:50%;background:#ff4b4b;border:2px solid #fff;animation:pingDot 2s infinite}
@keyframes pingDot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}
@keyframes floatIn{from{opacity:0;transform:translateY(20px) scale(.9)}to{opacity:1;transform:translateY(0) scale(1)}}

/* ============================================================
   FOOTER
============================================================ */
footer{background:#090d13;padding:60px 24px 28px;font-family:'DM Sans',sans-serif}
.footer-inner{max-width:1100px;margin:0 auto}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;padding-bottom:48px;border-bottom:1px solid rgba(255,255,255,.07);margin-bottom:28px}
.footer-brand .brand-name{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:#fff;display:block;margin-bottom:12px}
.footer-brand p{font-size:.85rem;line-height:1.75;color:rgba(255,255,255,.36);max-width:240px;margin-bottom:20px}
.footer-socials{display:flex;gap:8px}
.footer-socials a{width:35px;height:35px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.45);font-size:.82rem;text-decoration:none;transition:background .2s,color .2s,transform .2s}
.footer-socials a:hover{background:var(--brand);color:#fff;border-color:var(--brand);transform:translateY(-2px)}
.footer-col h4{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.8);margin-bottom:16px}
.footer-col ul{list-style:none}
.footer-col ul li{margin-bottom:10px}
.footer-col ul li a{color:rgba(255,255,255,.36);text-decoration:none;font-size:.86rem;transition:color .2s}
.footer-col ul li a:hover{color:#fff}
.footer-contact-line{display:flex;align-items:flex-start;gap:8px;font-size:.83rem;color:rgba(255,255,255,.36);margin-bottom:9px}
.footer-contact-line i{color:var(--brand);font-size:.83rem;margin-top:2px;width:14px;flex-shrink:0}
.footer-bottom{display:flex;justify-content:space-between;align-items:center;font-size:.78rem;color:rgba(255,255,255,.2);flex-wrap:wrap;gap:8px}

/* ============================================================
   ANIMATIONS
============================================================ */
.reveal{opacity:0;transform:translateY(20px);transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:translateY(0)}
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:1024px){
  .page-wrap{grid-template-columns:1fr}
  .filter-sidebar{position:static;display:grid;grid-template-columns:repeat(2,1fr);gap:16px;padding:20px}
  .filter-sidebar h3{grid-column:1/-1}
  .active-filters{grid-column:1/-1}
  .footer-top{grid-template-columns:1fr 1fr;gap:28px}
}
@media(max-width:768px){
  header{padding:0 18px;height:66px}
  nav ul{display:none;flex-direction:column;position:fixed;top:66px;left:0;right:0;background:rgba(255,255,255,.97);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);padding:16px 18px 24px;box-shadow:0 12px 40px rgba(0,0,0,.12);z-index:998;gap:4px}
  nav ul.show{display:flex}
  nav ul li a{padding:12px 16px;font-size:.93rem}
  .menu-toggle{display:flex}
  .search-hero{padding:40px 18px 50px}
  .sh-search{flex-direction:column;border-radius:14px;padding:8px;gap:6px}
  .sh-search input,.sh-search button{border-radius:9px}
  .sh-search button{justify-content:center}
  .sh-stats{gap:0;flex-wrap:wrap}
  .sh-stat{padding:0 16px;flex:0 0 45%}
  .sh-stat::after{display:none}
  .page-wrap{padding:24px 16px 60px;gap:16px}
  .filter-sidebar{grid-template-columns:1fr;padding:18px}
  #grid.list-view .card{grid-template-columns:1fr}
  #grid.list-view .cover{height:180px}
  .footer-top{grid-template-columns:1fr;gap:24px}
  .footer-bottom{flex-direction:column;text-align:center}
  .float-wa .wa-label{display:none}
  .float-wa{padding:13px;border-radius:50%}
}
  </style>
</head>
<body>

<!-- FLOATING WHATSAPP -->
<a href="https://chat.whatsapp.com/GLWo3a93fVK2Ws3EClxfDL" target="_blank" rel="noopener noreferrer" class="float-wa" aria-label="Chat on WhatsApp">
  <i class="fab fa-whatsapp"></i>
  <span class="wa-label">Need help?</span>
  <span class="wa-ping"></span>
</a>

<!-- ============================================================
     HEADER
============================================================ -->
<header>
  <a href="index.php" class="logo">
    <img src="logo.png" alt="HostelConnect Logo">
    <span>HostelConnect</span>
  </a>
  <nav>
    <ul class="nav-links" id="navMenu">
      <li><a href="index.php">Home</a></li>
      <li><a href="hostel.php" class="active">Find Hostels</a></li>
      <li><a href="roommates.php">Find Roommates</a></li>
      <li><a href="register.php">List Your Hostel</a></li>
      <li><a href="index.php#contact">Contact</a></li>
      <?php if($isLoggedIn): ?>
        <?php if($_SESSION['role'] === 'student'): ?>
          <li><a href="student/student_dashboard.php">Dashboard</a></li>
        <?php elseif($_SESSION['role'] === 'agent' || $_SESSION['role'] === 'landlord'): ?>
          <li><a href="listing/dashboard.php">Dashboard</a></li>
        <?php elseif($_SESSION['role'] === 'admin'): ?>
          <li><a href="admin/dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <li><a href="logout.php" class="nav-cta">Logout</a></li>
      <?php else: ?>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php" class="nav-cta">Login</a></li>
      <?php endif; ?>
    </ul>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">☰</button>
  </nav>
</header>

<!-- ============================================================
     SEARCH HERO BANNER
============================================================ -->
<div class="search-hero">
  <div class="sh-orb o1"></div>
  <div class="sh-orb o2"></div>
  <div class="sh-inner">
    <div class="sh-tag"><i class="fas fa-building"></i> Hostel Listings</div>
    <h1>Find Your <em>Perfect</em> Hostel</h1>
    <p>Search from hundreds of verified, affordable student hostels near your campus</p>
    <div class="sh-search">
      <input type="text" id="searchInput" placeholder="Search by city, area or hostel name…" autocomplete="off">
      <button onclick="applyFilters()"><i class="fas fa-search"></i> Search</button>
    </div>
    <div class="sh-stats">
      <div class="sh-stat"><strong id="totalCount">—</strong><span>Total Listings</span></div>
      <div class="sh-stat"><strong id="availCount">—</strong><span>Available Now</span></div>
      <div class="sh-stat"><strong>20+</strong><span>Cities</span></div>
    </div>
  </div>
</div>

<!-- ============================================================
     PAGE BODY
============================================================ -->
<div class="page-wrap">

  <!-- ── FILTER SIDEBAR ── -->
  <aside class="filter-sidebar" id="filterSidebar">
    <h3>
      <span><i class="fas fa-sliders-h" style="color:var(--brand);margin-right:8px;font-size:.95rem"></i>Filters</span>
      <button class="filter-clear" onclick="clearAllFilters()">Clear all</button>
    </h3>

    <!-- Active filter pills -->
    <div class="active-filters" id="activePills"></div>

    <div class="filter-group">
      <label class="filter-label" for="city">City</label>
      <select class="filter-input" id="city">
        <option value="">All Cities</option>
      </select>
    </div>

    <div class="filter-group">
      <label class="filter-label" for="area">Area / Suburb</label>
      <input class="filter-input" id="area" type="text" placeholder="e.g. Akoka, Agbowo…">
    </div>

    <div class="filter-group">
      <label class="filter-label">Price Range (₦)</label>
      <div class="filter-row">
        <input class="filter-input" id="minp" type="number" min="0" placeholder="Min">
        <input class="filter-input" id="maxp" type="number" min="0" placeholder="Max">
      </div>
    </div>

    <div class="filter-group">
      <label class="filter-label">Room Type</label>
      <div class="type-chips" id="typeChips">
        <span class="type-chip active" data-val="">All</span>
        <span class="type-chip" data-val="Self-contain">Self-contain</span>
        <span class="type-chip" data-val="Single room">Single Room</span>
        <span class="type-chip" data-val="2-bedroom">2-Bedroom</span>
        <span class="type-chip" data-val="Shared room">Shared Room</span>
      </div>
    </div>

    <div class="filter-group">
      <label class="filter-label" for="avail">Availability</label>
      <select class="filter-input" id="avail">
        <option value="">Any</option>
        <option value="available">Available Now</option>
        <option value="unavailable">Unavailable</option>
      </select>
    </div>

    <div class="filter-group">
      <label class="filter-label" for="sort">Sort By</label>
      <select class="filter-input" id="sort">
        <option value="asc">Price: Low to High</option>
        <option value="desc">Price: High to Low</option>
        <option value="newest">Newest First</option>
      </select>
    </div>
  </aside>

  <!-- ── RESULTS PANEL ── -->
  <div class="results-panel">
    <div class="results-header reveal">
      <div class="results-count" id="resultsCount">
        Loading listings…
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <div class="view-toggle">
          <button class="vt-btn active" id="gridViewBtn" onclick="setView('grid')" title="Grid view"><i class="fas fa-th"></i></button>
          <button class="vt-btn" id="listViewBtn" onclick="setView('list')" title="List view"><i class="fas fa-list"></i></button>
        </div>
      </div>
    </div>

    <!-- Skeleton loaders (shown while fetching) -->
    <div id="skeletons" class="skeleton-grid">
      <?php for($i=0;$i<8;$i++): ?>
      <div class="skeleton-card">
        <div class="skel-img"></div>
        <div class="skel-body">
          <div class="skel-line w60"></div>
          <div class="skel-line"></div>
          <div class="skel-line w40"></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>

    <div id="grid" class="grid" style="display:none"></div>
    <div id="pagination"></div>
  </div>

</div><!-- /page-wrap -->

<!-- ============================================================
     FOOTER
============================================================ -->
<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <span class="brand-name">HostelConnect</span>
        <p>Helping students across Nigeria find safe, verified, and affordable accommodation — close to campus, within budget.</p>
        <div class="footer-socials">
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://chat.whatsapp.com/GLWo3a93fVK2Ws3EClxfDL" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="index.php#about">About Us</a></li>
          <li><a href="/privacy">Privacy Policy</a></li>
          <li><a href="faqs.html">FAQs</a></li>
          <li><a href="#">Help Center</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/register">List a Property</a></li>
          <li><a href="/roommates">Find a Roommate</a></li>
          <li><a href="hostel.php">Browse Hostels</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <div class="footer-contact-line"><i class="fas fa-envelope"></i>support@hostelconnect.com.ng</div>
        <div class="footer-contact-line"><i class="fas fa-phone"></i>+234 814 566 0986</div>
        <div class="footer-contact-line"><i class="fab fa-whatsapp"></i>WhatsApp Support</div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?php echo date("Y"); ?> HostelConnect. All rights reserved.</span>
      <span>Built with ❤️ for Nigerian students 🇳🇬</span>
    </div>
  </div>
</footer>

<!-- ============================================================
     SCRIPTS — ALL ORIGINAL LOGIC PRESERVED + ENHANCEMENTS
============================================================ */-->
<script>
(() => {
  let data = [];
  let activeType = '';
  let currentView = 'grid';

  // DOM refs
  const grid        = document.getElementById('grid');
  const skeletons   = document.getElementById('skeletons');
  const city        = document.getElementById('city');
  const area        = document.getElementById('area');
  const minp        = document.getElementById('minp');
  const maxp        = document.getElementById('maxp');
  const avail       = document.getElementById('avail');
  const sort        = document.getElementById('sort');
  const searchInput = document.getElementById('searchInput');
  const pagination  = document.getElementById('pagination');
  const resultsCount= document.getElementById('resultsCount');
  const activePills = document.getElementById('activePills');

  let currentPage = 1;
  const perPage   = 12;

  // ── Formatting ──────────────────────────────────────────
  function toNGN(n) {
    return new Intl.NumberFormat('en-NG', {style:'currency', currency:'NGN', maximumFractionDigits:0}).format(n);
  }

  // ── View toggle ─────────────────────────────────────────
  function setView(v) {
    currentView = v;
    grid.classList.toggle('list-view', v === 'list');
    document.getElementById('gridViewBtn').classList.toggle('active', v === 'grid');
    document.getElementById('listViewBtn').classList.toggle('active', v === 'list');
  }
  window.setView = setView;

  // ── Type chips ──────────────────────────────────────────
  document.querySelectorAll('.type-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.type-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeType = chip.dataset.val;
      currentPage = 1;
      apply();
    });
  });

  // ── Active filter pills ─────────────────────────────────
  function updatePills(filters) {
    activePills.innerHTML = '';
    filters.forEach(f => {
      if (!f.val) return;
      const pill = document.createElement('span');
      pill.className = 'af-pill';
      pill.innerHTML = `${f.label}: <strong>${f.val}</strong>
        <button onclick="${f.clear}" title="Remove filter"><i class="fas fa-times"></i></button>`;
      activePills.appendChild(pill);
    });
  }

  // ── Clear all filters ───────────────────────────────────
  function clearAllFilters() {
    city.value = '';
    area.value = '';
    minp.value = '';
    maxp.value = '';
    avail.value = '';
    sort.value = 'asc';
    searchInput.value = '';
    activeType = '';
    document.querySelectorAll('.type-chip').forEach(c => c.classList.toggle('active', c.dataset.val === ''));
    currentPage = 1;
    apply();
  }
  window.clearAllFilters = clearAllFilters;

  // ── Render ───────────────────────────────────────────────
  function render(list) {
    grid.innerHTML = '';
    skeletons.style.display = 'none';
    grid.style.display = '';

    // update stats in hero
    document.getElementById('totalCount').textContent = data.length + '+';
    document.getElementById('availCount').textContent = data.filter(x => x.availability === 'available').length + '+';

    // results count
    resultsCount.innerHTML = `Showing <strong>${Math.min(list.length, (currentPage)*perPage - (perPage - list.slice((currentPage-1)*perPage, currentPage*perPage).length))}</strong> of <strong>${list.length}</strong> listings`;

    // active pills
    updatePills([
      {label:'City',      val:city.value,       clear:"document.getElementById('city').value='';applyFilters()"},
      {label:'Area',      val:area.value,       clear:"document.getElementById('area').value='';applyFilters()"},
      {label:'Min ₦',    val:minp.value?'₦'+Number(minp.value).toLocaleString():'', clear:"document.getElementById('minp').value='';applyFilters()"},
      {label:'Max ₦',    val:maxp.value?'₦'+Number(maxp.value).toLocaleString():'', clear:"document.getElementById('maxp').value='';applyFilters()"},
      {label:'Type',      val:activeType,       clear:"clearAllFilters()"},
      {label:'Available', val:avail.value==='available'?'Yes':avail.value==='unavailable'?'No':'', clear:"document.getElementById('avail').value='';applyFilters()"},
    ]);

    if (list.length === 0) {
      grid.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-search"></i>
          <h3>No hostels found</h3>
          <p>Try adjusting your filters or broadening your search area</p>
          <button onclick="clearAllFilters()"><i class="fas fa-redo" style="margin-right:6px"></i>Clear Filters</button>
        </div>`;
      pagination.innerHTML = '';
      return;
    }

    const start = (currentPage - 1) * perPage;
    const pageItems = list.slice(start, start + perPage);

    pageItems.forEach((x, idx) => {
      const el = document.createElement('article');
      el.className = 'card';
      el.style.animationDelay = (idx * 0.04) + 's';
      const isNew = idx < 3 && currentPage === 1;
      el.innerHTML = `
        <div class="cover">
          <span class="card-type-badge">${x.type || 'Hostel'}</span>
          ${isNew ? '<span class="card-new-badge">New</span>' : ''}
          ${x.photos && x.photos.length > 0
            ? `<img src="listing/uploads/${x.photos[0]}" alt="${x.title}" loading="lazy">`
            : `<div class="no-photo"><i class="fas fa-image"></i><span>No photo yet</span></div>`}
        </div>
        <div class="card-body">
          <div class="price">${toNGN(x.price)}<span class="per"> / ${x.period}</span></div>
          <div class="card-title">${x.title}</div>
          <div class="card-loc"><i class="fas fa-map-marker-alt"></i>${x.city} &bull; ${x.area}</div>
          <span class="avail-badge ${x.availability === 'available' ? 'yes' : 'no'}">
            ${x.availability === 'available' ? '✅ Available Now' : '❌ Not Available'}
          </span>
          <div class="tags">${(x.facilities || []).map(t => `<span class="tag">${t}</span>`).join('')}</div>
          <div class="actions">
            <a class="ghost" href="details.php?slug=${x.slug}">
              <i class="fas fa-eye"></i> View Details
            </a>
          </div>
        </div>`;
      grid.appendChild(el);
    });

    renderPagination(list.length);
  }

  // ── Pagination ───────────────────────────────────────────
  function renderPagination(total) {
    const pages = Math.ceil(total / perPage);
    pagination.innerHTML = '';
    if (pages <= 1) return;

    const prev = document.createElement('button');
    prev.innerHTML = '← Prev';
    prev.disabled = currentPage === 1;
    prev.onclick = () => { if (currentPage > 1) { currentPage--; apply(); scrollToGrid(); }};
    pagination.appendChild(prev);

    // page number buttons (show max 5)
    const range = 2;
    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - range && p <= currentPage + range)) {
        const btn = document.createElement('button');
        btn.textContent = p;
        if (p === currentPage) btn.classList.add('active');
        btn.onclick = () => { currentPage = p; apply(); scrollToGrid(); };
        pagination.appendChild(btn);
      } else if (p === currentPage - range - 1 || p === currentPage + range + 1) {
        const dots = document.createElement('span');
        dots.textContent = '…';
        pagination.appendChild(dots);
      }
    }

    const next = document.createElement('button');
    next.innerHTML = 'Next →';
    next.disabled = currentPage === pages;
    next.onclick = () => { if (currentPage < pages) { currentPage++; apply(); scrollToGrid(); }};
    pagination.appendChild(next);
  }

  function scrollToGrid() {
    const offset = document.querySelector('.page-wrap').offsetTop - 90;
    window.scrollTo({ top: offset, behavior: 'smooth' });
  }

  // ── Main filter + sort logic (original, enhanced) ────────
  function apply() {
    let list = data.slice();

    if (city.value)        list = list.filter(x => x.city === city.value);
    if (area.value.trim()) list = list.filter(x => x.area.toLowerCase().includes(area.value.trim().toLowerCase()));

    const min = +minp.value || 0, max = +maxp.value || Infinity;
    list = list.filter(x => x.price >= min && x.price <= max);

    if (activeType)     list = list.filter(x => x.type === activeType);
    if (avail.value)    list = list.filter(x => x.availability === avail.value);

    if (searchInput.value.trim()) {
      const q = searchInput.value.trim().toLowerCase();
      list = list.filter(x =>
        x.title.toLowerCase().includes(q) ||
        x.area.toLowerCase().includes(q) ||
        x.city.toLowerCase().includes(q)
      );
    }

    if (sort.value === 'asc')    list.sort((a, b) => a.price - b.price);
    else if (sort.value === 'desc') list.sort((a, b) => b.price - a.price);
    // newest: keep original DB order (no sort)

    render(list);
  }

  // Expose globally so hero search button works
  window.applyFilters = () => { currentPage = 1; apply(); };

  // Input listeners
  [city, area, minp, maxp, avail, sort, searchInput].forEach(el =>
    el.addEventListener('input', () => { currentPage = 1; apply(); })
  );

  // ── Load data ─────────────────────────────────────────────
  async function loadData() {
    try {
      const res = await fetch("fetch_listings.php");
      data = await res.json();

      // Populate city dropdown
      city.innerHTML = '<option value="">All Cities</option>';
      [...new Set(data.map(d => d.city))].sort().forEach(c => {
        const o = document.createElement('option');
        o.textContent = c; o.value = c;
        city.appendChild(o);
      });

      apply();
    } catch (err) {
      console.error("Failed to load listings:", err);
      skeletons.style.display = 'none';
      grid.style.display = '';
      grid.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Could not load listings</h3><p>Please refresh the page or try again later.</p></div>';
    }
  }

  // ── Nav toggle ────────────────────────────────────────────
  const toggle  = document.getElementById('menuToggle');
  const navMenu = document.getElementById('navMenu');
  toggle.addEventListener('click', () => {
    const open = navMenu.classList.toggle('show');
    toggle.classList.toggle('open', open);
    toggle.textContent = open ? '✕' : '☰';
  });
  navMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      navMenu.classList.remove('show');
      toggle.classList.remove('open');
      toggle.textContent = '☰';
    });
  });

  // ── Scroll reveal ─────────────────────────────────────────
  const ro = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.07 });
  document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

  // ── Check URL params (e.g. ?city=Lagos from homepage) ────
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('city'))   { /* will be set after city dropdown populates */ }
  if (urlParams.get('search')) { searchInput.value = urlParams.get('search'); }

  loadData();
})();
</script>
</body>
</html>