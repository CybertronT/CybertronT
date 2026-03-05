<?php
session_start();
require "../config.php";

// Ensure admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['name'] ?? "Admin";

// --- Stats ---
$pendingHostels   = $conn->query("SELECT COUNT(*) AS c FROM hostels WHERE status='pending'")->fetch_assoc()['c'];
$approvedHostels  = $conn->query("SELECT COUNT(*) AS c FROM hostels WHERE status='approved'")->fetch_assoc()['c'];

$pendingRoommates  = $conn->query("SELECT COUNT(*) AS c FROM roommate_requests WHERE status='pending'")->fetch_assoc()['c'];
$approvedRoommates = $conn->query("SELECT COUNT(*) AS c FROM roommate_requests WHERE status='approved'")->fetch_assoc()['c'];

$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];

// --- Recent Pending Hostels ---
$recentHostels = $conn->query("SELECT id, title, city, type, price, created_at FROM hostels WHERE status='pending' ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// --- Recent Pending Roommates ---
$recentRoommates = $conn->query("SELECT id, requester_name, campus, area, budget_min, budget_max, created_at FROM roommate_requests WHERE status='pending' ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard • HostelConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="../logo.png">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;display:flex;min-height:100vh;}
  .sidebar{width:230px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;padding:20px;transition:0.3s;left:0;z-index:1000;}
  .sidebar h2{font-size:1.5rem;margin-bottom:20px;color:#fff;}
  .sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;font-weight:500;}
  .sidebar a:hover{background:#005f8f;}
  .sidebar-toggle{display:none;position:fixed;top:15px;left:15px;font-size:1.5rem;background:#008CBA;color:#fff;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;z-index:1100;}
  .sidebar-toggle:hover{background:#005f8f;}
  .main{margin-left:230px;flex:1;display:flex;flex-direction:column;padding:20px;transition:0.3s;}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
  .topbar h1{color:#008CBA;font-size:1.8rem;}
  .topbar .welcome{color:#333;font-weight:500;}
  .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;}
  .card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
  .card h3{color:#008CBA;margin-bottom:8px;font-size:1.1rem;}
  .card p{font-size:1.6rem;font-weight:600;color:#333;}
  .card.accent{border-left:4px solid #e67e22;}
  h2{margin:25px 0 15px;color:#008CBA;}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);margin-bottom:25px;}
  th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;}
  .btn:hover{background:#005f8f;}
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  @media(max-width:768px){
    .sidebar{left:-250px;width:220px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
  }
</style>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>

<div class="sidebar" id="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="hostels_pending.php">Pending Hostels</a>
  <a href="roommates_pending.php">Pending Roommates</a>
  <a href="listings.php">All Listings</a>
  <a href="all_roommates.php">All Roommates</a>
  <a href="users.php">Manage Users</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Dashboard</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <div class="cards">
    <div class="card accent">
      <h3>Pending Hostels</h3>
      <p><?= $pendingHostels ?></p>
    </div>
    <div class="card">
      <h3>Approved Hostels</h3>
      <p><?= $approvedHostels ?></p>
    </div>
    <div class="card accent">
      <h3>Pending Roommates</h3>
      <p><?= $pendingRoommates ?></p>
    </div>
    <div class="card">
      <h3>Approved Roommates</h3>
      <p><?= $approvedRoommates ?></p>
    </div>
    <div class="card">
      <h3>Users</h3>
      <p><?= $totalUsers ?></p>
    </div>
  </div>

  <!-- Recent Pending Hostels -->
  <h2>Recent Pending Hostels</h2>
  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>City</th>
        <th>Type</th>
        <th>Price</th>
        <th>Submitted</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($recentHostels): ?>
        <?php foreach ($recentHostels as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['title']) ?></td>
            <td><?= htmlspecialchars($h['city']) ?></td>
            <td><?= htmlspecialchars($h['type']) ?></td>
            <td>₦<?= number_format($h['price']) ?></td>
            <td><?= htmlspecialchars($h['created_at']) ?></td>
            <td><a href="hostels_pending.php?id=<?= $h['id'] ?>" class="btn">Review</a></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6">No pending hostels.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Recent Pending Roommates -->
  <h2>Recent Pending Roommate Requests</h2>
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
      <?php if ($recentRoommates): ?>
        <?php foreach ($recentRoommates as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['requester_name']) ?></td>
            <td><?= htmlspecialchars($r['campus']) ?></td>
            <td><?= htmlspecialchars($r['area']) ?></td>
            <td>₦<?= number_format($r['budget_min']) ?> - ₦<?= number_format($r['budget_max']) ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td><a href="roommates_pending.php?id=<?= $r['id'] ?>" class="btn">Review</a></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6">No pending roommate requests.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

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
