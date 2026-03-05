<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Roommates • HostelConnect</title>
  <meta name="description" content="Find compatible student roommates near your campus on HostelConnect. Filter by gender, religion, budget and area.">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
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
  --purple:#7c3aed;
  --purple-bg:#f3e8ff;
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
   PAGE HERO
============================================================ */
.page-hero{
  position:relative;
  background:linear-gradient(140deg,#0b68b1 0%,#07438e 50%,#03295a 100%);
  padding:56px 24px 68px;
  overflow:hidden;text-align:center;
}
.page-hero::before{content:'';position:absolute;inset:0;background:url('bg.jpg') center/cover no-repeat;opacity:.07;z-index:0}
.ph-orb{position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none;z-index:0}
.ph-orb.o1{width:500px;height:500px;background:radial-gradient(circle,rgba(124,58,237,.25) 0%,transparent 70%);top:-150px;right:-80px;animation:orbDrift 12s ease-in-out infinite alternate}
.ph-orb.o2{width:350px;height:350px;background:radial-gradient(circle,rgba(207,108,23,.18) 0%,transparent 70%);bottom:-80px;left:5%;animation:orbDrift 18s ease-in-out infinite alternate-reverse}
@keyframes orbDrift{from{transform:translate(0,0) scale(1)}to{transform:translate(30px,20px) scale(1.08)}}
/* Floating icons decoration */
.ph-floats{position:absolute;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.ph-float{position:absolute;font-size:1.6rem;opacity:.07;animation:floatUp 16s linear infinite}
.ph-float:nth-child(1){left:8%;animation-duration:14s;animation-delay:0s}
.ph-float:nth-child(2){left:22%;animation-duration:18s;animation-delay:3s}
.ph-float:nth-child(3){left:60%;animation-duration:13s;animation-delay:6s}
.ph-float:nth-child(4){left:80%;animation-duration:20s;animation-delay:1s}
.ph-float:nth-child(5){left:44%;animation-duration:17s;animation-delay:9s}
@keyframes floatUp{0%{transform:translateY(110vh);opacity:0}10%{opacity:.07}90%{opacity:.04}100%{transform:translateY(-10vh);opacity:0}}

.ph-inner{position:relative;z-index:1;max-width:660px;margin:0 auto}
.ph-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.88);font-size:.76rem;font-weight:600;padding:6px 16px;border-radius:100px;margin-bottom:20px;backdrop-filter:blur(8px);letter-spacing:.5px;text-transform:uppercase;animation:fadeSlideUp .7s .05s cubic-bezier(.22,1,.36,1) both}
.ph-inner h1{font-family:'Playfair Display',serif;font-size:clamp(1.9rem,5vw,3rem);font-weight:800;color:#fff;line-height:1.15;letter-spacing:-1px;margin-bottom:12px;animation:fadeSlideUp .7s .15s cubic-bezier(.22,1,.36,1) both}
.ph-inner h1 em{font-style:italic;color:#fbbf24}
.ph-inner p{color:rgba(255,255,255,.62);font-size:.97rem;margin-bottom:36px;font-weight:300;max-width:480px;margin-left:auto;margin-right:auto;animation:fadeSlideUp .7s .25s cubic-bezier(.22,1,.36,1) both}

/* CTA buttons inside hero */
.ph-ctas{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;animation:fadeSlideUp .7s .35s cubic-bezier(.22,1,.36,1) both}
.ph-cta-primary{
  display:inline-flex;align-items:center;gap:8px;
  padding:13px 26px;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--accent),var(--accent-dark));
  color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.92rem;
  text-decoration:none;box-shadow:0 6px 20px rgba(207,108,23,.35);
  transition:transform .2s,box-shadow .2s;
}
.ph-cta-primary:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(207,108,23,.45);color:#fff}
.ph-cta-ghost{
  display:inline-flex;align-items:center;gap:8px;
  padding:12px 24px;border-radius:var(--r-sm);
  background:rgba(255,255,255,.1);color:#fff;
  border:1.5px solid rgba(255,255,255,.25);
  font-family:'DM Sans',sans-serif;font-weight:600;font-size:.9rem;
  text-decoration:none;backdrop-filter:blur(8px);
  transition:background .2s,transform .2s;
}
.ph-cta-ghost:hover{background:rgba(255,255,255,.18);transform:translateY(-2px);color:#fff}

/* Stats row */
.ph-stats{display:flex;justify-content:center;gap:0;margin-top:44px;animation:fadeSlideUp .7s .5s cubic-bezier(.22,1,.36,1) both}
.ph-stat{text-align:center;padding:0 28px;position:relative}
.ph-stat:not(:last-child)::after{content:'';position:absolute;right:0;top:10%;bottom:10%;width:1px;background:rgba(255,255,255,.18)}
.ph-stat strong{display:block;font-size:1.6rem;font-weight:800;color:#fff;font-family:'Playfair Display',serif;line-height:1;margin-bottom:3px}
.ph-stat span{font-size:.72rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:1px}

/* ============================================================
   HOW IT WORKS MINI-STRIP
============================================================ */
.how-strip{
  background:var(--white);border-bottom:1px solid var(--border);
  padding:28px 24px;
}
.how-strip-inner{
  max-width:1100px;margin:0 auto;
  display:flex;justify-content:center;gap:0;flex-wrap:wrap;
}
.how-step{
  display:flex;align-items:center;gap:12px;
  padding:0 32px;position:relative;
  flex:1;min-width:180px;max-width:280px;
}
.how-step:not(:last-child)::after{
  content:'→';position:absolute;right:-4px;
  color:var(--border);font-size:1.2rem;
}
.how-step-icon{
  width:42px;height:42px;min-width:42px;border-radius:12px;
  background:var(--brand-light);color:var(--brand);
  display:flex;align-items:center;justify-content:center;font-size:1rem;
}
.how-step-text .step-title{font-weight:700;font-size:.88rem;color:var(--dark)}
.how-step-text .step-sub{font-size:.77rem;color:var(--muted)}

/* ============================================================
   MAIN LAYOUT
============================================================ */
.page-wrap{
  max-width:1260px;margin:0 auto;
  padding:40px 24px 80px;
  display:grid;
  grid-template-columns:260px 1fr;
  gap:28px;
  align-items:start;
}

/* ============================================================
   FILTER SIDEBAR
============================================================ */
.filter-sidebar{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:24px;
  box-shadow:var(--shadow-sm);
  position:sticky;top:92px;
  animation:fadeSlideUp .6s .1s cubic-bezier(.22,1,.36,1) both;
}
.filter-sidebar h3{
  font-family:'Playfair Display',serif;
  font-size:1.05rem;font-weight:700;color:var(--dark);
  margin-bottom:20px;
  display:flex;align-items:center;justify-content:space-between;
}
.filter-clear{font-family:'DM Sans',sans-serif;font-size:.75rem;font-weight:600;color:var(--accent);cursor:pointer;background:none;border:none;padding:0;transition:color .2s}
.filter-clear:hover{color:var(--accent-dark)}
.filter-group{margin-bottom:18px}
.filter-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);margin-bottom:8px;display:block}
.filter-input{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:'DM Sans',sans-serif;font-size:.87rem;color:var(--body);background:var(--bg);outline:none;transition:border-color .2s,box-shadow .2s,background .2s;appearance:none}
.filter-input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white)}
.filter-row{display:grid;grid-template-columns:1fr 1fr;gap:8px}

