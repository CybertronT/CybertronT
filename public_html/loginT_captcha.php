<?php
session_start();
require "config.php";
require_once "csrf.php";

$errors = [];
$success = '';
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? null;

// Google reCAPTCHA keys
$recaptcha_site   = "6Ld9y9IrAAAAALxKCDOIl-F7oQdWf5ILjVFRDb4Z";
$recaptcha_secret = "6Ld9y9IrAAAAAMf9Hkf8IDzN4imn-sp2b0G2IAOI";

// Track login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Safe redirect
function safe_redirect($url, $default = 'index.php') {
    if ($url && preg_match('#^/[a-zA-Z0-9_\-\/]+(\?[a-zA-Z0-9_=%&-]*)?$#', $url)) {
        return $url;
    }
    return $default;
}

// Log activity
function log_activity($conn, $user_id, $action, $description = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action, $description, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}

// Messages
if (isset($_GET['registered'])) {
    $success = "Registration successful! You can now log in.";
} elseif (isset($_GET['success'])) {
    $success = urldecode($_GET['success']);
}
if (isset($_GET['error'])) {
    $errors[] = urldecode($_GET['error']);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $user     = trim($_POST['user'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$user || !$password) {
        $errors[] = "All fields are required.";
    } else {
        // Require captcha after 3 failed attempts
        if ($_SESSION['login_attempts'] >= 3) {
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret="
                . $recaptcha_secret . "&response=" . $recaptcha_response);
            $captcha_success = json_decode($verify, true);

            if (empty($captcha_success['success'])) {
                $errors[] = "Captcha verification failed. Please try again.";
            }
        }

        if (!$errors) {
            $stmt = $conn->prepare("
                SELECT id, full_name, username, email, password, contact, role, status
                FROM users 
                WHERE username=? OR email=? 
                LIMIT 1
            ");
            $stmt->bind_param("ss", $user, $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                if ($row['status'] === 'blocked') {
                    $errors[] = "Your account has been blocked. Please contact support.";
                    log_activity($conn, $row['id'], "login_blocked", "Blocked user tried to login.");
                } elseif (password_verify($password, $row['password'])) {
                    // Success
                    $_SESSION['user_id']  = $row['id'];
                    $_SESSION['role']     = $row['role'];
                    $_SESSION['name']     = $row['full_name'];
                    $_SESSION['contact']  = $row['contact'];
                    $_SESSION['login_attempts'] = 0;

                    log_activity($conn, $row['id'], "login_success", "User logged in successfully.");

                    // Redirect
                    if (!empty($redirect)) {
                        $target = safe_redirect($redirect);
                        header("Location: $target");
                        exit;
                    }

                    switch ($row['role']) {
                        case 'student':
                            header("Location: student/student_dashboard.php");
                            break;
                        case 'support':
                            header("Location: support/dashboard.php");
                            break;
                        case 'admin':
                            header("Location: admin/dashboard.php");
                            break;
                        case 'landlord':
                        case 'agent':
                        default:
                            header("Location: listing/dashboard.php");
                            break;
                    }
                    exit;
                } else {
                    $errors[] = "Incorrect password.";
                    $_SESSION['login_attempts']++;
                    log_activity($conn, $row['id'], "login_failed", "Wrong password entered.");
                }
            } else {
                $errors[] = "User not found.";
                $_SESSION['login_attempts']++;
                log_activity($conn, null, "login_failed", "Invalid username/email: $user");
            }
            $stmt->close();
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
<title>HostelConnect • Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="logo.png">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;line-height:1.5;display:flex;align-items:center;justify-content:center;min-height:100vh;}
.container{width:100%;max-width:400px;padding:30px;background:#fff;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.08);}
h1{text-align:center;margin-bottom:10px;color:#0b79d0;font-size:1.8rem;}
p.lead{text-align:center;margin-bottom:20px;color:#666;font-size:1rem;}
form{display:flex;flex-direction:column;gap:15px;}
label{font-weight:600;margin-bottom:5px;font-size:0.95rem;}
input{width:100%;padding:12px 15px;border-radius:8px;border:1px solid #e5e7eb;font-size:1rem;}
input:focus{outline:none;border-color:#0b79d0;box-shadow:0 0 5px rgba(11,121,208,0.3);}
button{width:100%;padding:12px;border-radius:8px;background:#0b79d0;color:#fff;font-weight:600;border:none;cursor:pointer;text-align:center;font-size:1rem;}
button:hover{background:#095a9d;}
.error{color:red;margin-bottom:10px;font-size:0.9rem;text-align:center;}
.success{color:green;margin-bottom:10px;font-size:0.9rem;text-align:center;}
.footer{text-align:center;margin-top:20px;font-size:.9rem;color:#666;}
@media(max-width:500px){.container{padding:20px;}h1{font-size:1.5rem;}}
</style>
</head>
<body>

<div class="container">
  <h1>HostelConnect</h1>
  <p class="lead">Login to your account</p>

  <?php if(!empty($success)): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if(!empty($errors)): ?>
    <div class="error"><?= implode('<br>', $errors) ?></div>
  <?php endif; ?>

  <form action="login.php" method="post" autocomplete="on">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

    <div class="field">
      <label for="user">Username or Email</label>
      <input type="text" id="user" name="user" placeholder="Enter username or email" required>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter password" required>
    </div>

    <?php if ($_SESSION['login_attempts'] >= 3): ?>
      <div class="g-recaptcha" data-sitekey="<?= $recaptcha_site ?>"></div>
    <?php endif; ?>

    <button type="submit">Login</button>
  </form>

  <p class="lead" style="margin-top:15px;text-align:center;">
    <a href="forgot_password.php" style="color:#0b79d0;">Forgot your password?</a>
  </p>
  <p class="lead" style="margin-top:15px;text-align:center;">
    Don’t have an account? <a href="register.php" style="color:#0b79d0;">Register</a>
  </p>
</div>

</body>
</html>
