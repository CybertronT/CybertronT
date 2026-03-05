<?php
session_start();
require "../config.php";

// Ensure admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['name'] ?? "Admin";

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM hostels WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: listings.php?msg=deleted");
    exit;
}

// Fetch filters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

// Build query
$sql = "SELECT id, title, city, type, price, status, created_at FROM hostels WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND title LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}
if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$listings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Listings • Admin</title>
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
  h2{margin:20px 0 15px;color:#008CBA;}
  .filters{margin-bottom:15px;display:flex;gap:12px;flex-wrap:wrap;}
  .filters input,.filters select{padding:8px;border:1px solid #ccc;border-radius:6px;font-size:0.9rem;}
  .table-responsive{overflow-x:auto;}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  th,td{padding:12px 15px;border-bottom:1px solid #eee;text-align:left;white-space:normal;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;text-decoration:none;cursor:pointer;display:inline-block;text-align:center;}
  .btn:hover{background:#005f8f;}
  .btn-danger{background:#e74c3c;}
  .btn-danger:hover{background:#c0392b;}
  td .actions{display:flex;gap:8px;flex-wrap:nowrap;}
  @media(max-width:600px){
    td .actions{flex-direction:column;gap:6px;}
  }
  @media(max-width:992px){
    th,td{padding:8px;font-size:0.9rem;white-space:nowrap;}
  }
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  @media(max-width:768px){
    .sidebar{left:-250px;width:220px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
    .filters{flex-direction:column;}
    .filters input,.filters select{width:100%;}
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
  <a href="users.php">Manage Users</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>All Listings</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <div class="filters">
    <input type="text" id="searchInput" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
    <select id="statusFilter">
      <option value="">All Status</option>
      <option value="approved" <?= $status==='approved'?'selected':'' ?>>Approved</option>
      <option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option>
      <option value="rejected" <?= $status==='rejected'?'selected':'' ?>>Rejected</option>
    </select>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>City</th>
          <th>Type</th>
          <th>Price</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($listings): ?>
          <?php foreach ($listings as $l): ?>
            <tr>
              <td><?= htmlspecialchars($l['title']) ?></td>
              <td><?= htmlspecialchars($l['city']) ?></td>
              <td><?= htmlspecialchars($l['type']) ?></td>
              <td>₦<?= number_format($l['price']) ?></td>
              <td><?= ucfirst(htmlspecialchars($l['status'])) ?></td>
              <td><?= htmlspecialchars($l['created_at']) ?></td>
              <td>
                <div class="actions">
                  <a href="edit_listing.php?id=<?= $l['id'] ?>" class="btn">Edit</a>
                  <a href="listings.php?delete=<?= $l['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this hostel?');">Delete</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7">No listings found.</td></tr>
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

// auto filter/search
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

function applyFilters(){
  const search = document.getElementById('searchInput').value;
  const status = document.getElementById('statusFilter').value;
  const params = new URLSearchParams();
  if (search) params.append('search', search);
  if (status) params.append('status', status);
  window.location = 'listings.php' + (params.toString() ? '?' + params.toString() : '');
}
</script>

</body>
</html>
