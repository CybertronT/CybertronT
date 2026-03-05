<?php
session_start();
require "../config.php";

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../login.php?redirect=$redirect");
    exit;
}
$allowed_roles = ['student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name    = $_SESSION['name'] ?? '';
$contact = $_SESSION['contact'] ?? '';
$role    = $_SESSION['role'] ?? 'student';

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

// Stats
$totalRequests = $approvedRequests = $pendingRequests = 0;

$stmt = $conn->prepare("SELECT COUNT(*) FROM roommate_requests WHERE user_id = ?");
$stmt->bind_param("i", $user_id); $stmt->execute(); $stmt->bind_result($totalRequests); $stmt->fetch(); $stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM roommate_requests WHERE user_id = ? AND status = 'approved'");
$stmt->bind_param("i", $user_id); $stmt->execute(); $stmt->bind_result($approvedRequests); $stmt->fetch(); $stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM roommate_requests WHERE user_id = ? AND status = 'pending'");
$stmt->bind_param("i", $user_id); $stmt->execute(); $stmt->bind_result($pendingRequests); $stmt->fetch(); $stmt->close();

$recent = $conn->query("SELECT * FROM roommate_requests WHERE status='approved' AND user_id != $user_id ORDER BY id DESC LIMIT 5");
$mine   = $conn->query("SELECT * FROM roommate_requests WHERE user_id = $user_id ORDER BY id DESC");

// Name initials for avatar
function initials($name) {
    $parts = array_filter(explode(' ', $name));
    return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($parts, 0, 2))));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard — HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="../logo.png">
  <style>
/* ============================================================
   RESET & VARIABLES
============================================================ */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#0b68b1;--brand-dark:#084e87;--brand-light:#dff0ff;
  --accent:#cf6c17;--accent-dark:#a85510;--accent-light:#fff0e3;
  --dark:#0d1117;--body:#374151;--muted:#8b95a1;
  --border:#e4e9ef;--bg:#f4f7fc;--white:#ffffff;
  --green:#16a34a;--green-bg:#dcfce7;
  --red:#dc2626;--red-bg:#fee2e2;
  --yellow:#f59e0b;--yellow-bg:#fef3c7;
  --sidebar-w:248px;
  --topbar-h:68px;
  --shadow-sm:0 2px 12px rgba(11,104,177,.07);
  --shadow-md:0 8px 32px rgba(11,104,177,.12);
  --r:14px;--r-sm:9px;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);display:flex;min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:10px}

