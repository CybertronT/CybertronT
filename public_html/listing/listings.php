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

$user_id   = intval($_SESSION['user_id']);
$user_name = $_SESSION['name'] ?? "User";
$role      = $_SESSION['role'] ?? "guest";

// Handle availability toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $listing_id   = intval($_POST['id']);
    $availability = isset($_POST['availability']) ? 'available' : 'unavailable';

    $stmt = $conn->prepare("UPDATE hostels SET availability = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $availability, $listing_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Refresh page after update
    header("Location: listings.php");
    exit;
}

// Fetch listings only for this user + role
$stmt = $conn->prepare("SELECT id, title, city, area, price, period, status, availability, created_at 
                        FROM hostels 
                        WHERE user_id = ? AND role = ? 
                        ORDER BY id DESC");
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();
$listings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>HostelConnect • My Listings</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  html, body{height:100%;}
  body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;display:flex;min-height:100vh;}
  .sidebar{width:220px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;padding:20px;transition:0.3s;left:0;z-index:1000;}
  .sidebar h2{font-size:1.4rem;margin-bottom:20px;color:#fff;}
  .sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
  .sidebar a:hover{background:#005f8f;}
  .sidebar-toggle{display:none;position:fixed;top:15px;left:15px;font-size:1.5rem;background:#008CBA;color:#fff;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;z-index:1100;}
  .sidebar-toggle:hover{background:#005f8f;}
  .main{margin-left:220px;flex:1;display:flex;flex-direction:column;padding:20px;transition:0.3s;}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
  .topbar h1{color:#008CBA;font-size:1.8rem;}
  .topbar .welcome{color:#333;font-weight:500;}
  .main-content{flex:1;overflow-x:auto;} /* scroll only inside content when needed */

  /* Table styles */
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  thead th{padding:12px 15px;text-align:left;border-bottom:1px solid rgba(0,0,0,0.05);background:#008CBA;color:#fff;position:relative;}
  tbody td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;vertical-align:middle;}
  tr:hover{background:#f1f9ff;}
  .action-buttons {display: flex;gap: 8px;align-items:center;}
  .status{padding:4px 8px;border-radius:5px;font-size:0.8rem;font-weight:600;text-transform:capitalize;display:inline-block;}
  .status.pending{background:#fff3cd;color:#856404;}
  .status.approved{background:#d4edda;color:#155724;}
  .status.rejected{background:#f8d7da;color:#721c24;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;display:inline-block;}
  .btn:hover{background:#005f8f;}
  .btn.edit { background:#28a745; }
  .btn.edit:hover { background:#1e7e34; }
  .empty{padding:20px;text-align:center;color:#777;background:#fff;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}

  /* Toggle Switch */
  .switch { position: relative; display: inline-block; width: 50px; height: 24px; }
  .switch input { opacity: 0; width: 0; height: 0; }
  .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
  .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
  input:checked + .slider { background-color: #28a745; }
  input:checked + .slider:before { transform: translateX(26px); }

  /* column classes so we can hide on mobile */
  .col-area { /* area column */ }
  .col-period { /* period column */ }
  .col-posted { /* posted on column */ }

  /* Mobile tweaks: hide Area, Period, Posted On */
  @media (max-width: 768px) {
    .sidebar{position:fixed;left:-250px;top:0;bottom:0;width:220px;flex-direction:column;overflow-y:auto;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}

    /* hide less-important columns */
    thead th.col-area,
    td.col-area,
    thead th.col-period,
    td.col-period,
    thead th.col-posted,
    td.col-posted {
      display: none;
    }

    /* tighten table for mobile */
    table{font-size:0.9rem;}
    thead th, tbody td{padding:8px;}
    .action-buttons {gap:6px;}
    .btn{padding:6px 8px;font-size:0.82rem;}
    .status{font-size:0.75rem;padding:3px 6px;}
  }

  /* small accessibility + visual improvements */
  .actions { white-space:nowrap; } /* prevents action buttons from wrapping poorly */
</style>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">&#9776;</button>

<div class="sidebar" id="sidebar">
  <h2>HostelConnect</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="add_listing.php">Add Listing</a>
  <a href="listings.php">My Listings</a>
  <a href="profile.php">Profile</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>My Listings</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($role) ?>)</div>
  </div>

  <div class="main-content">
    <?php if (!empty($listings)): ?>
      <table role="table" aria-label="My listings table">
        <thead role="rowgroup">
          <tr role="row">
            <th role="columnheader">Title</th>
            <th role="columnheader">City</th>
            <th role="columnheader" class="col-area">Area</th>
            <th role="columnheader">Price</th>
            <th role="columnheader" class="col-period">Period</th>
            <th role="columnheader">Status</th>
            <th role="columnheader">Availability</th>
            <th role="columnheader" class="col-posted">Posted On</th>
            <th role="columnheader">Action</th>
          </tr>
        </thead>
        <tbody role="rowgroup">
          <?php foreach ($listings as $listing): ?>
            <tr role="row">
              <td role="cell"><?= htmlspecialchars($listing['title']) ?></td>
              <td role="cell"><?= htmlspecialchars($listing['city']) ?></td>
              <td role="cell" class="col-area"><?= htmlspecialchars($listing['area']) ?></td>
              <td role="cell">₦<?= number_format($listing['price']) ?></td>
              <td role="cell" class="col-period"><?= htmlspecialchars($listing['period']) ?></td>
              <td role="cell"><span class="status <?= strtolower($listing['status']) ?>"><?= htmlspecialchars($listing['status']) ?></span></td>
              <td role="cell">
                <form method="post" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $listing['id'] ?>">
                  <label class="switch" aria-label="Toggle availability">
                    <input type="checkbox" name="availability" value="available"
                           onchange="this.form.submit()"
                           <?= ($listing['availability'] === 'available') ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                  </label>
                </form>
              </td>
              <td role="cell" class="col-posted"><?= htmlspecialchars(date("M d, Y", strtotime($listing['created_at'] ?? 'now'))) ?></td>
              <td role="cell" class="actions">
                <div class="action-buttons">
                  <a class="btn" href="listing_details.php?id=<?= $listing['id'] ?>">View</a>
                  <a class="btn edit" href="edit_listing.php?id=<?= $listing['id'] ?>">Edit</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty">You haven’t posted any listings yet.</div>
    <?php endif; ?>
  </div>

  <footer>
    &copy; 2025 HostelConnect
  </footer>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
});

/* Optional: auto-hide sidebar after clicking a link on small screens to improve UX */
document.querySelectorAll('.sidebar a').forEach(a => {
  a.addEventListener('click', () => {
    if (window.innerWidth <= 768) sidebar.classList.remove('active');
  });
});
</script>

</body>
</html>
