<?php
session_start();
require "../config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['name'] ?? 'Admin';

// Fetch all roommate requests, pending first
$roommateRes = $conn->query("
    SELECT id, requester_name, campus, area, budget_min, budget_max, status, created_at
    FROM roommate_requests
    ORDER BY FIELD(status,'pending') DESC, created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin • Roommate Requests</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* Reset & Base */
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;display:flex;min-height:100vh;}
a{text-decoration:none;}
button{cursor:pointer;}

/* Sidebar */
.sidebar{
  width:220px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;left:0;padding:20px;transition:0.3s;z-index:1000;
}
.sidebar h2{font-size:1.4rem;margin-bottom:20px;}
.sidebar a{color:#fff;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
.sidebar a:hover{background:#005f8f;}

/* Main */
.main{flex:1;margin-left:220px;padding:20px;transition:0.3s;}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
.topbar h1{color:#008CBA;}
.topbar .welcome{font-weight:500;}
.menu-toggle{display:none;background:#008CBA;color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:1.5rem;}

/* Table */
table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
th, td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;}
th{background:#008CBA;color:#fff;}
tr:hover{background:#f0f8ff;}
.status-pending{color:#e67e22;font-weight:600;}
.status-approved{color:#27ae60;font-weight:600;}
.status-rejected{color:#c0392b;font-weight:600;}
.btn{padding:6px 12px;border:none;border-radius:6px;color:#fff;font-weight:600;margin-right:6px;}
.btn-approve{background:#27ae60;}
.btn-reject{background:#c0392b;}
.btn-pending{background:#e67e22;}
.btn:hover{opacity:0.9;}

/* Responsive */
@media(max-width:768px){
  .sidebar{left:-250px;position:fixed;}
  .sidebar.show{left:0;}
  .main{margin-left:0;}
  .menu-toggle{display:block;}
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <h2>HostelConnect</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="admin_roommate_requests.php">Roommate Requests</a>
  <a href="admin_listings.php">Listings</a>
  <a href="logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <button class="menu-toggle" id="menuToggle">&#9776;</button>
    <h1>Roommate Requests</h1>
    <div class="welcome">Hi, <?= htmlspecialchars($user_name) ?></div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Requester</th>
        <th>Campus / Area</th>
        <th>Budget (₦)</th>
        <th>Status</th>
        <th>Submitted At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $roommateRes->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['requester_name']) ?></td>
        <td><?= htmlspecialchars($row['campus'].' / '.$row['area']) ?></td>
        <td><?= number_format($row['budget_min']) ?> - <?= number_format($row['budget_max']) ?></td>
        <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
        <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
        <td>
          <form style="display:inline" method="post" action="admin_roommate_action.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button class="btn btn-approve">Approve</button>
          </form>
          <form style="display:inline" method="post" action="admin_roommate_action.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="btn btn-reject">Reject</button>
          </form>
          <form style="display:inline" method="post" action="admin_roommate_action.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="action" value="pending">
            <button class="btn btn-pending">Pending</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
const sidebar = document.getElementById('sidebar');
document.getElementById('menuToggle').addEventListener('click',()=>sidebar.classList.toggle('show'));
</script>
</body>
</html>
