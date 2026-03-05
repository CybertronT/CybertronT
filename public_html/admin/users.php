<?php
session_start();
require "../config.php";

// Ensure admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['name'] ?? "Admin";

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['alert'] = "User deleted successfully.";
    header("Location: users.php");
    exit;
}

// --- Handle Block/Unblock ---
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $stmt = $conn->prepare("SELECT status FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $newStatus = ($user['status'] === 'active') ? 'blocked' : 'active';
        $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['alert'] = "User status changed to $newStatus.";
    }
    header("Location: users.php");
    exit;
}

// --- Filters & Search ---
$where = [];
$params = [];
$types = "";

if (!empty($_GET['role'])) {
    $where[] = "role=?";
    $params[] = $_GET['role'];
    $types .= "s";
}

if (!empty($_GET['status'])) {
    $where[] = "status=?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (!empty($_GET['search'])) {
    $where[] = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
    $params[] = "%" . $_GET['search'] . "%";
    $params[] = "%" . $_GET['search'] . "%";
    $params[] = "%" . $_GET['search'] . "%";
    $types .= "sss";
}

$sql = "SELECT id, username, full_name, email, role, contact, created_at, status 
        FROM users";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users • HostelConnect Admin</title>
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
  .filters{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;}
  .filters input,.filters select{padding:8px 10px;border:1px solid #ccc;border-radius:6px;font-size:0.9rem;}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow-x:auto;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  th,td{padding:12px 15px;border-bottom:1px solid #eee;text-align:left;white-space:nowrap;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}
  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;display:inline-block;}
  .btn:hover{background:#005f8f;}
  .btn-danger{background:#e74c3c;}
  .btn-danger:hover{background:#c0392b;}
  td .actions{display:flex;gap:8px;flex-wrap:nowrap;}
  @media(max-width:992px){
    th,td{padding:8px;font-size:0.9rem;}
    .main{margin-left:200px;}
  }
  @media(max-width:600px){
    .sidebar{left:-250px;width:220px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
    td .actions{flex-direction:column;gap:6px;}
    .filters{flex-direction:column;align-items:flex-start;}
    .filters input,.filters select{width:100%;}
    table{font-size:0.85rem;display:block;overflow-x:auto;}
  }
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  .alert{padding:10px 15px;margin-bottom:15px;border-radius:6px;background:#dff0d8;color:#3c763d;font-size:0.9rem;}
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
  <a href="all_roommates.php">All Roommates</a>
  <a href="users.php" class="active">Manage Users</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Manage Users</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <?php if (!empty($_SESSION['alert'])): ?>
    <div class="alert"><?= $_SESSION['alert']; unset($_SESSION['alert']); ?></div>
  <?php endif; ?>

  <form method="get" class="filters">
    <select name="role" onchange="this.form.submit()">
      <option value="">All Roles</option>
      <?php
      $roles = ["student","landlord","agent","admin","support"];
      foreach ($roles as $role) {
          $sel = ($_GET['role']??'')===$role ? "selected" : "";
          echo "<option value='$role' $sel>" . ucfirst($role) . "</option>";
      }
      ?>
    </select>

    <select name="status" onchange="this.form.submit()">
      <option value="">All Status</option>
      <option value="active" <?= ($_GET['status']??'')==='active' ? 'selected' : '' ?>>Active</option>
      <option value="blocked" <?= ($_GET['status']??'')==='blocked' ? 'selected' : '' ?>>Blocked</option>
    </select>

    <input type="text" name="search" placeholder="Search by username, name, email" value="<?= htmlspecialchars($_GET['search']??'') ?>" oninput="this.form.submit()">
  </form>

  <table>
    <thead>
      <tr>
        <th>Username</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Role</th>
        <th>Status</th>
        <th>Joined</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($users): ?>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['full_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['contact'] ?? '-') ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['status'] ?? 'active') ?></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td>
              <div class="actions">
                <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn">Edit</a>
                <a href="users.php?toggle=<?= $u['id'] ?>" 
                   class="btn" 
                   style="background:<?= ($u['status']==='active' ? '#f39c12' : '#27ae60') ?>;"
                   onclick="return confirm('Are you sure you want to <?= $u['status']==='active' ? 'block' : 'unblock' ?> this user?');">
                   <?= $u['status']==='active' ? 'Block' : 'Unblock' ?>
                </a>
                <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8">No users found.</td></tr>
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
