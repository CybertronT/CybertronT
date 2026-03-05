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

// --- Fetch stats ---
$totalListings   = 0;
$activeListings  = 0;
$pendingListings = 0;

// Total listings by this user
$stmt = $conn->prepare("SELECT COUNT(*) FROM hostels WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($totalListings);
$stmt->fetch();
$stmt->close();

// Active listings
$stmt = $conn->prepare("SELECT COUNT(*) FROM hostels WHERE user_id = ? AND status = 'approved'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($activeListings);
$stmt->fetch();
$stmt->close();

// Pending listings
$stmt = $conn->prepare("SELECT COUNT(*) FROM hostels WHERE user_id = ? AND status = 'pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($pendingListings);
$stmt->fetch();
$stmt->close();

// Messages placeholder (if you add later)
$messagesCount = 0;

// --- Recent Listings ---
$stmt = $conn->prepare("SELECT id, title, city, type, price, status FROM hostels WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recentListings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • Dashboard</title>
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
  .main-content{flex:1;}
  .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:25px;}
  .card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
  .card h3{color:#008CBA;margin-bottom:8px;font-size:1.1rem;}
  .card p{font-size:1.5rem;font-weight:600;color:#333;}
  .card.accent{border-left:4px solid #e67e22;}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;}
  .btn:hover{background:#005f8f;}
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  @media(max-width:768px){
    .sidebar{position:fixed;left:-250px;top:0;bottom:0;width:220px;flex-direction:column;overflow-y:auto;}
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
  <h2>HostelConnect</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="add_listing.php">Add Listing</a>
  <a href="listings.php">My Listings</a>
  <a href="profile.php">Profile</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Dashboard</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($role) ?>)</div>
  </div>

  <div class="main-content">
    <div class="cards">
      <div class="card accent">
        <h3>Total Listings</h3>
        <p><?= $totalListings ?></p>
      </div>
      <div class="card">
        <h3>Active Listings</h3>
        <p><?= $activeListings ?></p>
      </div>
      <div class="card accent">
        <h3>Pending Approvals</h3>
        <p><?= $pendingListings ?></p>
      </div>
      <div class="card">
        <h3>Messages</h3>
        <p><?= $messagesCount ?></p>
      </div>
    </div>

    <h2>Recent Listings</h2>
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>City</th>
          <th>Type</th>
          <th>Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recentListings)): ?>
          <?php foreach ($recentListings as $listing): ?>
            <tr>
              <td><?= htmlspecialchars($listing['title']) ?></td>
              <td><?= htmlspecialchars($listing['city']) ?></td>
              <td><?= htmlspecialchars($listing['type']) ?></td>
              <td>₦<?= number_format($listing['price']) ?></td>
              <td><?= ucfirst(htmlspecialchars($listing['status'])) ?></td>
              <td>
                <a class="btn" href="listing_details.php?id=<?= $listing['id'] ?>">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No listings yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
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
</script>

</body>
</html>