/* Gender chips */
.gender-chips{display:flex;gap:8px}
.g-chip{
  flex:1;padding:8px 10px;border-radius:var(--r-sm);
  font-size:.82rem;font-weight:600;cursor:pointer;text-align:center;
  border:1.5px solid var(--border);background:var(--bg);color:var(--body);
  transition:all .2s;user-select:none;
}
.g-chip:hover{border-color:var(--brand);color:var(--brand)}
.g-chip.male.active{background:#dbeafe;color:#1d4ed8;border-color:#93c5fd}
.g-chip.female.active{background:#fce7f3;color:#be185d;border-color:#f9a8d4}
.g-chip.any.active{background:var(--brand-light);color:var(--brand);border-color:rgba(11,104,177,.3)}

/* Availability toggle */
.avail-toggle{display:flex;gap:8px}
.av-chip{flex:1;padding:8px;border-radius:var(--r-sm);font-size:.8rem;font-weight:600;cursor:pointer;text-align:center;border:1.5px solid var(--border);background:var(--bg);color:var(--body);transition:all .2s;user-select:none}
.av-chip.active.yes{background:var(--green-bg);color:var(--green);border-color:rgba(22,163,74,.3)}
.av-chip.active.no{background:var(--red-bg);color:var(--red);border-color:rgba(220,38,38,.3)}
.av-chip.active.all{background:var(--brand-light);color:var(--brand);border-color:rgba(11,104,177,.3)}

/* Active pills */
.active-filters{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px}
.af-pill{display:inline-flex;align-items:center;gap:5px;background:var(--brand-light);color:var(--brand);border:1px solid rgba(11,104,177,.2);font-size:.74rem;font-weight:600;padding:4px 10px;border-radius:100px}
.af-pill button{background:none;border:none;cursor:pointer;color:var(--brand);font-size:.8rem;line-height:1;padding:0;display:flex;align-items:center}

/* ============================================================
   RESULTS PANEL
============================================================ */
.results-panel{animation:fadeSlideUp .6s .15s cubic-bezier(.22,1,.36,1) both}
.results-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.results-count{font-size:.9rem;color:var(--muted)}
.results-count strong{color:var(--dark);font-weight:700}

/* ============================================================
   ROOMMATE CARDS
============================================================ */
#grid{display:grid;gap:22px;grid-template-columns:repeat(auto-fill,minmax(270px,1fr))}

.card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  display:flex;flex-direction:column;
  transition:transform .35s cubic-bezier(.22,1,.36,1),box-shadow .35s;
  animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
  position:relative;
}
@keyframes cardIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.card:hover{transform:translateY(-7px);box-shadow:var(--shadow-lg)}

/* Avatar / photo area */
.cover{
  height:180px;background:linear-gradient(135deg,var(--brand-light),#c7e0f7);
  overflow:hidden;position:relative;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.cover img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.card:hover .cover img{transform:scale(1.06)}
.cover .avatar-placeholder{
  width:72px;height:72px;border-radius:50%;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-family:'Playfair Display',serif;
  font-size:1.8rem;font-weight:800;
  box-shadow:0 8px 24px rgba(11,104,177,.3);
}
/* Gender badge on cover */
.gender-badge{
  position:absolute;top:12px;right:12px;
  padding:4px 10px;border-radius:100px;
  font-size:.7rem;font-weight:700;letter-spacing:.4px;text-transform:uppercase;
  backdrop-filter:blur(8px);
}
.gender-badge.male{background:rgba(29,78,216,.85);color:#fff}
.gender-badge.female{background:rgba(190,24,93,.85);color:#fff}
.gender-badge.any{background:rgba(11,104,177,.85);color:#fff}

/* Availability badge */
.avail-dot{
  position:absolute;top:12px;left:12px;
  width:10px;height:10px;border-radius:50%;
  border:2px solid rgba(255,255,255,.9);
}
.avail-dot.yes{background:var(--green);box-shadow:0 0 8px rgba(22,163,74,.6)}
.avail-dot.no{background:var(--red)}

.card-body{padding:20px;flex:1;display:flex;flex-direction:column}

/* Name + campus */
.rm-name{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:3px}
.rm-campus{display:flex;align-items:center;gap:5px;color:var(--brand);font-size:.82rem;font-weight:600;margin-bottom:12px}
.rm-campus i{font-size:.76rem}

/* Info rows */
.rm-info{display:flex;flex-direction:column;gap:7px;margin-bottom:14px;flex:1}
.rm-row{display:flex;align-items:center;gap:8px;font-size:.85rem}
.rm-row i{width:16px;color:var(--muted);font-size:.8rem;flex-shrink:0}
.rm-row span{color:var(--body)}
.rm-row .label{color:var(--muted);margin-right:2px}
.rm-row strong{color:var(--dark);font-weight:600}

/* Budget pill */
.budget-pill{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--accent-light);color:var(--accent-dark);
  border:1px solid rgba(207,108,23,.15);
  border-radius:100px;padding:5px 12px;
  font-size:.8rem;font-weight:700;margin-bottom:14px;
  font-family:'Playfair Display',serif;
}

/* Tags row */
.pref-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px}
.pref-tag{
  display:inline-flex;align-items:center;gap:4px;
  font-size:.73rem;font-weight:600;
  padding:4px 10px;border-radius:100px;
  border:1px solid var(--border);background:var(--bg);color:var(--body);
}
.pref-tag.religion{background:var(--purple-bg);color:var(--purple);border-color:rgba(124,58,237,.15)}

/* Avail row */
.avail-row{
  display:flex;align-items:center;gap:6px;
  font-size:.8rem;font-weight:700;margin-bottom:16px;
}
.avail-row.yes{color:var(--green)}
.avail-row.no{color:var(--red)}
.avail-row i{font-size:.85rem}

/* CTA button */
.view-btn{
  display:flex;align-items:center;justify-content:center;gap:7px;
  padding:11px 18px;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.88rem;
  text-decoration:none;box-shadow:0 3px 10px rgba(11,104,177,.22);
  transition:transform .2s,box-shadow .2s;margin-top:auto;
}
.view-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(11,104,177,.35);color:#fff}

/* Empty state */
.empty-state{
  grid-column:1/-1;text-align:center;padding:80px 20px;
}
.empty-state i{font-size:3.5rem;color:var(--border);display:block;margin-bottom:18px}
.empty-state h3{font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--dark);margin-bottom:8px}
.empty-state p{color:var(--muted);font-size:.9rem;margin-bottom:20px}
.empty-state button{padding:11px 24px;border:none;border-radius:var(--r-sm);background:var(--brand);color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.9rem;cursor:pointer;transition:background .2s}
.empty-state button:hover{background:var(--brand-dark)}

/* Skeleton */
.skeleton-grid{display:grid;gap:22px;grid-template-columns:repeat(auto-fill,minmax(270px,1fr))}
.skeleton-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);overflow:hidden}
.skel-img{height:180px;background:linear-gradient(90deg,var(--bg) 25%,var(--border) 50%,var(--bg) 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
.skel-body{padding:18px}
.skel-line{height:12px;border-radius:6px;margin-bottom:10px;background:linear-gradient(90deg,var(--bg) 25%,var(--border) 50%,var(--bg) 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
.skel-line.w60{width:60%}.skel-line.w40{width:40%}.skel-line.w75{width:75%}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* ============================================================
   PAGINATION
============================================================ */
#pagination{display:flex;justify-content:center;align-items:center;gap:8px;margin-top:48px;flex-wrap:wrap}
#pagination button{cursor:pointer;padding:10px 18px;border-radius:var(--r-sm);border:1.5px solid var(--border);background:var(--white);color:var(--brand);font-family:'DM Sans',sans-serif;font-weight:600;font-size:.85rem;transition:all .2s}
#pagination button:hover:not(:disabled){background:var(--brand);color:#fff;border-color:var(--brand);transform:translateY(-1px);box-shadow:0 4px 14px rgba(11,104,177,.22)}
#pagination button:disabled{opacity:.38;cursor:not-allowed}
#pagination button.active{background:var(--accent);color:white;border-color:var(--accent)}
#pagination span{color:var(--muted);font-size:.85rem;padding:0 4px}

/* ============================================================
   FLOATING WHATSAPP
============================================================ */
.float-wa{position:fixed;bottom:30px;right:28px;z-index:990;display:flex;align-items:center;gap:9px;background:#25D366;color:#fff;border-radius:100px;padding:12px 18px 12px 14px;text-decoration:none;font-weight:700;font-size:.86rem;box-shadow:0 6px 28px rgba(37,211,102,.4);transition:transform .25s,box-shadow .25s;animation:floatIn .6s 2s cubic-bezier(.22,1,.36,1) both}
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
  .how-strip-inner{gap:20px}
  .how-step:not(:last-child)::after{display:none}
}
@media(max-width:768px){
  header{padding:0 18px;height:66px}
  nav ul{display:none;flex-direction:column;position:fixed;top:66px;left:0;right:0;background:rgba(255,255,255,.97);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);padding:16px 18px 24px;box-shadow:0 12px 40px rgba(0,0,0,.12);z-index:998;gap:4px}
  nav ul.show{display:flex}
  nav ul li a{padding:12px 16px;font-size:.93rem}
  .menu-toggle{display:flex}
  .page-hero{padding:40px 18px 50px}
  .ph-ctas{flex-direction:column;align-items:center}
  .ph-stats{flex-wrap:wrap}
  .ph-stat{flex:0 0 45%;padding:0 12px}
  .ph-stat::after{display:none}
  .page-wrap{padding:24px 16px 60px;gap:16px}
  .filter-sidebar{grid-template-columns:1fr;padding:18px}
  .how-strip{overflow-x:auto}
  .how-strip-inner{flex-wrap:nowrap;justify-content:flex-start;padding:0 4px}
  .how-step{min-width:160px}
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
      <li><a href="hostel.php">Find Hostels</a></li>
      <li><a href="roommates.php" class="active">Find Roommates</a></li>
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
        <li><a href="/register">Register</a></li>
        <li><a href="/login" class="nav-cta">Login</a></li>
      <?php endif; ?>
    </ul>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">☰</button>
  </nav>
</header>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<div class="page-hero">
  <div class="ph-orb o1"></div>
  <div class="ph-orb o2"></div>
  <div class="ph-floats">
    <span class="ph-float"><i class="fas fa-user-friends"></i></span>
    <span class="ph-float"><i class="fas fa-home"></i></span>
    <span class="ph-float"><i class="fas fa-handshake"></i></span>
    <span class="ph-float"><i class="fas fa-heart"></i></span>
    <span class="ph-float"><i class="fas fa-users"></i></span>
  </div>
  <div class="ph-inner">
    <div class="ph-tag"><i class="fas fa-user-friends"></i> Roommate Finder</div>
    <h1>Find Your <em>Perfect</em><br>Roommate</h1>
    <p>Connect with verified students who share your budget, lifestyle, and campus. Split rent, share space, save money.</p>
    <div class="ph-ctas">
      <?php if($isLoggedIn): ?>
        <a href="student/request_roommate.php" class="ph-cta-primary">
          <i class="fas fa-plus-circle"></i> Post a Roommate Request
        </a>
      <?php else: ?>
        <a href="/login" class="ph-cta-primary">
          <i class="fas fa-plus-circle"></i> Post a Request
        </a>
      <?php endif; ?>
      <a href="#results" class="ph-cta-ghost">
        <i class="fas fa-search"></i> Browse Requests
      </a>
    </div>
    <div class="ph-stats">
      <div class="ph-stat"><strong id="totalRequests">—</strong><span>Active Requests</span></div>
      <div class="ph-stat"><strong id="availRequests">—</strong><span>Available Now</span></div>
      <div class="ph-stat"><strong>20+</strong><span>Campuses</span></div>
    </div>
  </div>
</div>

<!-- ============================================================
     HOW IT WORKS MINI-STRIP
============================================================ -->
<div class="how-strip">
  <div class="how-strip-inner">
    <div class="how-step">
      <div class="how-step-icon"><i class="fas fa-search"></i></div>
      <div class="how-step-text">
        <div class="step-title">Browse Requests</div>
        <div class="step-sub">Filter by campus &amp; budget</div>
      </div>
    </div>
    <div class="how-step">
      <div class="how-step-icon"><i class="fas fa-comment-dots"></i></div>
      <div class="how-step-text">
        <div class="step-title">Connect</div>
        <div class="step-sub">Message potential roommates</div>
      </div>
    </div>
    <div class="how-step">
      <div class="how-step-icon"><i class="fas fa-handshake"></i></div>
      <div class="how-step-text">
        <div class="step-title">Agree &amp; Move In</div>
        <div class="step-sub">Split rent, share a great space</div>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     PAGE BODY
============================================================ -->
<div class="page-wrap" id="results">

  <!-- ── FILTER SIDEBAR ── -->
  <aside class="filter-sidebar">
    <h3>
      <span><i class="fas fa-sliders-h" style="color:var(--brand);margin-right:8px;font-size:.9rem"></i>Filters</span>
      <button class="filter-clear" onclick="clearFilters()">Clear all</button>
    </h3>

    <div class="active-filters" id="activePills"></div>

    <div class="filter-group">
      <label class="filter-label" for="campus">Campus / School</label>
      <select class="filter-input" id="campus">
        <option value="">All Campuses</option>
      </select>
    </div>

    <div class="filter-group">
      <label class="filter-label" for="area">Preferred Area</label>
      <input class="filter-input" id="area" type="text" placeholder="e.g. Akoka, Agbowo…">
    </div>

    <div class="filter-group">
      <label class="filter-label">Gender Preference</label>
      <div class="gender-chips">
        <span class="g-chip any active" data-val="">Any</span>
        <span class="g-chip male" data-val="Male"><i class="fas fa-mars"></i> Male</span>
        <span class="g-chip female" data-val="Female"><i class="fas fa-venus"></i> Female</span>
      </div>
    </div>

    <div class="filter-group">
      <label class="filter-label" for="religion">Religion</label>
      <select class="filter-input" id="religion">
        <option value="">Any Religion</option>
        <option value="Christianity">Christianity</option>
        <option value="Islam">Islam</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <div class="filter-group">
      <label class="filter-label">Budget Range (₦)</label>
      <div class="filter-row">
        <input class="filter-input" id="minb" type="number" placeholder="Min">
        <input class="filter-input" id="maxb" type="number" placeholder="Max">
      </div>
    </div>

    <div class="filter-group">
      <label class="filter-label">Availability</label>
      <div class="avail-toggle">
        <span class="av-chip all active" data-val="">All</span>
        <span class="av-chip yes" data-val="available">Available</span>
        <span class="av-chip no" data-val="unavailable">Taken</span>
      </div>
    </div>
  </aside>

  <!-- ── RESULTS ── -->
  <div class="results-panel">
    <div class="results-header reveal">
      <div class="results-count" id="resultsCount">Loading roommate requests…</div>
      <?php if($isLoggedIn): ?>
      <a href="student/request_roommate.php" style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--r-sm);background:var(--accent);color:#fff;font-weight:700;font-size:.85rem;text-decoration:none;transition:background .2s">
        <i class="fas fa-plus"></i> Post Request
      </a>
      <?php endif; ?>
    </div>

    <!-- Skeleton loaders -->
    <div id="skeletons" class="skeleton-grid">
      <?php for($i=0;$i<8;$i++): ?>
      <div class="skeleton-card">
        <div class="skel-img"></div>
        <div class="skel-body">
          <div class="skel-line w60"></div>
          <div class="skel-line w75"></div>
          <div class="skel-line w40"></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>

    <div id="grid" style="display:none"></div>
    <div id="pagination"></div>
  </div>

</div>

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
          <li><a href="roommates.php">Find a Roommate</a></li>
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
============================================================ -->
<script>
(() => {
  let data = [];
  let activeGender = '';
  let activeAvail  = '';
  let currentPage  = 1;
  const perPage    = 12;

  // DOM
  const grid        = document.getElementById('grid');
  const skeletons   = document.getElementById('skeletons');
  const campus      = document.getElementById('campus');
  const area        = document.getElementById('area');
  const religion    = document.getElementById('religion');
  const minb        = document.getElementById('minb');
  const maxb        = document.getElementById('maxb');
  const pagination  = document.getElementById('pagination');
  const resultsCount= document.getElementById('resultsCount');
  const activePills = document.getElementById('activePills');

  // ── Gender chips ───────────────────────────────────────
  document.querySelectorAll('.g-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.g-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeGender = chip.dataset.val;
      currentPage = 1; apply();
    });
  });

  // ── Availability chips ─────────────────────────────────
  document.querySelectorAll('.av-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.av-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeAvail = chip.dataset.val;
      currentPage = 1; apply();
    });
  });

  // ── Active filter pills ────────────────────────────────
  function updatePills() {
    activePills.innerHTML = '';
    const pills = [
      {label:'Campus',   val:campus.value,    clear:"document.getElementById('campus').value='';applyNow()"},
      {label:'Area',     val:area.value,      clear:"document.getElementById('area').value='';applyNow()"},
      {label:'Gender',   val:activeGender,    clear:"clearGender()"},
      {label:'Religion', val:religion.value,  clear:"document.getElementById('religion').value='';applyNow()"},
      {label:'Min ₦',   val:minb.value?'₦'+Number(minb.value).toLocaleString():'', clear:"document.getElementById('minb').value='';applyNow()"},
      {label:'Max ₦',   val:maxb.value?'₦'+Number(maxb.value).toLocaleString():'', clear:"document.getElementById('maxb').value='';applyNow()"},
      {label:'Avail',    val:activeAvail==='available'?'Yes':activeAvail==='unavailable'?'No':'', clear:"clearAvail()"},
    ];
    pills.forEach(p => {
      if (!p.val) return;
      const pill = document.createElement('span');
      pill.className = 'af-pill';
      pill.innerHTML = `${p.label}: <strong>${p.val}</strong>
        <button onclick="${p.clear}"><i class="fas fa-times"></i></button>`;
      activePills.appendChild(pill);
    });
  }

  // ── Clear helpers ──────────────────────────────────────
  function clearGender() {
    activeGender = '';
    document.querySelectorAll('.g-chip').forEach(c => c.classList.toggle('active', c.dataset.val === ''));
    applyNow();
  }
  function clearAvail() {
    activeAvail = '';
    document.querySelectorAll('.av-chip').forEach(c => c.classList.toggle('active', c.dataset.val === ''));
    applyNow();
  }
  function clearFilters() {
    campus.value = ''; area.value = ''; religion.value = '';
    minb.value = ''; maxb.value = '';
    activeGender = ''; activeAvail = '';
    document.querySelectorAll('.g-chip').forEach(c => c.classList.toggle('active', c.dataset.val === ''));
    document.querySelectorAll('.av-chip').forEach(c => c.classList.toggle('active', c.dataset.val === ''));
    currentPage = 1; apply();
  }
  window.clearFilters = clearFilters;
  window.clearGender  = clearGender;
  window.clearAvail   = clearAvail;
  function applyNow(){ currentPage = 1; apply(); }
  window.applyNow = applyNow;

  // ── Initials avatar ────────────────────────────────────
  function initials(name) {
    if (!name || name === 'Anonymous') return '?';
    return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
  }

  // ── Render ─────────────────────────────────────────────
  function render(list) {
    grid.innerHTML = '';
    skeletons.style.display = 'none';
    grid.style.display = '';

    // hero stats
    document.getElementById('totalRequests').textContent = data.length + '+';
    document.getElementById('availRequests').textContent =
      data.filter(x => x.availability === 'available').length + '+';

    // count
    const start = (currentPage - 1) * perPage;
    const slice = list.slice(start, start + perPage);
    resultsCount.innerHTML = `Showing <strong>${slice.length}</strong> of <strong>${list.length}</strong> requests`;

    updatePills();

    if (list.length === 0) {
      grid.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-user-slash"></i>
          <h3>No roommate requests found</h3>
          <p>Try adjusting your filters or be the first to post a request for your campus!</p>
          <button onclick="clearFilters()"><i class="fas fa-redo" style="margin-right:6px"></i>Clear Filters</button>
        </div>`;
      pagination.innerHTML = '';
      return;
    }

    slice.forEach((x, idx) => {
      const card = document.createElement('article');
      card.className = 'card';
      card.style.animationDelay = (idx * 0.04) + 's';

      const isAvail   = x.availability === 'available';
      const genderVal = (x.gender_pref || '').toLowerCase();
      const genderClass = genderVal === 'male' ? 'male' : genderVal === 'female' ? 'female' : 'any';
      const genderLabel = x.gender_pref || 'Any';

      const budgetMin = Number(x.budget_min).toLocaleString('en-NG');
      const budgetMax = Number(x.budget_max).toLocaleString('en-NG');

      card.innerHTML = `
        <div class="cover">
          <div class="avail-dot ${isAvail ? 'yes' : 'no'}" title="${isAvail ? 'Available' : 'Not Available'}"></div>
          <span class="gender-badge ${genderClass}">${genderLabel}</span>
          ${x.photos?.length
            ? `<img src="student/uploads/roommates/${x.photos[0]}" alt="${x.requester_name || 'Roommate'}" loading="lazy">`
            : `<div class="avatar-placeholder">${initials(x.requester_name)}</div>`}
        </div>
        <div class="card-body">
          <div class="rm-name">${x.requester_name || 'Anonymous'}</div>
          <div class="rm-campus"><i class="fas fa-graduation-cap"></i>${x.campus}</div>
          <div class="budget-pill">
            <i class="fas fa-naira-sign" style="font-size:.7rem"></i>
            ₦${budgetMin} – ₦${budgetMax}
          </div>
          <div class="rm-info">
            <div class="rm-row">
              <i class="fas fa-map-marker-alt"></i>
              <span><span class="label">Area:</span> <strong>${x.area || 'Any'}</strong></span>
            </div>
            <div class="rm-row">
              <i class="fas fa-door-open"></i>
              <span><span class="label">Room type:</span> <strong>${x.room_type || 'Any'}</strong></span>
            </div>
          </div>
          <div class="pref-tags">
            ${x.gender_pref ? `<span class="pref-tag"><i class="fas fa-${genderVal === 'female' ? 'venus' : genderVal === 'male' ? 'mars' : 'genderless'}"></i> ${x.gender_pref}</span>` : ''}
            ${x.religion_pref ? `<span class="pref-tag religion"><i class="fas fa-praying-hands"></i> ${x.religion_pref}</span>` : ''}
          </div>
          <div class="avail-row ${isAvail ? 'yes' : 'no'}">
            <i class="fas fa-${isAvail ? 'check-circle' : 'times-circle'}"></i>
            ${isAvail ? 'Available Now' : 'No Longer Available'}
          </div>
          <a class="view-btn" href="roommate_detail.php?id=${x.id}">
            <i class="fas fa-user"></i> View Profile
          </a>
        </div>`;
      grid.appendChild(card);
    });

    renderPagination(list.length);
  }

  // ── Pagination ─────────────────────────────────────────
  function renderPagination(total) {
    const pages = Math.ceil(total / perPage);
    pagination.innerHTML = '';
    if (pages <= 1) return;

    const prev = document.createElement('button');
    prev.innerHTML = '← Prev'; prev.disabled = currentPage === 1;
    prev.onclick = () => { currentPage--; apply(); scrollUp(); };
    pagination.appendChild(prev);

    const range = 2;
    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - range && p <= currentPage + range)) {
        const btn = document.createElement('button');
        btn.textContent = p;
        if (p === currentPage) btn.classList.add('active');
        btn.onclick = () => { currentPage = p; apply(); scrollUp(); };
        pagination.appendChild(btn);
      } else if (p === currentPage - range - 1 || p === currentPage + range + 1) {
        const dots = document.createElement('span'); dots.textContent = '…';
        pagination.appendChild(dots);
      }
    }

    const next = document.createElement('button');
    next.innerHTML = 'Next →'; next.disabled = currentPage === pages;
    next.onclick = () => { currentPage++; apply(); scrollUp(); };
    pagination.appendChild(next);
  }

  function scrollUp() {
    window.scrollTo({ top: document.getElementById('results').offsetTop - 90, behavior: 'smooth' });
  }

  // ── Apply filters (original logic preserved) ───────────
  function apply() {
    let list = data.slice();
    if (campus.value)      list = list.filter(x => x.campus === campus.value);
    if (area.value.trim()) list = list.filter(x => x.area?.toLowerCase().includes(area.value.trim().toLowerCase()));
    if (activeGender)      list = list.filter(x => x.gender_pref === activeGender);
    if (religion.value)    list = list.filter(x => x.religion_pref === religion.value);
    if (activeAvail)       list = list.filter(x => x.availability === activeAvail);
    const min = +minb.value || 0, max = +maxb.value || Infinity;
    list = list.filter(x => x.budget_min >= min && x.budget_max <= max);
    render(list);
  }

  [campus, area, religion, minb, maxb].forEach(el =>
    el.addEventListener('input', () => { currentPage = 1; apply(); })
  );

  // ── Load data ──────────────────────────────────────────
  async function loadData() {
    try {
      const res = await fetch("fetch_roommates.php");
      data = await res.json();

      campus.innerHTML = '<option value="">All Campuses</option>';
      [...new Set(data.map(d => d.campus))].sort().forEach(c => {
        const o = document.createElement('option');
        o.textContent = c; o.value = c;
        campus.appendChild(o);
      });

      apply();
    } catch (err) {
      console.error("Failed to load roommates:", err);
      skeletons.style.display = 'none';
      grid.style.display = '';
      grid.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-exclamation-triangle"></i>
          <h3>Could not load requests</h3>
          <p>Please refresh the page or try again shortly.</p>
        </div>`;
    }
  }

  // ── Nav toggle ─────────────────────────────────────────
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

  // ── Scroll reveal ──────────────────────────────────────
  const ro = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.07 });
  document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

  loadData();
})();
</script>
</body>
</html>