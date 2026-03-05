<?php
session_start();
require "../config.php";

// Ensure admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['name'] ?? "Admin";

// --- Approve or Reject Action ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = intval($_POST['id']);
    $action = $_POST['action'];

    // fetch roommate requester name
    $stmt = $conn->prepare("SELECT requester_name FROM roommate_requests WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($reqName);
    $stmt->fetch();
    $stmt->close();
    $reqNameSafe = $reqName ? htmlspecialchars($reqName) : "Roommate request";

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE roommate_requests SET status='approved', rejection_reason=NULL WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = "✅ '$reqNameSafe' approved successfully.";
    } elseif ($action === 'reject') {
        $reason = trim($_POST['reason'] ?? '');
        $stmt = $conn->prepare("UPDATE roommate_requests SET status='rejected', rejection_reason=? WHERE id=?");
        $stmt->bind_param("si", $reason, $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = "❌ '$reqNameSafe' rejected." . ($reason ? " Reason: $reason" : "");
    }

    header("Location: roommates_pending.php");
    exit;
}

// --- Fetch Pending Roommate Requests ---
$pendingRoommates = $conn->query("SELECT id, requester_name, campus, area, budget_min, budget_max, created_at 
    FROM roommate_requests 
    WHERE status='pending' 
    ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Roommates • HostelConnect Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;display:flex;min-height:100vh;}
  .sidebar{width:230px;background:#008CBA;color:#fff;position:fixed;top:0;bottom:0;padding:20px;transition:0.3s;z-index:1000;}
  .sidebar h2{font-size:1.5rem;margin-bottom:20px;}
  .sidebar a{color:#fff;text-decoration:none;display:block;padding:10px;border-radius:6px;margin-bottom:8px;font-weight:500;}
  .sidebar a:hover{background:#005f8f;}
  .sidebar-toggle{display:none;position:absolute;top:20px;left:20px;font-size:1.5rem;background:#008CBA;color:#fff;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;z-index:1101;}
  .main{margin-left:230px;flex:1;padding:20px;display:flex;flex-direction:column;}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:10px;}
  .topbar h1{color:#008CBA;font-size:1.6rem;}
  h2{margin:20px 0;color:#008CBA;}
  table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  th,td{padding:12px 15px;border-bottom:1px solid #eee;text-align:left;font-size:0.95rem;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;cursor:pointer;font-size:0.85rem;}
  .btn:hover{background:#005f8f;}
  form{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
  input[type="text"]{padding:6px;border:1px solid #ccc;border-radius:6px;font-size:0.85rem;}
  .flash{margin-bottom:20px;padding:12px;border-radius:6px;}
  .flash.success{background:#d4edda;color:#155724;}
  .flash.error{background:#f8d7da;color:#721c24;}
  footer{margin-top:auto;text-align:center;color:#777;font-size:0.9rem;padding:15px;}
  /* Responsive tweaks */
  @media(max-width:900px){
    body{flex-direction:column;}
    .main{margin-left:0;}
    .sidebar{left:-250px;width:230px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    table{font-size:0.8rem;}
    th, td{padding:6px 8px;}
    input[type="text"]{width:100%;max-width:150px;font-size:0.75rem;}
    form{flex-direction:column;align-items:flex-start;}
  }
</style>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>

<div class="sidebar" id="sidebar">
  <h2>Admin Panel</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="hostels_pending.php">Pending Hostels</a>
  <a href="roommates_pending.php">Pending Roommates</a>
  <a href="listings.php">All Listings</a>
  <a href="users.php">Manage Users</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Pending Roommate Requests</h1>
    <div>Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <?php if (isset($_SESSION['flash'])): ?>
    <div class="flash <?= strpos($_SESSION['flash'], '✅') !== false ? 'success' : 'error' ?>">
      <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    </div>
  <?php endif; ?>

  <h2>All Pending Requests</h2>
  <div style="overflow-x:auto;">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Campus</th>
          <th>Area</th>
          <th>Budget</th>
          <th>Submitted</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($pendingRoommates): ?>
          <?php foreach ($pendingRoommates as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['requester_name']) ?></td>
              <td><?= htmlspecialchars($r['campus']) ?></td>
              <td><?= htmlspecialchars($r['area']) ?></td>
              <td>₦<?= number_format($r['budget_min']) ?> - ₦<?= number_format($r['budget_max']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <button type="submit" name="action" value="approve" class="btn">Approve</button>
                  <input type="text" name="reason" placeholder="Reason (optional)">
                  <button type="submit" name="action" value="reject" class="btn" style="background:#e74c3c;">Reject</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No pending roommate requests.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; <?= date("Y") ?> HostelConnect Admin
  </footer>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
});
</script>

</body>
</html>
