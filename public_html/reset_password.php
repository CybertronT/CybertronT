<?php
require "config.php";
require_once "csrf.php";

$errors = [];
$success = '';
$showForm = false;

// 1. Validate token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("
        SELECT id, full_name, reset_token_created 
        FROM users 
        WHERE reset_token=? 
        LIMIT 1
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $createdAt = strtotime($row['reset_token_created']);
        $expiresAt = $createdAt + (24 * 60 * 60); // 24 hours

        if (time() > $expiresAt) {
            $errors[] = " This reset link has expired. Please request a new one.";
        } else {
            $showForm = true;
            $userId = $row['id'];
            $name = $row['full_name'];
        }
    } else {
        $errors[] = " Invalid reset link.";
    }
    $stmt->close();
}

// 2. Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid CSRF token. Please try again.";
    } else {
        $userId = $_POST['user_id'] ?? null;
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if (!$password || !$confirm) {
            $errors[] = "All fields are required.";
        } elseif ($password !== $confirm) {
            $errors[] = " Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $errors[] = " Password must be at least 6 characters.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_token_created=NULL WHERE id=?");
            $stmt->bind_param("si", $hashed, $userId);
            $stmt->execute();
            $stmt->close();

            $success = " Your password has been reset successfully. You can now <a href='login.php' style='color:#0b79d0;'>login</a>.";
            $showForm = false;
        }
    }
}

$csrf_token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • Reset Password</title>
<style>
body{font-family:'Poppins',sans-serif;background:#f5f7fa;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
.box{max-width:450px;padding:30px;background:#fff;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.08);text-align:center;}
h1{color:#0b79d0;font-size:1.5rem;margin-bottom:15px;}
.message{padding:15px;border-radius:8px;font-size:1rem;margin-bottom:20px;}
.message.success{background:#e6f9ec;color:#1b7a2a;border:1px solid #b2e6c2;}
.message.error{background:#fdeaea;color:#a94442;border:1px solid #f5c6cb;}
form{display:flex;flex-direction:column;gap:15px;margin-top:15px;}
input{padding:12px;border:1px solid #ccc;border-radius:8px;font-size:1rem;}
button{padding:12px;border:none;border-radius:8px;background:#0b79d0;color:#fff;font-weight:600;cursor:pointer;}
button:hover{background:#095a9d;}
</style>
</head>
<body>
  <div class="box">
    <h1>Reset Password</h1>

    <?php if(!empty($errors)): ?>
      <div class="message error"><?= implode("<br>", $errors) ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if($showForm): ?>
      <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm" placeholder="Confirm New Password" required>
        <button type="submit">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
