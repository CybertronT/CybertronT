<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Affordable Student Hostels | HostelConnect</title>
  <meta name="description" content="HostelConnect helps students find safe, affordable, and verified hostels near their campuses. Browse listings, connect with landlords, or find a roommate today.">
  <meta name="keywords" content="student hostels, hostel accommodation, cheap hostels, student housing, roommate finder, landlord listing">
  <meta name="robots" content="index, follow">
  <meta property="og:title" content="HostelConnect - Student Hostel Finder">
  <meta property="og:description" content="Find affordable student hostels, connect with landlords, or discover roommates easily.">
  <meta property="og:image" content="hostelconnect.com.ng/logo.png">
  <meta property="og:url" content="https://hostelconnect.com.ng/">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="HostelConnect - Find Student Hostels">
  <meta name="twitter:description" content="Affordable student hostels and roommate finder.">
  <meta name="twitter:image" content="https://hostelconnect.com.ng/logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","url":"https://hostelconnect.com.ng","logo":"https://hostelconnect.com.ng/logo.png","name":"HostelConnect"}</script>
  <style>
/* ============================================================
   RESET & ROOT VARIABLES
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
html{scroll-padding-top:76px;scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);line-height:1.65;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--brand);border-radius:10px}

/* ============================================================
   PARTICLES
============================================================ */
.particles{position:absolute;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.particle{position:absolute;border-radius:50%;background:rgba(255,255,255,.06);animation:floatUp linear infinite}
.particle:nth-child(1){width:14px;height:14px;left:8%;animation-duration:14s;animation-delay:0s}
.particle:nth-child(2){width:8px;height:8px;left:20%;animation-duration:18s;animation-delay:2s}
.particle:nth-child(3){width:20px;height:20px;left:35%;animation-duration:12s;animation-delay:4s}
.particle:nth-child(4){width:6px;height:6px;left:55%;animation-duration:20s;animation-delay:1s}
.particle:nth-child(5){width:16px;height:16px;left:70%;animation-duration:16s;animation-delay:6s}
.particle:nth-child(6){width:10px;height:10px;left:85%;animation-duration:13s;animation-delay:3s}
.particle:nth-child(7){width:5px;height:5px;left:45%;animation-duration:22s;animation-delay:8s}
.particle:nth-child(8){width:18px;height:18px;left:92%;animation-duration:17s;animation-delay:5s}
@keyframes floatUp{0%{transform:translateY(110vh) scale(0);opacity:0}10%{opacity:1}90%{opacity:.4}100%{transform:translateY(-10vh) scale(1);opacity:0}}

/* ============================================================
   HEADER
============================================================ */
header{display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,.94);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);padding:0 40px;height:72px;position:sticky;top:0;z-index:999;border-bottom:1px solid rgba(228,233,239,.9);box-shadow:0 2px 24px rgba(11,104,177,.06);animation:slideDown .6s cubic-bezier(.22,1,.36,1) both}
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
   HERO
============================================================ */
.home{position:relative;min-height:96vh;display:flex;align-items:center;justify-content:center;overflow:hidden;background:linear-gradient(140deg,#0b68b1 0%,#07438e 45%,#03295a 100%)}
.orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0}
.orb-1{width:600px;height:600px;background:radial-gradient(circle,rgba(207,108,23,.25) 0%,transparent 70%);top:-150px;right:-100px;animation:orbDrift 12s ease-in-out infinite alternate}
.orb-2{width:500px;height:500px;background:radial-gradient(circle,rgba(11,104,177,.4) 0%,transparent 70%);bottom:-100px;left:-80px;animation:orbDrift 16s ease-in-out infinite alternate-reverse}
.orb-3{width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%);top:40%;left:30%;animation:orbDrift 20s ease-in-out infinite alternate}
@keyframes orbDrift{from{transform:translate(0,0) scale(1)}to{transform:translate(40px,30px) scale(1.1)}}
.home::before{content:'';position:absolute;inset:0;background:url('bg.jpg') center/cover no-repeat;opacity:.07;z-index:0}
.hero-inner{position:relative;z-index:1;text-align:center;padding:80px 24px 60px;max-width:800px;width:100%}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.22);color:rgba(255,255,255,.9);font-size:.8rem;font-weight:500;padding:7px 18px;border-radius:100px;margin-bottom:30px;backdrop-filter:blur(10px);animation:fadeSlideUp .8s .1s cubic-bezier(.22,1,.36,1) both}
.hero-badge .dot{width:8px;height:8px;border-radius:50%;background:#4ade80;box-shadow:0 0 8px #4ade80;animation:pulseGlow 2s infinite}
@keyframes pulseGlow{0%,100%{transform:scale(1);box-shadow:0 0 8px #4ade80}50%{transform:scale(1.4);box-shadow:0 0 16px #4ade80}}
.home h1{font-family:'Playfair Display',serif;font-size:clamp(2.2rem,6vw,4rem);font-weight:800;color:#fff;line-height:1.12;margin-bottom:22px;letter-spacing:-1.5px;animation:fadeSlideUp .8s .25s cubic-bezier(.22,1,.36,1) both}
.home h1 em{font-style:italic;color:#fbbf24;position:relative;display:inline-block}
.home h1 em::after{content:'';position:absolute;bottom:2px;left:0;right:0;height:3px;background:linear-gradient(90deg,#fbbf24,var(--accent));border-radius:2px;transform:scaleX(0);transform-origin:left;animation:underlineGrow .6s 1s ease forwards}
@keyframes underlineGrow{to{transform:scaleX(1)}}
.home .subtitle{font-size:1.05rem;color:rgba(255,255,255,.72);margin-bottom:42px;max-width:500px;margin-left:auto;margin-right:auto;font-weight:300;line-height:1.8;animation:fadeSlideUp .8s .4s cubic-bezier(.22,1,.36,1) both}
.search-bar{display:flex;max-width:580px;margin:0 auto;background:rgba(255,255,255,.97);border-radius:14px;padding:6px;box-shadow:var(--shadow-lg);animation:fadeSlideUp .8s .55s cubic-bezier(.22,1,.36,1) both;transition:box-shadow .3s}
.search-bar:focus-within{box-shadow:0 20px 60px rgba(0,0,0,.3),0 0 0 3px rgba(207,108,23,.3)}
.search-bar input{flex:1;padding:14px 18px;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.95rem;color:var(--dark);outline:none;min-width:0}
.search-bar input::placeholder{color:var(--muted)}
.search-bar button{padding:13px 26px;border:none;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.9rem;cursor:pointer;display:flex;align-items:center;gap:8px;white-space:nowrap;transition:transform .2s,box-shadow .2s;box-shadow:0 4px 14px rgba(207,108,23,.35)}
.search-bar button:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(207,108,23,.45)}
.hero-stats{display:flex;justify-content:center;gap:0;margin-top:56px;animation:fadeSlideUp .8s .7s cubic-bezier(.22,1,.36,1) both}
.stat{text-align:center;padding:0 32px;position:relative}
.stat:not(:last-child)::after{content:'';position:absolute;right:0;top:15%;bottom:15%;width:1px;background:rgba(255,255,255,.2)}
.stat strong{display:block;font-size:2rem;font-weight:800;color:#fff;font-family:'Playfair Display',serif;line-height:1;margin-bottom:4px}
.stat span{font-size:.75rem;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:1px}
.scroll-hint{position:absolute;bottom:28px;left:50%;transform:translateX(-50%);z-index:1;display:flex;flex-direction:column;align-items:center;gap:6px;color:rgba(255,255,255,.35);font-size:.7rem;letter-spacing:1px;text-transform:uppercase;animation:fadeIn 1s 1.5s both}
.scroll-hint .arr{width:18px;height:18px;border-right:2px solid rgba(255,255,255,.3);border-bottom:2px solid rgba(255,255,255,.3);transform:rotate(45deg);animation:scrollBounce 2s infinite}
@keyframes scrollBounce{0%,100%{transform:rotate(45deg) translateY(0)}50%{transform:rotate(45deg) translateY(5px)}}

/* ============================================================
   SECTION TAG HELPER
============================================================ */
.section-tag{display:inline-flex;align-items:center;gap:8px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--brand);margin-bottom:10px}
.section-tag::before{content:'';width:22px;height:2px;background:var(--brand);border-radius:2px}
.section-head{text-align:center;margin-bottom:56px}
.section-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,3vw,2.4rem);font-weight:800;color:var(--dark);letter-spacing:-.5px;margin-bottom:8px}
.section-head p{color:var(--muted);font-size:.97rem;max-width:460px;margin:0 auto}

/* ============================================================
   UNIVERSITIES STRIP
============================================================ */
.uni-strip{background:var(--white);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:20px 0;overflow:hidden;position:relative}
.uni-strip::before,.uni-strip::after{content:'';position:absolute;top:0;bottom:0;width:80px;z-index:2;pointer-events:none}
.uni-strip::before{left:0;background:linear-gradient(to right,var(--white),transparent)}
.uni-strip::after{right:0;background:linear-gradient(to left,var(--white),transparent)}
.uni-track{display:flex;gap:48px;align-items:center;animation:scrollTrack 28s linear infinite;width:max-content}
.uni-track:hover{animation-play-state:paused}
@keyframes scrollTrack{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.uni-item{display:flex;align-items:center;gap:10px;white-space:nowrap;color:var(--muted);font-size:.82rem;font-weight:600;letter-spacing:.3px;text-transform:uppercase;opacity:.7;transition:opacity .2s}
.uni-item:hover{opacity:1;color:var(--brand)}
.uni-item i{color:var(--brand);font-size:1rem;opacity:.6}
.uni-strip-label{text-align:center;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--muted);padding:0 24px 10px;opacity:.7}

/* ============================================================
   HOW IT WORKS — STEPS
============================================================ */
.steps-section{padding:110px 24px;background:var(--bg)}
.steps-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:32px;max-width:1000px;margin:0 auto;position:relative}
.steps-grid::before{content:'';position:absolute;top:44px;left:10%;right:10%;height:2px;background:linear-gradient(90deg,var(--brand-light),var(--accent-light));z-index:0}
.step-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:36px 24px 30px;text-align:center;position:relative;z-index:1;transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s}
.step-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg)}
.step-num{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;box-shadow:0 6px 20px rgba(11,104,177,.3);transition:transform .3s}
.step-card:hover .step-num{transform:scale(1.12)}
.step-card h3{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:8px}
.step-card p{color:var(--muted);font-size:.88rem;line-height:1.7}

