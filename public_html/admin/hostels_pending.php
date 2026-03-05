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

    // fetch hostel title
    $stmt = $conn->prepare("SELECT title FROM hostels WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($title);
    $stmt->fetch();
    $stmt->close();
    $titleSafe = $title ? htmlspecialchars($title) : "Hostel";

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE hostels SET status='approved', rejection_reason=NULL WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = "✅ '$titleSafe' approved successfully.";
    } elseif ($action === 'reject') {
        $reason = trim($_POST['reason'] ?? '');
        $stmt = $conn->prepare("UPDATE hostels SET status='rejected', rejection_reason=? WHERE id=?");
        $stmt->bind_param("si", $reason, $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = "❌ '$titleSafe' rejected." . ($reason ? " Reason: $reason" : "");
    }

    header("Location: hostels_pending.php");
    exit;
}


// --- Fetch Pending Hostels ---
$pendingHostels = $conn->query("SELECT id, title, city, type, price, created_at FROM hostels WHERE status='pending' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Hostels • HostelConnect Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
  .alert{background:#dff0d8;color:#3c763d;padding:12px 15px;border-radius:6px;margin-bottom:20px;font-weight:500;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
  .alert.error{background:#f2dede;color:#a94442;}
  h2{margin:20px 0 15px;color:#008CBA;}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;}
  .btn:hover{background:#005f8f;}
  form{display:flex;gap:8px;align-items:center;}
  input[type="text"]{padding:6px;border:1px solid #ccc;border-radius:6px;font-size:0.9rem;}
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  @media(max-width:768px){
    .sidebar{left:-250px;width:220px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
    form{flex-direction:column;align-items:flex-start;}
    input[type="text"]{width:100%;}
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
    <h1>Pending Hostels</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <h2>All Pending Hostels</h2>
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
      <?php if ($pendingHostels): ?>
        <?php foreach ($pendingHostels as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['title']) ?></td>
            <td><?= htmlspecialchars($h['city']) ?></td>
            <td><?= htmlspecialchars($h['type']) ?></td>
            <td>₦<?= number_format($h['price']) ?></td>
            <td><?= htmlspecialchars($h['created_at']) ?></td>
            <td>
              <form method="post" style="margin:0;">
                <input type="hidden" name="id" value="<?= $h['id'] ?>">
                <button type="submit" name="action" value="approve" class="btn">Approve</button>
                <input type="text" name="reason" placeholder="Reason (optional)">
                <button type="submit" name="action" value="reject" class="btn" style="background:#e74c3c;">Reject</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6">No pending hostels found.</td></tr>
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

// Optional: auto-hide alert after 5s
setTimeout(() => {
  const alertBox = document.querySelector('.alert');
  if (alertBox) {
    alertBox.style.transition = "opacity 0.5s";
    alertBox.style.opacity = 0;
    setTimeout(() => alertBox.remove(), 500);
  }
}, 5000);
</script>

</body>
</html>
