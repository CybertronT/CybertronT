<?php
session_start();
require "../config.php";

// Ensure admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['name'] ?? "Admin";

// --- Validate ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$id = intval($_GET['id']);

// --- Fetch User ---
$stmt = $conn->prepare("SELECT id, username, full_name, email, contact, role FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// --- Handle Form Submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, email=?, contact=?, role=? WHERE id=?");
    $stmt->bind_param("sssssi", $username, $full_name, $email, $contact, $role, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['alert'] = "User updated successfully.";
    header("Location: users.php");
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User • HostelConnect Admin</title>
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
  form{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.05);max-width:600px;width:100%;}
  label{display:block;margin-bottom:6px;font-weight:600;color:#333;}
  input,select{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;margin-bottom:15px;font-size:1rem;}
  .btn{padding:10px 16px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:1rem;font-weight:500;cursor:pointer;}
  .btn:hover{background:#005f8f;}
  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}
  @media(max-width:600px){
    .sidebar{left:-250px;width:220px;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
    form{padding:15px;}
    input,select{font-size:0.95rem;}
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
    <h1>Edit User</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</div>
  </div>

  <form method="post">
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Full Name</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Contact</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>">

    <label>Role</label>
    <select name="role" required>
      <?php
      $roles = ["student","landlord","agent","admin","support"];
      foreach ($roles as $role) {
          $sel = $user['role']===$role ? "selected" : "";
          echo "<option value='$role' $sel>" . ucfirst($role) . "</option>";
      }
      ?>
    </select>

    <button type="submit" class="btn">Save Changes</button>
  </form>

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
