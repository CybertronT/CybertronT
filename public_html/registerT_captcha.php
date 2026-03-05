<?php 
session_start();
require "config.php";

$errors = [];

// Google reCAPTCHA keys
$recaptcha_site   = "6Ld9y9IrAAAAALxKCDOIl-F7oQdWf5ILjVFRDb4Z";
$recaptcha_secret = "6Ld9y9IrAAAAAMf9Hkf8IDzN4imn-sp2b0G2IAOI";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmPassword'] ?? '';
    $role     = $_POST['role'] ?? '';
    $contact  = trim($_POST['contact'] ?? '');

    // reCAPTCHA validation
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" 
        . $recaptcha_secret . "&response=" . $recaptcha_response);
    $captcha_success = json_decode($verify, true);

    if (empty($captcha_success['success'])) {
        $errors[] = "Captcha verification failed. Please try again.";
    }

    // Validation
    if (!$username || !$name || !$email || !$password || !$confirm || !$role || !$contact) {
        $errors[] = "All fields are required.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check username/email uniqueness
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username or email already taken.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hash  = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, role, contact, email_verified) VALUES (?,?,?,?,?,?,1)");
        $stmt->bind_param("ssssss", $username, $name, $email, $hash, $role, $contact);

        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • Register</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="logo.png">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<style>
  * {box-sizing:border-box;margin:0;padding:0;}
  body {font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;line-height:1.5;}
  .container {max-width:480px;margin:50px auto;padding:30px;background:#fff;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.08);}
  h1{text-align:center;margin-bottom:10px;color:#0b79d0;font-size:1.8rem;}
  p.lead{text-align:center;margin-bottom:20px;color:#666;font-size:1rem;}
  form{display:flex;flex-direction:column;gap:15px;}
  label{font-weight:600;margin-bottom:5px;font-size:0.95rem;}
  input,select{width:100%;padding:12px 15px;border-radius:8px;border:1px solid #e5e7eb;font-size:1rem;}
  input:focus,select:focus{outline:none;border-color:#0b79d0;box-shadow:0 0 5px rgba(11,121,208,0.3);}
  .hidden{display:none;}
  button,.btn-link{width:100%;padding:12px;border-radius:8px;background:#0b79d0;color:#fff;font-weight:600;border:none;cursor:pointer;text-align:center;text-decoration:none;display:inline-block;font-size:1rem;}
  button:hover,.btn-link:hover{background:#095a9d;}
  .error{color:red;margin-bottom:10px;font-size:0.9rem;}
  .row{display:flex;gap:10px;flex-wrap:wrap;}
  .row .field{flex:1;min-width:120px;}
  .footer{text-align:center;margin-top:25px;font-size:.9rem;color:#666;}
  @media (max-width:600px){
    .container{margin:20px 15px;padding:20px;}
    h1{font-size:1.5rem;}
    .row{flex-direction:column;}
    .row .field{min-width:100%;}
  }
</style>
</head>
<body>

<div class="container">
  <h1>HostelConnect</h1>
  <p class="lead">Register your account</p>

  <?php if(!empty($errors)): ?>
    <div class="error"><?php echo implode('<br>',$errors); ?></div>
  <?php endif; ?>

  <form id="registerForm" action="register.php" method="post" autocomplete="on">
    <div class="field">
      <label for="role">Select your role</label>
      <select id="role" name="role" required>
        <option value="">Choose role…</option>
        <option value="landlord">Landlord</option>
        <option value="agent">Agent</option>
        <option value="student">Student</option>
      </select>
    </div>

    <div class="field">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" placeholder="e.g., John Doe" required>
    </div>

    <div class="field">
      <label for="contact">Contact Number (+234)</label>
      <input type="tel" id="contact" name="contact" placeholder="e.g., 801234567" maxlength="11" pattern="\d{10}" required>
    </div>

    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Choose a username" required>
    </div>

    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required>
    </div>

    <div class="row">
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="field">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
      </div>
    </div>

    <!-- Google reCAPTCHA -->
    <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site; ?>"></div>

    <button type="submit">Register</button>
  </form>

  <p class="lead" style="margin-top:20px;text-align:center;">
    Already have an account? <a href="login.php" style="color:#0b79d0;">Login</a>
  </p>
</div>

<script>
(() => {
  const $ = id => document.getElementById(id);
})();
</script>
</body>
</html>
