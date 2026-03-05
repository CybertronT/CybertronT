<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$allowed_roles = [ 'student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}


$user_id   = intval($_SESSION['user_id']);
$role      = $_SESSION['role'] ?? "guest";
$message   = "";

// --- Handle profile update ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $contact   = trim($_POST['contact']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, contact = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $contact, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile.";
    }
    $stmt->close();
}

// --- Handle password update ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && password_verify($current_password, $row['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            if ($stmt->execute()) {
                $message = "Password updated successfully!";
            } else {
                $message = "Error updating password.";
            }
            $stmt->close();
        } else {
            $message = "Current password is incorrect.";
        }
    }
}

// --- Fetch user info ---
$stmt = $conn->prepare("SELECT full_name, email, contact FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc() ?? ['full_name'=>'', 'email'=>'', 'contact'=>''];
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile • HostelConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  html,body{height:100%;font-family:'Poppins',sans-serif;background:#f5f7fa;}
  .sidebar{width:220px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;padding:20px;transition:0.3s;left:0;z-index:1000;}
  .sidebar h2{font-size:1.4rem;margin-bottom:20px;color:#fff;}
  .sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
  .sidebar a:hover{background:#005f8f;}
  .sidebar-toggle{display:none;position:fixed;top:15px;left:15px;font-size:1.5rem;background:#008CBA;color:#fff;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;z-index:1100;}
  .sidebar-toggle:hover{background:#005f8f;}
  .main{margin-left:0;flex:1;display:flex;justify-content:center;align-items:flex-start;padding:20px;transition:0.3s;min-height:100vh;}
  .profile-card{width:100%;max-width:600px;background:#fff;padding:25px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
  .profile-card h1{color:#008CBA;margin-bottom:15px;text-align:center;}
  .profile-card p{margin-top:12px;margin-bottom:5px;font-weight:500;color:#555;}
  .profile-card input{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:6px;font-size:1rem;}
  .btn{padding:10px 18px;border:none;border-radius:6px;font-weight:600;font-size:1rem;cursor:pointer;}
  .edit-btn{background:#008CBA;color:#fff;margin-top:10px;}
  .edit-btn:hover{background:#005f8f;}
  .save-btn{background:#28a745;color:#fff;margin-top:10px;}
  .save-btn:hover{background:#1e7e34;}
  .password-box{display:none;margin-top:20px;padding:15px;border:1px solid #eee;border-radius:8px;background:#fafafa;}
  .message{margin-bottom:15px;padding:10px;border-radius:6px;font-weight:500;text-align:center;}
  .success{background:#d4edda;color:#155724;}
  .error{background:#f8d7da;color:#721c24;}
  footer{text-align:center;margin-top:30px;font-size:.9rem;color:#666;}
  @media(max-width:768px){
    .sidebar{position:fixed;left:-250px;top:0;bottom:0;width:220px;overflow-y:auto;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{padding:10px;}
  }
</style>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>

<div class="sidebar" id="sidebar">
  <h2>HostelConnect</h2>
  <a href="student_dashboard.php">Dashboard</a>
  
  <a href="request_roommate.php">Post Request</a>
  <a href="student_request.php">My Requests</a>
   <a href="student_profile.php">Profile</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="profile-card">
    <h1>My Profile</h1>

    <?php if (!empty($message)): ?>
      <div class="message <?= strpos($message,'successfully')!==false ? 'success':'error' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="post" id="profileForm">
      <input type="hidden" name="update_profile" value="1">
      <p><strong>Full Name:</strong></p>
      <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" readonly required>
      
      <p><strong>Email:</strong></p>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly required>
      
      <p><strong>Contact:</strong></p>
      <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" readonly required>
      
      <button type="button" class="btn edit-btn" id="editBtn">Edit Profile</button>
      <button type="submit" class="btn save-btn" id="saveBtn" style="display:none;">Save Changes</button>
    </form>

    <hr style="margin:25px 0;">

    <div>
      <button type="button" class="btn edit-btn" id="showPasswordBtn">Change Password</button>
      <form method="post" class="password-box" id="passwordBox">
        <input type="hidden" name="update_password" value="1">
        <p><strong>Current Password:</strong></p>
        <input type="password" name="current_password" placeholder="Enter current password" required>
        
        <p><strong>New Password:</strong></p>
        <input type="password" name="new_password" placeholder="New Password" required>
        
        <p><strong>Confirm New Password:</strong></p>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        
        <button type="submit" class="btn save-btn">Save Password</button>
      </form>
    </div>
  </div>
</div>

<footer>&copy; 2025 HostelConnect</footer>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('active'); });

const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const inputs = document.querySelectorAll('#profileForm input[type=text], #profileForm input[type=email]');

editBtn.addEventListener('click', () => {
    inputs.forEach(i => i.removeAttribute('readonly'));
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
});

const showPasswordBtn = document.getElementById('showPasswordBtn');
const passwordBox = document.getElementById('passwordBox');
showPasswordBtn.addEventListener('click', () => {
    passwordBox.style.display = (passwordBox.style.display === 'block') ? 'none' : 'block';
});
</script>
</body>
</html>