/* ============================================================
   SIDEBAR
============================================================ */
.sidebar{
  width:var(--sidebar-w);min-width:var(--sidebar-w);
  background:linear-gradient(180deg,#0a5fa0 0%,#073d6e 100%);
  display:flex;flex-direction:column;
  position:fixed;top:0;bottom:0;left:0;
  z-index:200;
  transition:transform .3s cubic-bezier(.22,1,.36,1);
  box-shadow:4px 0 24px rgba(11,104,177,.18);
}

/* Sidebar brand */
.sb-brand{
  display:flex;align-items:center;gap:10px;
  padding:22px 20px 18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  margin-bottom:10px;
}
.sb-brand img{width:36px;height:36px;border-radius:8px}
.sb-brand span{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#fff;letter-spacing:-.2px}

/* Sidebar nav */
.sb-nav{flex:1;padding:8px 12px;overflow-y:auto}
.sb-section{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.28);padding:12px 8px 6px;margin-top:4px}
.sb-link{
  display:flex;align-items:center;gap:11px;
  padding:11px 12px;border-radius:var(--r-sm);
  color:rgba(255,255,255,.65);font-size:.88rem;font-weight:500;
  text-decoration:none;margin-bottom:2px;
  transition:background .2s,color .2s;
  position:relative;
}
.sb-link i{width:18px;text-align:center;font-size:.9rem;flex-shrink:0}
.sb-link:hover{background:rgba(255,255,255,.1);color:#fff}
.sb-link.active{background:rgba(255,255,255,.15);color:#fff;font-weight:600}
.sb-link.active::before{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:#fbbf24;border-radius:0 3px 3px 0}
.sb-link.danger{color:rgba(255,120,120,.75)}
.sb-link.danger:hover{background:rgba(220,38,38,.15);color:#fca5a5}

/* Sidebar footer */
.sb-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.08)}
.sb-user{display:flex;align-items:center;gap:10px}
.sb-avatar{
  width:36px;height:36px;min-width:36px;border-radius:50%;
  background:linear-gradient(135deg,rgba(255,255,255,.25),rgba(255,255,255,.1));
  border:2px solid rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;
  font-family:'Playfair Display',serif;font-size:.85rem;font-weight:800;color:#fff;
}
.sb-user-info .sb-name{font-size:.85rem;font-weight:700;color:#fff;line-height:1.2}
.sb-user-info .sb-role{font-size:.72rem;color:rgba(255,255,255,.4);text-transform:capitalize}

/* ============================================================
   MAIN AREA
============================================================ */
.main{
  flex:1;margin-left:var(--sidebar-w);
  display:flex;flex-direction:column;
  min-height:100vh;
  transition:margin-left .3s cubic-bezier(.22,1,.36,1);
}

/* ============================================================
   TOPBAR
============================================================ */
.topbar{
  height:var(--topbar-h);
  display:flex;align-items:center;justify-content:space-between;
  background:var(--white);
  padding:0 28px;
  border-bottom:1px solid var(--border);
  box-shadow:var(--shadow-sm);
  position:sticky;top:0;z-index:100;
  gap:16px;
}
.topbar-left{display:flex;align-items:center;gap:14px}
.menu-toggle{
  display:none;width:40px;height:40px;border-radius:var(--r-sm);
  background:var(--brand-light);border:1.5px solid rgba(11,104,177,.15);
  color:var(--brand);font-size:1rem;cursor:pointer;
  align-items:center;justify-content:center;transition:background .2s;
}
.menu-toggle:hover{background:#cce4f8}
.topbar-title{font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:var(--dark)}
.topbar-right{display:flex;align-items:center;gap:10px}
.topbar-welcome{font-size:.87rem;color:var(--muted);font-weight:500}
.topbar-welcome strong{color:var(--dark)}
.topbar-badge{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--brand-light);color:var(--brand);
  border:1px solid rgba(11,104,177,.15);
  border-radius:100px;padding:5px 12px;font-size:.76rem;font-weight:700;
  text-transform:capitalize;
}

/* ============================================================
   CONTENT
============================================================ */
.content{padding:28px;flex:1}

/* Alerts */
.alert{
  border-radius:var(--r-sm);padding:13px 16px;
  font-size:.87rem;display:flex;align-items:flex-start;gap:10px;
  margin-bottom:22px;
}
.alert i{margin-top:1px;flex-shrink:0}
.alert.success{background:var(--green-bg);color:var(--green);border:1px solid rgba(22,163,74,.2)}
.alert.error{background:var(--red-bg);color:var(--red);border:1px solid rgba(220,38,38,.2)}

/* Page intro */
.page-intro{margin-bottom:28px}
.page-intro h2{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:800;color:var(--dark);letter-spacing:-.3px;margin-bottom:4px}
.page-intro p{color:var(--muted);font-size:.9rem}

/* ============================================================
   STAT CARDS
============================================================ */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:28px}
.stat-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:22px 24px;
  box-shadow:var(--shadow-sm);
  display:flex;align-items:center;gap:18px;
  transition:transform .25s,box-shadow .25s;
}
.stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.stat-icon{
  width:52px;height:52px;min-width:52px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;font-size:1.2rem;
}
.stat-icon.blue{background:var(--brand-light);color:var(--brand)}
.stat-icon.green{background:var(--green-bg);color:var(--green)}
.stat-icon.yellow{background:var(--yellow-bg);color:var(--yellow)}
.stat-info .stat-value{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:800;color:var(--dark);line-height:1;margin-bottom:3px}
.stat-info .stat-label{font-size:.78rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.8px}

/* ============================================================
   QUICK ACTIONS
============================================================ */
.actions-row{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:28px}
.action-btn{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 20px;border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:700;
  text-decoration:none;border:none;cursor:pointer;
  transition:transform .2s,box-shadow .2s,background .2s;
}
.action-btn:hover{transform:translateY(-2px)}
.action-btn.primary{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;box-shadow:0 3px 12px rgba(11,104,177,.25)}
.action-btn.primary:hover{box-shadow:0 6px 20px rgba(11,104,177,.35)}
.action-btn.accent{background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;box-shadow:0 3px 12px rgba(207,108,23,.22)}
.action-btn.accent:hover{box-shadow:0 6px 20px rgba(207,108,23,.35)}
.action-btn.ghost{background:var(--white);color:var(--brand);border:1.5px solid rgba(11,104,177,.2)}
.action-btn.ghost:hover{background:var(--brand-light)}

/* ============================================================
   SECTION CARDS
============================================================ */
.section-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:24px;
  box-shadow:var(--shadow-sm);margin-bottom:22px;
}
.section-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;padding-bottom:14px;
  border-bottom:2px solid var(--bg);
  flex-wrap:wrap;gap:10px;
}
.section-header h3{
  font-family:'Playfair Display',serif;
  font-size:1.05rem;font-weight:700;color:var(--dark);
  display:flex;align-items:center;gap:8px;
}
.section-header h3 i{color:var(--brand);font-size:.95rem}
.section-link{
  font-size:.82rem;font-weight:600;color:var(--brand);
  text-decoration:none;display:inline-flex;align-items:center;gap:5px;
  transition:color .2s;
}
.section-link:hover{color:var(--brand-dark)}

