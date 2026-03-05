<?php
session_start();
require "config.php";
require_once "csrf.php";

$errors  = [];
$success = '';
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? null;

$recaptcha_site   = "6Ld9y9IrAAAAALxKCDOIl-F7oQdWf5ILjVFRDb4Z";
$recaptcha_secret = "6Ld9y9IrAAAAAMf9Hkf8IDzN4imn-sp2b0G2IAOI";

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

function safe_redirect($url, $default = 'index.php') {
    if ($url && preg_match('#^/[a-zA-Z0-9_\-\/]+(\?[a-zA-Z0-9_=%&-]*)?$#', $url)) {
        return $url;
    }
    return $default;
}

function log_activity($conn, $user_id, $action, $description = '') {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action, $description, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['registered'])) {
    $success = "Registration successful! Please check your email to verify before logging in.";
} elseif (isset($_GET['success'])) {
    $success = urldecode($_GET['success']);
}
if (isset($_GET['error'])) {
    $errors[] = urldecode($_GET['error']);
}

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
                SELECT id, full_name, username, email, password, contact, role, email_verified, status
                FROM users WHERE username=? OR email=? LIMIT 1
            ");
            $stmt->bind_param("ss", $user, $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                if ($row['status'] === 'blocked') {
                    $errors[] = "Your account has been blocked. Please contact support.";
                    log_activity($conn, $row['id'], "login_blocked", "Blocked user tried to login.");
                } elseif (!$row['email_verified']) {
                    $errors[] = "Your email is not verified. <a href='resend.php?email=" . urlencode($row['email']) . "'>Resend verification email</a>";
                } elseif (password_verify($password, $row['password'])) {
                    $_SESSION['user_id']  = $row['id'];
                    $_SESSION['role']     = $row['role'];
                    $_SESSION['name']     = $row['full_name'];
                    $_SESSION['contact']  = $row['contact'];
                    $_SESSION['login_attempts'] = 0;

                    log_activity($conn, $row['id'], "login_success", "User logged in successfully.");

                    if (!empty($redirect)) {
                        header("Location: " . safe_redirect($redirect));
                        exit;
                    }
                    switch ($row['role']) {
                        case 'student':  header("Location: student/student_dashboard.php"); break;
                        case 'support':  header("Location: support/dashboard.php"); break;
                        case 'admin':    header("Location: admin/dashboard.php"); break;
                        default:         header("Location: listing/dashboard.php"); break;
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
                log_activity($conn, null, "login_failed", "Tried: $user");
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
  <title>Login — HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#0b68b1;
  --brand-dark:#084e87;
  --brand-light:#dff0ff;
  --accent:#cf6c17;
  --accent-dark:#a85510;
  --dark:#0d1117;
  --body:#374151;
  --muted:#8b95a1;
  --border:#e4e9ef;
  --bg:#f4f7fc;
  --white:#ffffff;
  --green:#16a34a;
  --green-bg:#dcfce7;
  --red:#dc2626;
  --red-bg:#fee2e2;
  --r:16px;
  --r-sm:10px;
}
html,body{height:100%;-webkit-font-smoothing:antialiased}
body{
  font-family:'DM Sans',sans-serif;
  background:var(--bg);
  color:var(--body);
  display:flex;
  min-height:100vh;
}

/* ============================================================
   SPLIT LAYOUT
============================================================ */
.split{display:flex;width:100%;min-height:100vh}

/* LEFT PANEL — decorative brand side */
.panel-left{
  flex:1;
  background:linear-gradient(155deg,#0b68b1 0%,#07438e 55%,#03295a 100%);
  display:flex;flex-direction:column;
  justify-content:space-between;
  padding:48px 52px;
  position:relative;
  overflow:hidden;
}
.panel-left::before{
  content:'';position:absolute;inset:0;
  background:url('bg.jpg') center/cover no-repeat;
  opacity:.07;
}
.pl-orb{position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none}
.pl-orb.o1{width:480px;height:480px;background:radial-gradient(circle,rgba(207,108,23,.22) 0%,transparent 70%);top:-180px;right:-120px}
.pl-orb.o2{width:360px;height:360px;background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%);bottom:-80px;left:-80px}

.pl-inner{position:relative;z-index:1}

/* Logo area */
.pl-logo{display:flex;align-items:center;gap:12px;margin-bottom:64px}
.pl-logo img{width:44px;height:44px;border-radius:10px}
.pl-logo span{font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:800;color:#fff}

/* Headline */
.pl-headline{
  font-family:'Playfair Display',serif;
  font-size:clamp(2rem,3.5vw,2.8rem);
  font-weight:800;color:#fff;
  line-height:1.18;letter-spacing:-.6px;
  margin-bottom:20px;
}
.pl-headline em{font-style:italic;color:#fbbf24}
.pl-sub{color:rgba(255,255,255,.55);font-size:.97rem;font-weight:300;line-height:1.7;max-width:360px;margin-bottom:44px}

/* Features list */
.pl-features{display:flex;flex-direction:column;gap:16px}
.pl-feat{display:flex;align-items:center;gap:14px}
.pl-feat-icon{
  width:40px;height:40px;min-width:40px;border-radius:11px;
  background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
  display:flex;align-items:center;justify-content:center;
  color:rgba(255,255,255,.85);font-size:.9rem;
  backdrop-filter:blur(8px);
}
.pl-feat-text{color:rgba(255,255,255,.7);font-size:.88rem;font-weight:500}
.pl-feat-text strong{color:#fff;font-weight:700}

/* Bottom credit */
.pl-bottom{
  position:relative;z-index:1;
  color:rgba(255,255,255,.25);font-size:.78rem;
}

/* ============================================================
   RIGHT PANEL — the form
============================================================ */
.panel-right{
  width:480px;min-width:480px;
  background:var(--white);
  display:flex;align-items:center;justify-content:center;
  padding:48px 52px;
  box-shadow:-8px 0 48px rgba(11,104,177,.08);
}
.form-box{width:100%;max-width:360px}

/* Back link */
.back-link{
  display:inline-flex;align-items:center;gap:7px;
  color:var(--muted);font-size:.83rem;font-weight:600;
  text-decoration:none;margin-bottom:36px;
  transition:color .2s;
}
.back-link:hover{color:var(--brand)}
.back-link i{font-size:.78rem}

/* Heading */
.form-heading{margin-bottom:30px}
.form-heading h2{
  font-family:'Playfair Display',serif;
  font-size:1.75rem;font-weight:800;
  color:var(--dark);letter-spacing:-.4px;margin-bottom:5px;
}
.form-heading p{color:var(--muted);font-size:.88rem}

/* Alerts */
.alert{
  border-radius:var(--r-sm);padding:12px 14px;
  font-size:.85rem;line-height:1.65;margin-bottom:22px;
  display:flex;align-items:flex-start;gap:9px;
}
.alert i{font-size:.9rem;margin-top:2px;flex-shrink:0}
.alert.error{background:var(--red-bg);color:var(--red);border:1px solid rgba(220,38,38,.18)}
.alert.success{background:var(--green-bg);color:var(--green);border:1px solid rgba(22,163,74,.18)}
.alert a{color:var(--brand);font-weight:600;text-decoration:none}
.alert a:hover{text-decoration:underline}

/* Attempts badge */
.attempts-note{
  display:flex;align-items:center;gap:6px;
  background:var(--red-bg);color:var(--red);
  border:1px solid rgba(220,38,38,.18);
  border-radius:var(--r-sm);padding:10px 14px;
  font-size:.82rem;font-weight:600;margin-bottom:18px;
}

/* Form fields */
.form-group{margin-bottom:18px}
.form-label{
  display:block;font-size:.75rem;font-weight:700;
  text-transform:uppercase;letter-spacing:1px;
  color:var(--muted);margin-bottom:7px;
}
.input-wrap{position:relative}
.input-wrap .fi{
  position:absolute;left:14px;top:50%;transform:translateY(-50%);
  color:var(--muted);font-size:.88rem;pointer-events:none;
  transition:color .2s;
}
.form-input{
  width:100%;padding:13px 14px 13px 40px;
  border:1.5px solid var(--border);border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;font-size:.93rem;color:var(--dark);
  background:var(--bg);outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
}
.form-input:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(11,104,177,.1);
  background:var(--white);
}
.form-input:focus + .fi,
.input-wrap:focus-within .fi{color:var(--brand)}
.form-input::placeholder{color:#bcc4ce}

/* Password toggle */
.pw-toggle{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;
  color:var(--muted);font-size:.88rem;padding:4px;
  transition:color .2s;
}
.pw-toggle:hover{color:var(--brand)}

/* reCAPTCHA */
.recaptcha-wrap{margin-bottom:20px;display:flex;justify-content:center}

/* Submit */
.submit-btn{
  width:100%;padding:14px;border:none;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;font-family:'DM Sans',sans-serif;
  font-weight:700;font-size:.97rem;cursor:pointer;
  box-shadow:0 4px 16px rgba(11,104,177,.25);
  transition:transform .2s,box-shadow .2s;
  display:flex;align-items:center;justify-content:center;gap:8px;
  margin-bottom:22px;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(11,104,177,.38)}

/* Footer links */
.form-footer{display:flex;flex-direction:column;gap:12px}
.form-footer a{
  color:var(--brand);font-size:.87rem;font-weight:600;
  text-decoration:none;display:inline-flex;align-items:center;gap:5px;
  transition:color .2s;
}
.form-footer a:hover{color:var(--brand-dark)}
.form-footer .divider{height:1px;background:var(--border);margin:4px 0}
.form-footer p{color:var(--muted);font-size:.87rem}
.form-footer p a{font-weight:700}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:860px){
  .panel-left{display:none}
  .panel-right{
    width:100%;min-width:0;
    padding:40px 24px;
    background:var(--bg);
    box-shadow:none;
    /* show a subtle top strip of brand color */
  }
  .panel-right::before{
    content:'';display:block;height:5px;
    background:linear-gradient(90deg,var(--brand),var(--accent));
    position:fixed;top:0;left:0;right:0;z-index:10;
  }
  .form-box{max-width:400px;margin:0 auto}
  .form-box .back-link{margin-bottom:28px}
}
  </style>
</head>
<body>

<div class="split">

  <!-- ============================================================
       LEFT PANEL
  ============================================================ -->
  <div class="panel-left">
    <div class="pl-orb o1"></div>
    <div class="pl-orb o2"></div>

    <div class="pl-inner">
      <!-- Logo -->
      <div class="pl-logo">
        <img src="logo.png" alt="HostelConnect">
        <span>HostelConnect</span>
      </div>

      <!-- Headline -->
      <div class="pl-headline">
        Find your <em>perfect</em><br>student home
      </div>
      <p class="pl-sub">
        Thousands of verified hostels across Nigeria — filtered by campus, budget, and lifestyle. Login to connect with landlords and roommates instantly.
      </p>

      <!-- Features -->
      <div class="pl-features">
        <div class="pl-feat">
          <div class="pl-feat-icon"><i class="fas fa-shield-alt"></i></div>
          <div class="pl-feat-text"><strong>Verified listings</strong> — every hostel is checked before going live</div>
        </div>
        <div class="pl-feat">
          <div class="pl-feat-icon"><i class="fas fa-user-friends"></i></div>
          <div class="pl-feat-text"><strong>Roommate finder</strong> — match with compatible students near you</div>
        </div>
        <div class="pl-feat">
          <div class="pl-feat-icon"><i class="fas fa-comments"></i></div>
          <div class="pl-feat-text"><strong>Direct contact</strong> — no middlemen, talk straight to the landlord</div>
        </div>
      </div>
    </div>

    <div class="pl-bottom">
      &copy; <?= date('Y') ?> HostelConnect &mdash; Built for Nigerian students
    </div>
  </div>

  <!-- ============================================================
       RIGHT PANEL — FORM
  ============================================================ -->
  <div class="panel-right">
    <div class="form-box">

      <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to home
      </a>

      <div class="form-heading">
        <h2>Welcome back 👋</h2>
        <p>Enter your credentials to access your account</p>
      </div>

      <!-- Success -->
      <?php if (!empty($success)): ?>
        <div class="alert success">
          <i class="fas fa-check-circle"></i>
          <span><?= htmlspecialchars($success) ?></span>
        </div>
      <?php endif; ?>

      <!-- Errors -->
      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <i class="fas fa-exclamation-circle"></i>
          <span><?= implode('<br>', $errors) ?></span>
        </div>
      <?php endif; ?>

      <!-- Attempts warning -->
      <?php if ($_SESSION['login_attempts'] >= 2 && $_SESSION['login_attempts'] < 3): ?>
        <div class="attempts-note">
          <i class="fas fa-exclamation-triangle"></i>
          <?= 3 - $_SESSION['login_attempts'] ?> attempt<?= (3 - $_SESSION['login_attempts']) !== 1 ? 's' : '' ?> left before CAPTCHA is required
        </div>
      <?php endif; ?>

      <!-- Form — all original inputs & hidden fields preserved -->
      <form action="login.php" method="post" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="redirect"   value="<?= htmlspecialchars($redirect ?? '') ?>">

        <div class="form-group">
          <label class="form-label" for="user">Username or Email</label>
          <div class="input-wrap">
            <i class="fas fa-user fi"></i>
            <input
              class="form-input"
              type="text" id="user" name="user"
              placeholder="your@email.com or username"
              value="<?= htmlspecialchars($_POST['user'] ?? '') ?>"
              required autocomplete="username">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock fi"></i>
            <input
              class="form-input"
              type="password" id="password" name="password"
              placeholder="Enter your password"
              required autocomplete="current-password"
              style="padding-right:44px">
            <button type="button" class="pw-toggle" onclick="togglePw()" title="Show/hide password">
              <i class="fas fa-eye" id="pwIcon"></i>
            </button>
          </div>
        </div>

        <!-- reCAPTCHA — only after 3 failed attempts -->
        <?php if ($_SESSION['login_attempts'] >= 3): ?>
          <div class="recaptcha-wrap">
            <div class="g-recaptcha" data-sitekey="<?= $recaptcha_site ?>"></div>
          </div>
        <?php endif; ?>

        <button type="submit" class="submit-btn">
          <i class="fas fa-sign-in-alt"></i> Login to my account
        </button>
      </form>

      <div class="form-footer">
        <a href="forgot_password.php">
          <i class="fas fa-key"></i> Forgot your password?
        </a>
        <div class="divider"></div>
        <p>Don't have an account? <a href="register.php">Create one free</a></p>
      </div>

    </div>
  </div>

</div><!-- /split -->

<script>
function togglePw() {
  const input = document.getElementById('password');
  const icon  = document.getElementById('pwIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}
</script>
</body>
</html>