/* ============================================================
   LISTINGS
============================================================ */
.wrap{max-width:1180px;margin:90px auto;padding:0 24px}
.wrap-header{text-align:center;margin-bottom:44px}
.wrap-header h2{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,3vw,2.4rem);font-weight:800;color:var(--dark);letter-spacing:-.5px;margin-bottom:8px}
.wrap-header p{color:var(--muted);font-size:.97rem;max-width:420px;margin:0 auto}
.controls{display:grid;gap:10px;background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:20px;margin-bottom:36px;box-shadow:var(--shadow-sm)}
@media(min-width:860px){.controls{grid-template-columns:repeat(6,1fr)}}
.controls select,.controls input[type="number"],.controls input[type="text"],.controls input:not([type="submit"]){width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:'DM Sans',sans-serif;font-size:.87rem;color:var(--body);background:var(--bg);outline:none;transition:border-color .2s,box-shadow .2s,background .2s;appearance:none}
.controls select:focus,.controls input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white)}
.grid{display:grid;gap:22px;grid-template-columns:repeat(auto-fill,minmax(268px,1fr))}
.card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s;position:relative}
.card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg)}
.cover{height:200px;background:var(--brand-light);overflow:hidden;position:relative}
.cover img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.card:hover .cover img{transform:scale(1.07)}
.cover .no-photo{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--muted);font-size:.82rem;gap:8px}
.cover .no-photo i{font-size:2.2rem;opacity:.25}
.card-type-badge{position:absolute;top:12px;left:12px;background:rgba(11,104,177,.88);color:#fff;font-size:.7rem;font-weight:700;padding:5px 11px;border-radius:100px;backdrop-filter:blur(6px);letter-spacing:.5px;text-transform:uppercase}
.card-badge-new{position:absolute;top:12px;right:12px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-size:.68rem;font-weight:700;padding:4px 10px;border-radius:100px;letter-spacing:.5px;text-transform:uppercase}
.card-body{padding:18px}
.price{font-weight:800;color:var(--accent);font-size:1.18rem;margin-bottom:5px;font-family:'Playfair Display',serif}
.price .per{font-family:'DM Sans',sans-serif;font-size:.8rem;font-weight:400;color:var(--muted)}
.card-title{font-weight:600;color:var(--dark);font-size:.98rem;margin-bottom:7px;line-height:1.4}
.card-loc{display:flex;align-items:center;gap:5px;color:var(--muted);font-size:.82rem;margin-bottom:10px}
.card-loc i{color:var(--brand);font-size:.78rem}
.avail-badge{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:700;padding:4px 11px;border-radius:100px;margin-bottom:10px}
.avail-badge.yes{background:var(--green-bg);color:var(--green)}
.avail-badge.no{background:var(--red-bg);color:var(--red)}
.tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:14px}
.tag{font-size:.73rem;border:1px solid var(--border);padding:3px 9px;border-radius:100px;color:var(--body);background:var(--bg)}
.actions{display:flex;gap:8px}
.btn,.ghost{flex:1;text-align:center;padding:10px 14px;border-radius:var(--r-sm);font-size:.86rem;font-weight:600;text-decoration:none;transition:transform .2s,box-shadow .2s,background .2s;cursor:pointer;border:none;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;justify-content:center;gap:6px}
.btn{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:white;box-shadow:0 3px 10px rgba(11,104,177,.22)}
.btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(11,104,177,.35)}
.ghost{background:var(--brand-light);color:var(--brand);border:1.5px solid rgba(11,104,177,.15)}
.ghost:hover{background:#cce4f8;transform:translateY(-1px)}

/* ============================================================
   PAGINATION
============================================================ */
.pagination{display:flex;justify-content:center;align-items:center;gap:10px;margin-top:48px}
.pagination button{cursor:pointer;padding:10px 22px;border-radius:var(--r-sm);border:1.5px solid var(--border);background:var(--white);color:var(--brand);font-family:'DM Sans',sans-serif;font-weight:600;font-size:.87rem;transition:all .2s}
.pagination button:hover:not(:disabled){background:var(--brand);color:#fff;border-color:var(--brand);transform:translateY(-1px);box-shadow:0 4px 14px rgba(11,104,177,.25)}
.pagination button:disabled{opacity:.38;cursor:not-allowed}
.pagination button.active{background:var(--accent);color:white;border-color:var(--accent)}
.pagination span{color:var(--muted);font-size:.86rem;padding:0 6px}

/* ============================================================
   WHY HOSTELCONNECT
============================================================ */
.why-section{padding:110px 24px;background:var(--white)}
.why-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:22px;max-width:1100px;margin:0 auto}
.why-card{display:flex;align-items:flex-start;gap:16px;background:var(--bg);border:1px solid var(--border);border-radius:var(--r);padding:26px;transition:transform .3s cubic-bezier(.22,1,.36,1),box-shadow .3s,border-color .3s}
.why-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);border-color:rgba(11,104,177,.2)}
.why-icon{width:46px;height:46px;min-width:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem}
.why-icon.blue{background:var(--brand-light);color:var(--brand)}
.why-icon.orange{background:var(--accent-light);color:var(--accent)}
.why-icon.green{background:var(--green-bg);color:var(--green)}
.why-icon.purple{background:#f3e8ff;color:#7c3aed}
.why-card h4{font-size:.97rem;font-weight:700;color:var(--dark);margin-bottom:4px}
.why-card p{color:var(--muted);font-size:.84rem;line-height:1.6}

/* ============================================================
   ABOUT
============================================================ */
.about{position:relative;padding:110px 24px;background:var(--dark);overflow:hidden}
.about::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 75% 40%,rgba(11,104,177,.2) 0%,transparent 55%),radial-gradient(ellipse at 20% 70%,rgba(207,108,23,.1) 0%,transparent 50%)}
.about-inner{position:relative;z-index:1;max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
.about-text .section-tag{color:#60a5fa}
.about-text .section-tag::before{background:#60a5fa}
.about-text h2{font-family:'Playfair Display',serif;font-size:clamp(1.9rem,3vw,2.6rem);font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.5px;margin-bottom:18px}
.about-text p{color:rgba(255,255,255,.58);font-size:.97rem;line-height:1.85;font-weight:300}
.about-stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.about-stat-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:var(--r);padding:26px 20px;text-align:center;transition:background .3s,border-color .3s,transform .3s}
.about-stat-card:hover{background:rgba(11,104,177,.18);border-color:rgba(11,104,177,.35);transform:translateY(-4px)}
.about-stat-card i{font-size:1.5rem;color:#60a5fa;margin-bottom:10px;display:block}
.about-stat-card strong{display:block;color:#fff;font-size:1.7rem;font-family:'Playfair Display',serif;margin-bottom:4px}
.about-stat-card span{color:rgba(255,255,255,.38);font-size:.72rem;text-transform:uppercase;letter-spacing:1px}

/* ============================================================
   TESTIMONIALS
============================================================ */
.testi-section{padding:110px 24px;background:var(--bg);overflow:hidden}
.testi-track-wrap{overflow:hidden;position:relative;margin-top:0}
.testi-track-wrap::before,.testi-track-wrap::after{content:'';position:absolute;top:0;bottom:0;width:100px;z-index:2;pointer-events:none}
.testi-track-wrap::before{left:0;background:linear-gradient(to right,var(--bg),transparent)}
.testi-track-wrap::after{right:0;background:linear-gradient(to left,var(--bg),transparent)}
.testi-track{display:flex;gap:24px;animation:scrollTrack 36s linear infinite;width:max-content;padding:16px 0}
.testi-track:hover{animation-play-state:paused}
.testi-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:28px;width:320px;min-width:320px;box-shadow:var(--shadow-sm);transition:transform .3s,box-shadow .3s;flex-shrink:0}
.testi-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md)}
.testi-stars{display:flex;gap:3px;margin-bottom:14px}
.testi-stars i{color:var(--yellow);font-size:.9rem}
.testi-text{color:var(--body);font-size:.9rem;line-height:1.75;margin-bottom:18px;font-style:italic}
.testi-text::before{content:'\201C';font-size:2rem;color:var(--brand);opacity:.2;line-height:0;vertical-align:-.4em;margin-right:4px}
.testi-author{display:flex;align-items:center;gap:12px}
.testi-avatar{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0}
.testi-name{font-weight:700;font-size:.88rem;color:var(--dark)}
.testi-school{font-size:.76rem;color:var(--muted)}

/* ============================================================
   HOW IT WORKS (OPTIONS)
============================================================ */
.how-section{padding:110px 24px;background:var(--white)}
.options-grid{display:grid;gap:24px;max-width:1100px;margin:0 auto;grid-template-columns:repeat(auto-fit,minmax(300px,1fr))}
.option-card{border:1px solid var(--border);border-radius:var(--r);padding:38px 30px 34px;background:var(--white);transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s;position:relative;overflow:hidden;display:flex;flex-direction:column}
.option-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--brand),var(--accent));transform:scaleX(0);transform-origin:left;transition:transform .4s cubic-bezier(.22,1,.36,1)}
.option-card:hover::before{transform:scaleX(1)}
.option-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg)}
.option-icon{width:58px;height:58px;border-radius:16px;background:var(--brand-light);display:flex;align-items:center;justify-content:center;margin-bottom:22px;transition:background .3s,transform .3s}
.option-card:hover .option-icon{background:var(--brand);transform:scale(1.08)}
.option-icon i{font-size:1.4rem;color:var(--brand);transition:color .3s}
.option-card:hover .option-icon i{color:#fff}
.option-card h3{font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:var(--dark);margin-bottom:10px}
.option-card p{color:var(--muted);font-size:.91rem;line-height:1.75;margin-bottom:26px;flex:1}
.option-btn-group{display:flex;flex-direction:column;gap:10px;margin-top:auto}
.opt-btn-primary{display:flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;border-radius:var(--r-sm);background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.9rem;text-decoration:none;transition:transform .2s,box-shadow .2s;box-shadow:0 3px 12px rgba(11,104,177,.25);width:100%}
.opt-btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 22px rgba(11,104,177,.38);color:#fff}
.opt-btn-outline{display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:var(--r-sm);background:transparent;color:var(--brand);border:2px solid rgba(11,104,177,.25);font-family:'DM Sans',sans-serif;font-weight:600;font-size:.9rem;text-decoration:none;transition:background .2s,border-color .2s,transform .2s;width:100%}
.opt-btn-outline:hover{background:var(--brand-light);border-color:var(--brand);transform:translateY(-1px);color:var(--brand)}
.opt-btn-accent{background:linear-gradient(135deg,var(--accent),var(--accent-dark))!important;box-shadow:0 3px 12px rgba(207,108,23,.25)!important}
.opt-btn-accent:hover{box-shadow:0 6px 22px rgba(207,108,23,.38)!important}

/* ============================================================
   FAQ
============================================================ */
.faq-section{padding:110px 24px;background:var(--bg)}
.faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:12px}
.faq-item{background:var(--white);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;transition:box-shadow .3s}
.faq-item.open{box-shadow:var(--shadow-md);border-color:rgba(11,104,177,.2)}
.faq-q{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;cursor:pointer;gap:16px;user-select:none}
.faq-q span{font-weight:600;font-size:.95rem;color:var(--dark);line-height:1.4}
.faq-q .faq-icon{width:32px;height:32px;min-width:32px;border-radius:8px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:.8rem;transition:transform .3s,background .3s}
.faq-item.open .faq-icon{background:var(--brand);color:#fff;transform:rotate(45deg)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .4s cubic-bezier(.22,1,.36,1),padding .3s}
.faq-a p{padding:0 24px 20px;color:var(--muted);font-size:.9rem;line-height:1.75}
.faq-item.open .faq-a{max-height:200px}

/* ============================================================
   NEWSLETTER
============================================================ */
.newsletter-section{padding:80px 24px;background:linear-gradient(135deg,var(--brand),var(--brand-dark));position:relative;overflow:hidden}
.newsletter-section::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:rgba(255,255,255,.04);top:-200px;right:-100px}
.newsletter-section::after{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(207,108,23,.1);bottom:-100px;left:-50px}
.newsletter-inner{position:relative;z-index:1;max-width:580px;margin:0 auto;text-align:center}
.newsletter-inner h2{font-family:'Playfair Display',serif;font-size:clamp(1.7rem,3vw,2.2rem);font-weight:800;color:#fff;margin-bottom:10px;letter-spacing:-.4px}
.newsletter-inner p{color:rgba(255,255,255,.65);font-size:.95rem;margin-bottom:30px}
.newsletter-form{display:flex;gap:0;background:rgba(255,255,255,.95);border-radius:12px;padding:5px;box-shadow:0 12px 40px rgba(0,0,0,.2);max-width:480px;margin:0 auto}
.newsletter-form input{flex:1;padding:13px 18px;border:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:.9rem;color:var(--dark);outline:none;min-width:0}
.newsletter-form input::placeholder{color:var(--muted)}
.newsletter-form button{padding:12px 22px;border:none;border-radius:9px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.88rem;cursor:pointer;white-space:nowrap;transition:transform .2s,box-shadow .2s;box-shadow:0 3px 12px rgba(207,108,23,.3)}
.newsletter-form button:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(207,108,23,.45)}
.newsletter-note{margin-top:14px;font-size:.75rem;color:rgba(255,255,255,.4)}