/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:500px;font-size:.87rem}
thead th{
  padding:11px 14px;text-align:left;
  font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  color:var(--muted);background:var(--bg);
  border-bottom:1px solid var(--border);
}
thead th:first-child{border-radius:var(--r-sm) 0 0 0}
thead th:last-child{border-radius:0 var(--r-sm) 0 0}
tbody td{padding:13px 14px;border-bottom:1px solid var(--border);color:var(--body);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafbfd}

/* Status badges */
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:100px;font-size:.75rem;font-weight:700}
.badge.approved{background:var(--green-bg);color:var(--green)}
.badge.pending{background:var(--yellow-bg);color:var(--yellow)}
.badge.rejected{background:var(--red-bg);color:var(--red)}

/* Empty state */
.empty-state{text-align:center;padding:40px 20px;color:var(--muted)}
.empty-state i{font-size:2.5rem;color:var(--border);display:block;margin-bottom:12px}
.empty-state p{font-size:.9rem}

/* ============================================================
   FOOTER
============================================================ */
.dash-footer{
  text-align:center;padding:18px;
  font-size:.8rem;color:var(--muted);
  border-top:1px solid var(--border);background:var(--white);
}

/* Overlay for mobile sidebar */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199;backdrop-filter:blur(2px)}
.sb-overlay.show{display:block}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:900px){
  .sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
  .sidebar.show{transform:translateX(0)}
  .main{margin-left:0}
  .menu-toggle{display:flex}
  .stats-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:600px){
  .content{padding:18px 16px}
  .stats-grid{grid-template-columns:1fr}
  .topbar{padding:0 18px}
  .actions-row{flex-direction:column}
  .action-btn{justify-content:center}
}
  </style>
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ============================================================
     SIDEBAR
============================================================ -->
<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <img src="../logo.png" alt="HostelConnect">
    <span>HostelConnect</span>
  </div>

  <nav class="sb-nav">
    <div class="sb-section">Main</div>
    <a href="student_dashboard.php" class="sb-link active">
      <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="../hostel.php" class="sb-link">
      <i class="fas fa-building"></i> Browse Hostels
    </a>
    <a href="../roommates.php" class="sb-link">
      <i class="fas fa-users"></i> Find Roommates
    </a>

    <div class="sb-section">My Activity</div>
    <a href="request_roommate.php" class="sb-link">
      <i class="fas fa-plus-circle"></i> Post Request
    </a>
    <a href="student_request.php" class="sb-link">
      <i class="fas fa-folder-open"></i> My Requests
    </a>

    <div class="sb-section">Account</div>
    <a href="student_profile.php" class="sb-link">
      <i class="fas fa-user-circle"></i> My Profile
    </a>
    <a href="../logout.php" class="sb-link danger">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </nav>

  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-avatar"><?= initials($name) ?></div>
      <div class="sb-user-info">
        <div class="sb-name"><?= htmlspecialchars($name) ?></div>
        <div class="sb-role"><?= ucfirst($role) ?></div>
      </div>
    </div>
  </div>
</aside>

<!-- ============================================================
     MAIN
============================================================ -->
<div class="main">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" id="menuToggle" onclick="openSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <div class="topbar-title">Student Dashboard</div>
    </div>
    <div class="topbar-right">
      <span class="topbar-welcome">Hi, <strong><?= htmlspecialchars($name) ?></strong></span>
      <span class="topbar-badge"><i class="fas fa-graduation-cap"></i> <?= ucfirst($role) ?></span>
    </div>
  </div>

  <!-- Content -->
  <div class="content">

    <?php if ($success): ?>
      <div class="alert success"><i class="fas fa-check-circle"></i><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert error"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Page intro -->
    <div class="page-intro">
      <h2>Welcome back, <?= htmlspecialchars(explode(' ', $name)[0]) ?> 👋</h2>
      <p>Here's what's happening with your roommate requests today</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
        <div class="stat-info">
          <div class="stat-value"><?= $totalRequests ?></div>
          <div class="stat-label">Total Requests</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
          <div class="stat-value"><?= $approvedRequests ?></div>
          <div class="stat-label">Approved</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
          <div class="stat-value"><?= $pendingRequests ?></div>
          <div class="stat-label">Pending Review</div>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="actions-row">
      <a href="../roommates.php" class="action-btn primary">
        <i class="fas fa-search"></i> Find a Roommate
      </a>
      <a href="request_roommate.php" class="action-btn accent">
        <i class="fas fa-plus-circle"></i> Post a Request
      </a>
      <a href="student_request.php" class="action-btn ghost">
        <i class="fas fa-folder-open"></i> My Requests
      </a>
      <a href="../hostel.php" class="action-btn ghost">
        <i class="fas fa-building"></i> Browse Hostels
      </a>
    </div>

    <!-- My Requests -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-user-friends"></i> My Roommate Requests</h3>
        <a href="student_request.php" class="section-link">View all <i class="fas fa-arrow-right"></i></a>
      </div>

      <?php if ($mine && $mine->num_rows > 0): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Campus</th>
                <th>Area</th>
                <th>Budget</th>
                <th>Room Type</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($m = $mine->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($m['campus']) ?></td>
                  <td><?= htmlspecialchars($m['area']) ?></td>
                  <td style="color:var(--accent);font-weight:600">
                    ₦<?= number_format($m['budget_min']) ?> – ₦<?= number_format($m['budget_max']) ?>
                  </td>
                  <td><?= htmlspecialchars($m['room_type']) ?></td>
                  <td>
                    <?php
                      $s = strtolower($m['status']);
                      $icon = $s === 'approved' ? 'check' : ($s === 'pending' ? 'clock' : 'times');
                    ?>
                    <span class="badge <?= $s ?>">
                      <i class="fas fa-<?= $icon ?>"></i>
                      <?= ucfirst($s) ?>
                    </span>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>You haven't posted any roommate requests yet.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Recent Requests from Others -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-bullhorn"></i> Recent Roommate Requests</h3>
        <a href="../roommates.php" class="section-link">Browse all <i class="fas fa-arrow-right"></i></a>
      </div>

      <?php if ($recent && $recent->num_rows > 0): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Campus</th>
                <th>Area</th>
                <th>Budget</th>
                <th>Gender Pref</th>
                <th>Contact</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($r = $recent->fetch_assoc()): ?>
                <tr>
                  <td style="font-weight:600;color:var(--dark)"><?= htmlspecialchars($r['requester_name'] ?? 'Unknown') ?></td>
                  <td><?= htmlspecialchars($r['campus']) ?></td>
                  <td><?= htmlspecialchars($r['area']) ?></td>
                  <td style="color:var(--accent);font-weight:600">
                    ₦<?= number_format($r['budget_min']) ?> – ₦<?= number_format($r['budget_max']) ?>
                  </td>
                  <td><?= htmlspecialchars($r['gender_pref']) ?></td>
                  <td>
                    <?php if (!empty($r['requester_contact'])): ?>
                      <a href="tel:<?= htmlspecialchars($r['requester_contact']) ?>"
                         style="color:var(--brand);font-weight:600;text-decoration:none">
                        <?= htmlspecialchars($r['requester_contact']) ?>
                      </a>
                    <?php else: ?>
                      <span style="color:var(--muted)">N/A</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users"></i>
          <p>No roommate requests available yet.</p>
        </div>
      <?php endif; ?>
    </div>

  </div><!-- /content -->

  <div class="dash-footer">
    &copy; <?= date('Y') ?> HostelConnect &mdash; Built for Nigerian students 🇳🇬
  </div>

</div><!-- /main -->

<script>
function openSidebar() {
  document.getElementById('sidebar').classList.add('show');
  document.getElementById('sbOverlay').classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('sbOverlay').classList.remove('show');
}
// Close sidebar on nav link click (mobile)
document.querySelectorAll('.sb-link').forEach(a => {
  a.addEventListener('click', () => {
    if (window.innerWidth <= 900) closeSidebar();
  });
});
</script>
</body>
</html>