/* ============================================================
   CONTACT
============================================================ */
.contact{padding:110px 24px;background:var(--dark);position:relative;overflow:hidden;text-align:center}
.contact::before{content:'';position:absolute;width:700px;height:700px;border-radius:50%;background:rgba(11,104,177,.06);top:-300px;right:-200px}
.contact::after{content:'';position:absolute;width:400px;height:400px;border-radius:50%;background:rgba(207,108,23,.05);bottom:-100px;left:-100px}
.contact-inner{position:relative;z-index:1;max-width:580px;margin:0 auto}
.contact .section-tag{color:#60a5fa;justify-content:center}
.contact .section-tag::before{background:#60a5fa}
.contact h2{font-family:'Playfair Display',serif;font-size:clamp(1.9rem,3.5vw,2.8rem);font-weight:800;color:#fff;margin-bottom:14px;letter-spacing:-.5px}
.contact p{color:rgba(255,255,255,.55);font-size:1rem;margin-bottom:38px}
.contact a.whatsapp-btn{display:inline-flex;align-items:center;gap:10px;padding:18px 38px;background:#25D366;color:white;border-radius:14px;text-decoration:none;font-weight:700;font-size:1.05rem;transition:background .25s,transform .2s,box-shadow .2s;box-shadow:0 8px 28px rgba(37,211,102,.3)}
.contact a.whatsapp-btn:hover{background:#1da851;transform:translateY(-4px);box-shadow:0 16px 44px rgba(37,211,102,.4)}
.contact a.whatsapp-btn i{font-size:1.3rem}

/* ============================================================
   FOOTER
============================================================ */
footer{background:#090d13;padding:72px 24px 32px;font-family:'DM Sans',sans-serif}
.footer-inner{max-width:1100px;margin:0 auto}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:52px;padding-bottom:52px;border-bottom:1px solid rgba(255,255,255,.07);margin-bottom:32px}
.footer-brand .brand-name{font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:800;color:#fff;display:block;margin-bottom:14px}
.footer-brand p{font-size:.86rem;line-height:1.75;color:rgba(255,255,255,.38);max-width:250px;margin-bottom:22px}
.footer-socials{display:flex;gap:9px}
.footer-socials a{width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.09);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);font-size:.85rem;text-decoration:none;transition:background .2s,color .2s,transform .2s}
.footer-socials a:hover{background:var(--brand);color:#fff;border-color:var(--brand);transform:translateY(-2px)}
.footer-col h4{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.85);margin-bottom:18px}
.footer-col ul{list-style:none}
.footer-col ul li{margin-bottom:11px}
.footer-col ul li a{color:rgba(255,255,255,.38);text-decoration:none;font-size:.87rem;transition:color .2s}
.footer-col ul li a:hover{color:#fff}
.footer-contact-line{display:flex;align-items:flex-start;gap:9px;font-size:.84rem;color:rgba(255,255,255,.38);margin-bottom:10px}
.footer-contact-line i{color:var(--brand);font-size:.85rem;margin-top:2px;width:14px;flex-shrink:0}
.footer-bottom{display:flex;justify-content:space-between;align-items:center;font-size:.8rem;color:rgba(255,255,255,.22);flex-wrap:wrap;gap:10px}

/* ============================================================
   FLOATING WHATSAPP BUTTON
============================================================ */
.float-wa{position:fixed;bottom:30px;right:28px;z-index:990;display:flex;align-items:center;gap:10px;background:#25D366;color:#fff;border-radius:100px;padding:13px 20px 13px 14px;text-decoration:none;font-weight:700;font-size:.88rem;box-shadow:0 6px 28px rgba(37,211,102,.4);transition:transform .25s,box-shadow .25s;animation:floatWa .6s 2s cubic-bezier(.22,1,.36,1) both}
.float-wa:hover{transform:translateY(-4px) scale(1.04);box-shadow:0 12px 40px rgba(37,211,102,.55);color:#fff}
.float-wa i{font-size:1.4rem}
.float-wa .wa-ping{position:absolute;top:-4px;right:-4px;width:14px;height:14px;border-radius:50%;background:#ff4b4b;border:2px solid #fff;animation:pingDot 2s infinite}
@keyframes pingDot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.7}}
@keyframes floatWa{from{opacity:0;transform:translateY(20px) scale(.9)}to{opacity:1;transform:translateY(0) scale(1)}}

/* ============================================================
   REVEAL ANIMATIONS
============================================================ */
.reveal{opacity:0;transform:translateY(28px);transition:opacity .7s cubic-bezier(.22,1,.36,1),transform .7s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal-delay-1{transition-delay:.12s}
.reveal-delay-2{transition-delay:.24s}
.reveal-delay-3{transition-delay:.36s}
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:1024px){
  .footer-top{grid-template-columns:1fr 1fr;gap:32px}
  .about-inner{grid-template-columns:1fr;gap:48px}
  .steps-grid::before{display:none}
}
@media(max-width:768px){
  header{padding:0 18px;height:66px}
  nav ul{display:none;flex-direction:column;position:fixed;top:66px;left:0;right:0;background:rgba(255,255,255,.97);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);padding:16px 18px 24px;box-shadow:0 12px 40px rgba(0,0,0,.12);z-index:998;gap:4px}
  nav ul.show{display:flex}
  nav ul li a{padding:12px 16px;font-size:.93rem}
  .menu-toggle{display:flex}
  .home h1{letter-spacing:-.5px}
  .search-bar{flex-direction:column;border-radius:14px;padding:8px;gap:6px}
  .search-bar input,.search-bar button{border-radius:9px}
  .search-bar button{justify-content:center}
  .hero-stats{gap:0}
  .stat{padding:0 16px}
  .stat strong{font-size:1.5rem}
  .about-stat-grid{grid-template-columns:1fr 1fr}
  .options-grid{grid-template-columns:1fr;max-width:480px;margin:0 auto}
  .footer-top{grid-template-columns:1fr;gap:28px}
  .footer-bottom{flex-direction:column;text-align:center}
  .wrap{margin:60px auto}
  .about,.how-section,.contact,.why-section,.steps-section,.faq-section,.testi-section,.newsletter-section{padding:70px 20px}
  .hero-inner{padding:60px 18px 50px}
  .newsletter-form{flex-direction:column;border-radius:12px;gap:6px;padding:8px}
  .newsletter-form input,.newsletter-form button{border-radius:9px;width:100%}
  .newsletter-form button{justify-content:center;display:flex;align-items:center}
  .float-wa .wa-label{display:none}
  .float-wa{padding:14px;border-radius:50%}
}
@media(max-width:420px){
  .hero-stats{flex-wrap:wrap;gap:20px}
  .stat::after{display:none}
  .stat{padding:0 12px;flex:0 0 45%}
}
  </style>
</head>
<body>

<!-- ============================================================
     FLOATING WHATSAPP
============================================================ -->
<a href="https://chat.whatsapp.com/GLWo3a93fVK2Ws3EClxfDL" target="_blank" rel="noopener noreferrer" class="float-wa" aria-label="Chat on WhatsApp">
  <i class="fab fa-whatsapp"></i>
  <span class="wa-label">Chat with us</span>
  <span class="wa-ping"></span>
</a>

<!-- ============================================================
     HEADER
============================================================ -->
<header>
  <a href="#home" class="logo">
    <img src="logo.png" alt="HostelConnect Logo">
    <span>HostelConnect</span>
  </a>
  <nav>
    <ul class="nav-links" id="navMenu">
      <li><a href="#home">Home</a></li>
      <li><a href="hostel.php">Find Hostels</a></li>
      <li><a href="roommates.php">Find Roommates</a></li>
      <li><a href="register.php">List Your Hostel</a></li>
      <li><a href="#faq">FAQ</a></li>
      <li><a href="#contact">Contact</a></li>
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
     HERO
============================================================ -->
<section id="home" class="home">
  <div class="particles">
    <div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div>
    <div class="particle"></div><div class="particle"></div>
  </div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
  <div class="hero-inner">
    <div class="hero-badge"><span class="dot"></span>Trusted by students across Nigeria</div>
    <h1>Find Your <em>Perfect</em><br>Student Hostel</h1>
    <p class="subtitle">Browse verified, affordable accommodation near your campus — quickly, safely, and completely free.</p>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search by city, area or hostel name…" autocomplete="off">
      <button onclick="apply()"><i class="fas fa-search"></i> Search</button>
    </div>
    <div class="hero-stats">
      <div class="stat"><strong>500+</strong><span>Listings</span></div>
      <div class="stat"><strong>20+</strong><span>Cities</span></div>
      <div class="stat"><strong>3K+</strong><span>Students</span></div>
      <div class="stat"><strong>100%</strong><span>Verified</span></div>
    </div>
  </div>
  <div class="scroll-hint"><span>Scroll</span><div class="arr"></div></div>
</section>

<!-- ============================================================
     UNIVERSITIES STRIP
============================================================ -->
<div class="uni-strip">
  <p class="uni-strip-label">Serving students near these schools &amp; more</p>
  <div class="uni-track" id="uniTrack">
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>University of Lagos</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>OAU Ile-Ife</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>University of Ibadan</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>FUTA Akure</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>LASU Ojo</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIABUJA</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIBEN Benin</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>BUK Kano</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>FUNAAB Abeokuta</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>Covenant University</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>Babcock University</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIPORT</div>
    <!-- Duplicate for seamless loop -->
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>University of Lagos</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>OAU Ile-Ife</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>University of Ibadan</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>FUTA Akure</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>LASU Ojo</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIABUJA</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIBEN Benin</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>BUK Kano</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>FUNAAB Abeokuta</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>Covenant University</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>Babcock University</div>
    <div class="uni-item"><i class="fas fa-graduation-cap"></i>UNIPORT</div>
  </div>
</div>

<!-- ============================================================
     HOW IT WORKS — STEPS
============================================================ -->
<section class="steps-section">
  <div class="section-head reveal">
    <div class="section-tag" style="justify-content:center;">Simple Process</div>
    <h2>Find a Hostel in 3 Easy Steps</h2>
    <p>No stress, no agent fees — just search, connect, and move in</p>
  </div>
  <div class="steps-grid">
    <div class="step-card reveal">
      <div class="step-num">1</div>
      <h3>Search</h3>
      <p>Enter your school, city, or area. Filter by price, room type, and facilities to narrow down your options.</p>
    </div>
    <div class="step-card reveal reveal-delay-1">
      <div class="step-num">2</div>
      <h3>Connect</h3>
      <p>View hostel details, photos, and pricing. Contact the landlord directly — no middlemen, no hidden fees.</p>
    </div>
    <div class="step-card reveal reveal-delay-2">
      <div class="step-num">3</div>
      <h3>Move In</h3>
      <p>Agree on terms, pay securely, and get your keys. It's that simple — your new home awaits you.</p>
    </div>
  </div>
</section>

<!-- ============================================================
     LISTINGS
============================================================ -->
<section id="listings" class="wrap">
  <div class="wrap-header reveal">
    <div class="section-tag">Browse Listings</div>
    <h2>Available Hostels</h2>
    <p>Filter by city, price, and type to find exactly what you need</p>
  </div>
  <div class="controls reveal">
    <select id="city"><option value="">All Cities</option></select>
    <input id="area" placeholder="Suburb / Area" />
    <input id="minp" type="number" min="0" placeholder="Min ₦" />
    <input id="maxp" type="number" min="0" placeholder="Max ₦" />
    <select id="type">
      <option value="">Any Type</option>
      <option>Self-contain</option>
      <option>Single room</option>
      <option>2-bedroom</option>
      <option>Shared room</option>
    </select>
    <select id="sort">
      <option value="asc">Price ↑</option>
      <option value="desc">Price ↓</option>
    </select>
  </div>
  <div id="grid" class="grid"></div>
  <div id="pagination" class="pagination"></div>
</section>

<!-- ============================================================
     WHY HOSTELCONNECT
============================================================ -->
<section class="why-section">
  <div class="section-head reveal">
    <div class="section-tag" style="justify-content:center;">Why Us</div>
    <h2>Why Choose HostelConnect?</h2>
    <p>We're built specifically for Nigerian students — here's what sets us apart</p>
  </div>
  <div class="why-grid">
    <div class="why-card reveal">
      <div class="why-icon blue"><i class="fas fa-tags"></i></div>
      <div>
        <h4>100% Free to Use</h4>
        <p>No registration fees, no agent commissions. Searching and contacting landlords is completely free for students.</p>
      </div>
    </div>
    <div class="why-card reveal reveal-delay-1">
      <div class="why-icon green"><i class="fas fa-shield-alt"></i></div>
      <div>
        <h4>Verified Listings Only</h4>
        <p>Every hostel is reviewed before going live. No fake listings, no scam landlords — your safety is our priority.</p>
      </div>
    </div>
    <div class="why-card reveal reveal-delay-2">
      <div class="why-icon orange"><i class="fas fa-bolt"></i></div>
      <div>
        <h4>Direct Landlord Contact</h4>
        <p>Speak directly with property owners. Cut out agents entirely and get the best price straight from the source.</p>
      </div>
    </div>
    <div class="why-card reveal reveal-delay-3">
      <div class="why-icon purple"><i class="fas fa-user-friends"></i></div>
      <div>
        <h4>Built-in Roommate Finder</h4>
        <p>Split rent with compatible housemates. Our roommate feature helps you save money and find the right fit.</p>
      </div>
    </div>
    <div class="why-card reveal">
      <div class="why-icon blue"><i class="fas fa-map-marker-alt"></i></div>
      <div>
        <h4>Near Your Campus</h4>
        <p>All listings are tagged by university proximity. Find accommodation that's walking or cycling distance from your school.</p>
      </div>
    </div>
    <div class="why-card reveal reveal-delay-1">
      <div class="why-icon green"><i class="fas fa-headset"></i></div>
      <div>
        <h4>24/7 WhatsApp Support</h4>
        <p>Stuck or have questions? Our team is always available on WhatsApp to help you find the right hostel fast.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     ABOUT
============================================================ -->
<section id="about" class="about">
  <div class="about-inner">
    <div class="about-text reveal">
      <div class="section-tag">Who We Are</div>
      <h2>Making Student Housing Simpler &amp; Safer</h2>
      <p>HostelConnect is a trusted platform that helps students find safe, affordable, and convenient hostels close to their campuses. We make it easy to connect with compatible roommates, share accommodation, and cut living costs.<br><br>For landlords and agents, we provide a simple way to list properties, reach thousands of verified students, and manage rentals with ease.</p>
    </div>
    <div class="about-stat-grid reveal reveal-delay-2">
      <div class="about-stat-card"><i class="fas fa-building"></i><strong>500+</strong><span>Verified Hostels</span></div>
      <div class="about-stat-card"><i class="fas fa-users"></i><strong>3K+</strong><span>Happy Students</span></div>
      <div class="about-stat-card"><i class="fas fa-map-marker-alt"></i><strong>20+</strong><span>Cities Covered</span></div>
      <div class="about-stat-card"><i class="fas fa-shield-alt"></i><strong>100%</strong><span>Verified Listings</span></div>
    </div>
  </div>
</section>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->
<section class="testi-section">
  <div class="section-head reveal">
    <div class="section-tag" style="justify-content:center;">Student Reviews</div>
    <h2>What Students Are Saying</h2>
    <p>Real experiences from students who found their homes through HostelConnect</p>
  </div>
  <div class="testi-track-wrap">
    <div class="testi-track">
      <!-- Card 1 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">Found a clean, affordable self-contain 5 minutes from my faculty within 2 days. Didn't have to deal with any agents or pay unnecessary fees. This app is a lifesaver!</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#0b68b1,#064d8a)">A</div>
          <div><div class="testi-name">Adaeze O.</div><div class="testi-school">UNN — 300 Level</div></div>
        </div>
      </div>
      <!-- Card 2 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">I used the roommate finder and matched with someone from my department! We share a 2-bedroom and split everything. Saved me almost ₦150k per year. Very impressed.</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#cf6c17,#a85510)">K</div>
          <div><div class="testi-name">Kehinde B.</div><div class="testi-school">LASU — 200 Level</div></div>
        </div>
      </div>
      <!-- Card 3 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
        <p class="testi-text">The filters are really helpful. I set my max budget and it showed me exactly what I could afford near OAU. Spoke directly with the landlord and moved in the same week!</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#16a34a,#0d7a30)">T</div>
          <div><div class="testi-name">Tobi A.</div><div class="testi-school">OAU — Final Year</div></div>
        </div>
      </div>
      <!-- Card 4 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">As a fresh 100L student from Kogi, I was scared of being scammed. HostelConnect's verified listings gave me confidence. Found a hostel with WiFi and 24/7 light near UNIABUJA.</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#7c3aed,#5b21b6)">M</div>
          <div><div class="testi-name">Musa I.</div><div class="testi-school">UNIABUJA — 100 Level</div></div>
        </div>
      </div>
      <!-- Card 5 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">WhatsApp support was super fast! I had a question about a listing late at night and they replied within minutes. Rare to find this level of customer care in Nigeria, honestly.</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#db2777,#9d174d)">F</div>
          <div><div class="testi-name">Fatima S.</div><div class="testi-school">BUK — 400 Level</div></div>
        </div>
      </div>
      <!-- Card 6 -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">I listed my 3 hostels and got 12 student inquiries in the first week. The platform is clean, easy for students to navigate, and the team was very helpful during setup.</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#0b68b1,#cf6c17)">E</div>
          <div><div class="testi-name">Emeka C.</div><div class="testi-school">Landlord — Ibadan</div></div>
        </div>
      </div>
      <!-- Duplicate for seamless scroll -->
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">Found a clean, affordable self-contain 5 minutes from my faculty within 2 days. Didn't have to deal with any agents or pay unnecessary fees. This app is a lifesaver!</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#0b68b1,#064d8a)">A</div>
          <div><div class="testi-name">Adaeze O.</div><div class="testi-school">UNN — 300 Level</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p class="testi-text">I used the roommate finder and matched with someone from my department! We share a 2-bedroom and split everything. Saved me almost ₦150k per year. Very impressed.</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#cf6c17,#a85510)">K</div>
          <div><div class="testi-name">Kehinde B.</div><div class="testi-school">LASU — 200 Level</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
        <p class="testi-text">The filters are really helpful. I set my max budget and it showed me exactly what I could afford near OAU. Spoke directly with the landlord and moved in the same week!</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#16a34a,#0d7a30)">T</div>
          <div><div class="testi-name">Tobi A.</div><div class="testi-school">OAU — Final Year</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     HOW IT WORKS / OPTIONS
============================================================ -->
<section id="options" class="how-section">
  <div class="section-head reveal">
    <div class="section-tag" style="justify-content:center;">Get Started</div>
    <h2>What Are You Looking For?</h2>
    <p>HostelConnect serves students, roommate seekers, and landlords all in one place</p>
  </div>
  <div class="options-grid">
    <div class="option-card reveal">
      <div class="option-icon"><i class="fas fa-building"></i></div>
      <h3>Looking for a Hostel?</h3>
      <p>Browse hundreds of verified hostel listings near your school. Filter by price, type, and location to find your perfect fit.</p>
      <div class="option-btn-group">
        <a href="hostel.php" class="opt-btn-primary"><i class="fas fa-search"></i> Browse Hostels</a>
      </div>
    </div>
    <div class="option-card reveal reveal-delay-1">
      <div class="option-icon"><i class="fas fa-user-friends"></i></div>
      <h3>Need a Roommate?</h3>
      <p>Find compatible roommates who match your budget, schedule, and lifestyle. Post a request and let the right match find you.</p>
      <div class="option-btn-group">
        <a href="roommates.php" class="opt-btn-primary"><i class="fas fa-search"></i> Find a Roommate</a>
        <a href="student/request_roommate.php" class="opt-btn-outline"><i class="fas fa-plus-circle"></i> Post a Request</a>
      </div>
    </div>
    <div class="option-card reveal reveal-delay-2">
      <div class="option-icon"><i class="fas fa-key"></i></div>
      <h3>Are You a Landlord?</h3>
      <p>List your hostels and connect with thousands of verified students looking for accommodation near campus — completely free.</p>
      <div class="option-btn-group">
        <a href="register.php" class="opt-btn-primary opt-btn-accent"><i class="fas fa-plus"></i> List Your Property</a>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FAQ
============================================================ -->
<section id="faq" class="faq-section">
  <div class="section-head reveal">
    <div class="section-tag" style="justify-content:center;">Got Questions?</div>
    <h2>Frequently Asked Questions</h2>
    <p>Everything students usually ask before getting started</p>
  </div>
  <div class="faq-list">
    <div class="faq-item reveal">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>Is HostelConnect completely free for students?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>Yes, 100% free. You can search listings, view hostel details, contact landlords, and use the roommate finder all without paying a single naira. We never charge students to use any feature on the platform.</p></div>
    </div>
    <div class="faq-item reveal reveal-delay-1">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>How do I know the listings are real and not scams?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>Every listing goes through a manual verification process before it's approved. We verify landlord identity, property location, and photos. If you ever encounter a suspicious listing, you can report it instantly and our team will investigate.</p></div>
    </div>
    <div class="faq-item reveal reveal-delay-2">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>Can I contact the landlord directly without an agent?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>Absolutely. HostelConnect connects you directly with landlords or property managers — no agents involved. This means no agency fees and no middleman inflating the price. You negotiate and agree directly.</p></div>
    </div>
    <div class="faq-item reveal">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>How does the roommate finder work?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>Create a roommate request with your budget, preferred area, school, and lifestyle preferences. Other students searching for the same can find and contact you, or you can browse their requests and reach out. It's simple, fast, and free.</p></div>
    </div>
    <div class="faq-item reveal reveal-delay-1">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>I'm a landlord — how do I list my hostel?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>Register as a landlord or agent on HostelConnect, fill in your property details, upload photos, and set your pricing. Your listing will go live after a quick review by our team — usually within 24 hours. Listing is free.</p></div>
    </div>
    <div class="faq-item reveal reveal-delay-2">
      <div class="faq-q" onclick="toggleFaq(this)">
        <span>What cities and schools are currently covered?</span>
        <div class="faq-icon"><i class="fas fa-plus"></i></div>
      </div>
      <div class="faq-a"><p>We currently have listings across 20+ Nigerian cities including Lagos, Ibadan, Ile-Ife, Akure, Abuja, Benin City, Kano, and more — covering major federal and state universities. We're expanding rapidly, so check back often!</p></div>
    </div>
  </div>
</section>

<!-- ============================================================
     NEWSLETTER
============================================================ -->
<section class="newsletter-section">
  <div class="newsletter-inner reveal">
    <div class="section-tag" style="color:rgba(255,255,255,.45);justify-content:center;">
      <span style="display:inline-flex;align-items:center;gap:8px;"><span style="width:22px;height:2px;background:rgba(255,255,255,.3);border-radius:2px;display:inline-block;"></span>Stay Updated</span>
    </div>
    <h2>Get Hostel Alerts Near Your School</h2>
    <p>Drop your email and be the first to know when new verified hostels go live in your area — before they get taken.</p>
    <div class="newsletter-form">
      <input type="email" placeholder="Enter your email address…">
      <button type="button" onclick="handleNewsletter(this)">
        <i class="fas fa-bell"></i> Notify Me
      </button>
    </div>
    <p class="newsletter-note">No spam ever. Unsubscribe anytime. We only send hostel alerts.</p>
  </div>
</section>

<!-- ============================================================
     CONTACT
============================================================ -->
<section id="contact" class="contact">
  <div class="contact-inner reveal">
    <div class="section-tag">Get In Touch</div>
    <h2>Have Questions?<br>We're Here to Help</h2>
    <p>Chat with our friendly support team directly on WhatsApp — we typically respond within minutes.</p>
    <a href="https://chat.whatsapp.com/GLWo3a93fVK2Ws3EClxfDL" target="_blank" rel="noopener noreferrer" class="whatsapp-btn">
      <i class="fab fa-whatsapp"></i> Chat on WhatsApp
    </a>
  </div>
</section>

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
          <li><a href="#about">About Us</a></li>
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
     SCRIPTS — ALL ORIGINAL LOGIC PRESERVED + NEW UI HELPERS
============================================================ -->
<script>
(() => {
  /* ── Core listing logic (unchanged) ── */
  let data = [];
  const grid        = document.getElementById('grid');
  const city        = document.getElementById('city');
  const area        = document.getElementById('area');
  const minp        = document.getElementById('minp');
  const maxp        = document.getElementById('maxp');
  const type        = document.getElementById('type');
  const sort        = document.getElementById('sort');
  const searchInput = document.getElementById('searchInput');
  const pagination  = document.getElementById('pagination');
  let currentPage   = 1;
  const perPage     = 8;

  function toNGN(n){
    return new Intl.NumberFormat('en-NG',{style:'currency',currency:'NGN',maximumFractionDigits:0}).format(n);
  }

  function render(list){
    grid.innerHTML='';
    if(list.length===0){
      grid.innerHTML=`<div style="grid-column:1/-1;text-align:center;padding:70px 20px;"><i class="fas fa-search" style="font-size:3rem;color:var(--border);display:block;margin-bottom:16px;"></i><p style="color:var(--muted);">No listings match your filters. Try adjusting your search.</p></div>`;
      pagination.innerHTML='';
      return;
    }
    const start=(currentPage-1)*perPage;
    const pageItems=list.slice(start,start+perPage);
    pageItems.forEach((x,idx)=>{
      const el=document.createElement('article');
      el.className='card';
      const isNew = idx < 3; // first 3 get a "New" badge
      el.innerHTML=`
        <div class="cover">
          <span class="card-type-badge">${x.type||'Hostel'}</span>
          ${isNew?'<span class="card-badge-new">New</span>':''}
          ${x.photos&&x.photos.length>0
            ?`<img src="listing/uploads/${x.photos[0]}" alt="${x.title}" loading="lazy">`
            :`<div class="no-photo"><i class="fas fa-image"></i><span>No photo yet</span></div>`}
        </div>
        <div class="card-body">
          <div class="price">${toNGN(x.price)}<span class="per"> / ${x.period}</span></div>
          <div class="card-title">${x.title}</div>
          <div class="card-loc"><i class="fas fa-map-marker-alt"></i>${x.city} &bull; ${x.area}</div>
          <span class="avail-badge ${x.availability==='available'?'yes':'no'}">
            ${x.availability==='available'?'✅ Available Now':'❌ Not Available'}
          </span>
          <div class="tags">${(x.facilities||[]).map(t=>`<span class="tag">${t}</span>`).join('')}</div>
          <div class="actions">
            <a class="ghost" href="details.php?slug=${x.slug}"><i class="fas fa-eye"></i> View Details</a>
          </div>
        </div>`;
      grid.appendChild(el);
    });
    renderPagination(list.length);
  }

  function renderPagination(total){
    const pages=Math.ceil(total/perPage);
    pagination.innerHTML='';
    if(pages>1){
      const prev=document.createElement('button');
      prev.innerHTML='← Prev';prev.disabled=currentPage===1;
      prev.onclick=()=>{if(currentPage>1){currentPage--;apply();window.scrollTo({top:document.getElementById('listings').offsetTop-90,behavior:'smooth'});}};
      pagination.appendChild(prev);
      const info=document.createElement('span');
      info.textContent=`Page ${currentPage} of ${pages}`;
      pagination.appendChild(info);
      const next=document.createElement('button');
      next.innerHTML='Next →';next.disabled=currentPage===pages;
      next.onclick=()=>{if(currentPage<pages){currentPage++;apply();window.scrollTo({top:document.getElementById('listings').offsetTop-90,behavior:'smooth'});}};
      pagination.appendChild(next);
    }
  }

  function apply(){
    let list=data.slice();
    if(city.value)list=list.filter(x=>x.city===city.value);
    if(area.value.trim())list=list.filter(x=>x.area.toLowerCase().includes(area.value.trim().toLowerCase()));
    const min=+minp.value||0,max=+maxp.value||Infinity;
    list=list.filter(x=>x.price>=min&&x.price<=max);
    if(type.value)list=list.filter(x=>x.type===type.value);
    if(searchInput.value.trim())list=list.filter(x=>
      x.title.toLowerCase().includes(searchInput.value.trim().toLowerCase())||
      x.area.toLowerCase().includes(searchInput.value.trim().toLowerCase())||
      x.city.toLowerCase().includes(searchInput.value.trim().toLowerCase())
    );
    list.sort((a,b)=>sort.value==='asc'?a.price-b.price:b.price-a.price);
    render(list);
  }

  [city,area,minp,maxp,type,sort,searchInput].forEach(el=>el.addEventListener('input',()=>{currentPage=1;apply();}));
  window.applyFilters=()=>{currentPage=1;apply();};

  async function loadData(){
    try{
      const res=await fetch("fetch_listings.php");
      data=await res.json();
      city.innerHTML='<option value="">All Cities</option>';
      [...new Set(data.map(d=>d.city))].sort().forEach(c=>{
        const o=document.createElement('option');o.textContent=c;o.value=c;city.appendChild(o);
      });
      apply();
    }catch(err){
      console.error("Failed to load listings:",err);
      grid.innerHTML='<p style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">Could not load listings. Please refresh.</p>';
    }
  }

  /* ── Nav toggle ── */
  const toggle=document.getElementById('menuToggle');
  const navMenu=document.getElementById('navMenu');
  toggle.addEventListener('click',()=>{
    const open=navMenu.classList.toggle('show');
    toggle.classList.toggle('open',open);
    toggle.textContent=open?'✕':'☰';
  });
  navMenu.querySelectorAll('a').forEach(a=>{
    a.addEventListener('click',()=>{navMenu.classList.remove('show');toggle.classList.remove('open');toggle.textContent='☰';});
  });

  /* ── Scroll reveal ── */
  const ro=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible');});
  },{threshold:0.08,rootMargin:'0px 0px -40px 0px'});
  document.querySelectorAll('.reveal').forEach(el=>ro.observe(el));

  loadData();
})();

/* ── FAQ accordion ── */
function toggleFaq(btn){
  const item=btn.closest('.faq-item');
  const isOpen=item.classList.contains('open');
  document.querySelectorAll('.faq-item.open').forEach(i=>i.classList.remove('open'));
  if(!isOpen)item.classList.add('open');
}

/* ── Newsletter handler ── */
function handleNewsletter(btn){
  const input=btn.previousElementSibling;
  const email=input.value.trim();
  if(!email||!email.includes('@')){
    input.style.border='1.5px solid #dc2626';
    input.focus();
    return;
  }
  input.style.border='';
  btn.innerHTML='<i class="fas fa-check"></i> You\'re on the list!';
  btn.style.background='linear-gradient(135deg,#16a34a,#0d7a30)';
  btn.disabled=true;
  input.value='';
  input.placeholder='Thanks! We\'ll keep you posted 🎉';
}
</script>
</body>
</html>