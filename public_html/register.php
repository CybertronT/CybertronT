<?php
session_start();
require "config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$errors = [];
$posted = []; // repopulate on error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirmPassword'] ?? '';
    $role     = $_POST['role'] ?? '';
    $contact  = trim($_POST['contact'] ?? '');

    $posted = compact('username','name','email','role','contact');

    if (!$username || !$name || !$email || !$password || !$confirm || !$role || !$contact) {
        $errors[] = "All fields are required.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username or email is already taken.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hash  = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, role, contact, email_verified, verify_token, token_created_at) VALUES (?,?,?,?,?,?,0,?,NOW())");
        $stmt->bind_param("sssssss", $username, $name, $email, $hash, $role, $contact, $token);

        if ($stmt->execute()) {
            $verifyLink = "https://hostelconnect.com.ng/verify.php?token=$token";
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.go54mail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'verify@hostelconnect.com.ng';
                $mail->Password   = 'H0st3lconnect'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('verify@hostelconnect.com.ng', 'HostelConnect');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = "Verify your HostelConnect account";
                $mail->Body    = "
<h3>Hello $name,</h3>
<p>Thank you for registering on <strong>HostelConnect</strong>.</p>
<p>Please verify your email within 24 hours by clicking the link below:</p>
<p><a href='$verifyLink'>$verifyLink</a></p>
<p>If you did not sign up, please ignore this email.</p>
<p>— The HostelConnect Team</p>";
                $mail->AltBody = "Hello $name,\n\nVerify your account here: $verifyLink\n(Expires in 24 hours)\n\n— The HostelConnect Team";
                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}

function old($key, $posted) { return htmlspecialchars($posted[$key] ?? ''); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="logo.png">
  <style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#0b68b1;--brand-dark:#084e87;--brand-light:#dff0ff;
  --accent:#cf6c17;--accent-dark:#a85510;
  --dark:#0d1117;--body:#374151;--muted:#8b95a1;
  --border:#e4e9ef;--bg:#f4f7fc;--white:#ffffff;
  --green:#16a34a;--green-bg:#dcfce7;
  --red:#dc2626;--red-bg:#fee2e2;
  --r:16px;--r-sm:10px;
}
html,body{height:100%;-webkit-font-smoothing:antialiased}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);display:flex;min-height:100vh}

/* ============================================================
   SPLIT LAYOUT — mirrors login.php exactly
============================================================ */
.split{display:flex;width:100%;min-height:100vh}

/* LEFT PANEL */
.panel-left{
  flex:1;
  background:linear-gradient(155deg,#0b68b1 0%,#07438e 55%,#03295a 100%);
  display:flex;flex-direction:column;justify-content:space-between;
  padding:48px 52px;position:relative;overflow:hidden;
}
.panel-left::before{content:'';position:absolute;inset:0;background:url('bg.jpg') center/cover no-repeat;opacity:.07}
.pl-orb{position:absolute;border-radius:50%;filter:blur(70px);pointer-events:none}
.pl-orb.o1{width:480px;height:480px;background:radial-gradient(circle,rgba(207,108,23,.22) 0%,transparent 70%);top:-180px;right:-120px}
.pl-orb.o2{width:360px;height:360px;background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%);bottom:-80px;left:-80px}
.pl-inner{position:relative;z-index:1}
.pl-logo{display:flex;align-items:center;gap:12px;margin-bottom:56px}
.pl-logo img{width:44px;height:44px;border-radius:10px}
.pl-logo span{font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:800;color:#fff}
.pl-headline{font-family:'Playfair Display',serif;font-size:clamp(1.9rem,3vw,2.6rem);font-weight:800;color:#fff;line-height:1.18;letter-spacing:-.6px;margin-bottom:18px}
.pl-headline em{font-style:italic;color:#fbbf24}
.pl-sub{color:rgba(255,255,255,.55);font-size:.95rem;font-weight:300;line-height:1.7;max-width:360px;margin-bottom:40px}
.pl-steps{display:flex;flex-direction:column;gap:18px}
.pl-step{display:flex;align-items:flex-start;gap:14px}
.pl-step-num{
  width:32px;height:32px;min-width:32px;border-radius:50%;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.8rem;font-weight:800;font-family:'Playfair Display',serif;
}
.pl-step-text{padding-top:4px;color:rgba(255,255,255,.7);font-size:.88rem;line-height:1.5}
.pl-step-text strong{color:#fff;display:block;margin-bottom:2px}
.pl-bottom{position:relative;z-index:1;color:rgba(255,255,255,.25);font-size:.78rem}

/* RIGHT PANEL */
.panel-right{
  width:520px;min-width:520px;
  background:var(--white);
  display:flex;align-items:flex-start;justify-content:center;
  padding:48px 52px;
  box-shadow:-8px 0 48px rgba(11,104,177,.08);
  overflow-y:auto;
}
.form-box{width:100%;max-width:400px;padding:16px 0 40px}

/* Back link */
.back-link{display:inline-flex;align-items:center;gap:7px;color:var(--muted);font-size:.83rem;font-weight:600;text-decoration:none;margin-bottom:32px;transition:color .2s}
.back-link:hover{color:var(--brand)}

/* Heading */
.form-heading{margin-bottom:28px}
.form-heading h2{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:var(--dark);letter-spacing:-.4px;margin-bottom:5px}
.form-heading p{color:var(--muted);font-size:.88rem}

/* Error alert */
.alert{border-radius:var(--r-sm);padding:12px 14px;font-size:.85rem;line-height:1.65;margin-bottom:22px;display:flex;align-items:flex-start;gap:9px}
.alert i{font-size:.9rem;margin-top:2px;flex-shrink:0}
.alert.error{background:var(--red-bg);color:var(--red);border:1px solid rgba(220,38,38,.18)}

/* Role selector cards */
.role-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:22px}
.role-card{
  position:relative;cursor:pointer;
  border:2px solid var(--border);border-radius:var(--r-sm);
  padding:14px 10px;text-align:center;
  transition:border-color .2s,background .2s,transform .15s;
  background:var(--bg);
}
.role-card:hover{border-color:var(--brand);background:var(--brand-light);transform:translateY(-2px)}
.role-card input[type=radio]{position:absolute;opacity:0;width:0;height:0}
.role-card .rc-icon{font-size:1.4rem;margin-bottom:6px;color:var(--muted);transition:color .2s}
.role-card .rc-label{font-size:.82rem;font-weight:700;color:var(--body);transition:color .2s}
.role-card .rc-sub{font-size:.72rem;color:var(--muted);margin-top:2px}
.role-card.selected{border-color:var(--brand);background:var(--brand-light)}
.role-card.selected .rc-icon{color:var(--brand)}
.role-card.selected .rc-label{color:var(--brand)}

/* Form fields */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:7px}
.input-wrap{position:relative}
.input-wrap .fi{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.88rem;pointer-events:none;transition:color .2s}
.form-input,.form-select{
  width:100%;padding:12px 14px 12px 40px;
  border:1.5px solid var(--border);border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;font-size:.92rem;color:var(--dark);
  background:var(--bg);outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;
}
.form-select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238b95a1' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:36px}
.form-input:focus,.form-select:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white)}
.form-input:focus ~ .fi,.input-wrap:focus-within .fi{color:var(--brand)}
.form-input::placeholder{color:#bcc4ce}

/* Password strength */
.pw-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:.88rem;padding:4px;transition:color .2s}
.pw-toggle:hover{color:var(--brand)}
.pw-strength{margin-top:8px;display:flex;gap:4px}
.pw-bar{flex:1;height:3px;border-radius:2px;background:var(--border);transition:background .3s}
.pw-hint{font-size:.74rem;color:var(--muted);margin-top:4px}

/* Phone prefix */
.phone-wrap{display:flex;align-items:center;gap:0}
.phone-prefix{
  padding:12px 12px 12px 14px;
  border:1.5px solid var(--border);border-right:none;
  border-radius:var(--r-sm) 0 0 var(--r-sm);
  background:var(--bg);color:var(--muted);
  font-size:.88rem;font-weight:600;white-space:nowrap;
}
.phone-wrap .form-input{border-radius:0 var(--r-sm) var(--r-sm) 0;padding-left:14px}
.phone-wrap .form-input:focus{z-index:1}

/* Submit */
.submit-btn{
  width:100%;padding:14px;border:none;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;font-family:'DM Sans',sans-serif;
  font-weight:700;font-size:.97rem;cursor:pointer;
  box-shadow:0 4px 16px rgba(11,104,177,.25);
  transition:transform .2s,box-shadow .2s;
  display:flex;align-items:center;justify-content:center;gap:8px;
  margin-top:8px;margin-bottom:20px;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(11,104,177,.38)}

/* Footer */
.form-footer{text-align:center;color:var(--muted);font-size:.88rem}
.form-footer a{color:var(--brand);font-weight:700;text-decoration:none}
.form-footer a:hover{color:var(--brand-dark)}

/* Section label */
.section-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);margin-bottom:10px;display:flex;align-items:center;gap:8px}
.section-label::after{content:'';flex:1;height:1px;background:var(--border)}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:900px){
  .panel-left{display:none}
  .panel-right{width:100%;min-width:0;padding:32px 22px;background:var(--bg);box-shadow:none}
  .form-box{max-width:480px;margin:0 auto;padding:12px 0 40px}
}
@media(max-width:480px){
  .form-row{grid-template-columns:1fr}
  .role-cards{grid-template-columns:1fr 1fr 1fr}
  .form-heading h2{font-size:1.45rem}
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
      <div class="pl-logo">
        <img src="logo.png" alt="HostelConnect">
        <span>HostelConnect</span>
      </div>

      <div class="pl-headline">
        Join thousands of<br><em>Nigerian students</em><br>finding home
      </div>
      <p class="pl-sub">
        Whether you're a student looking for a hostel, a landlord with rooms to let, or an agent — HostelConnect is your platform.
      </p>

      <div class="pl-steps">
        <div class="pl-step">
          <div class="pl-step-num">1</div>
          <div class="pl-step-text">
            <strong>Create your account</strong>
            Takes less than 2 minutes, completely free
          </div>
        </div>
        <div class="pl-step">
          <div class="pl-step-num">2</div>
          <div class="pl-step-text">
            <strong>Verify your email</strong>
            Check your inbox for a verification link
          </div>
        </div>
        <div class="pl-step">
          <div class="pl-step-num">3</div>
          <div class="pl-step-text">
            <strong>Start exploring</strong>
            Browse hostels, post listings, or find roommates
          </div>
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
        <h2>Create your account 🎓</h2>
        <p>Fill in the details below to get started for free</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert error">
          <i class="fas fa-exclamation-circle"></i>
          <span><?= implode('<br>', $errors) ?></span>
        </div>
      <?php endif; ?>

      <form id="registerForm" action="register.php" method="post" autocomplete="on">

        <!-- Role selector — visual cards instead of dropdown -->
        <div class="section-label">I am a</div>
        <div class="role-cards" id="roleCards">
          <label class="role-card <?= old('role',$posted)==='student'?'selected':'' ?>">
            <input type="radio" name="role" value="student" <?= old('role',$posted)==='student'?'checked':'' ?> required>
            <div class="rc-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="rc-label">Student</div>
            <div class="rc-sub">Looking for a roomate/hostel</div>
          </label>
          <label class="role-card <?= old('role',$posted)==='landlord'?'selected':'' ?>">
            <input type="radio" name="role" value="landlord" <?= old('role',$posted)==='landlord'?'checked':'' ?>>
            <div class="rc-icon"><i class="fas fa-home"></i></div>
            <div class="rc-label">Landlord</div>
            <div class="rc-sub">I own property</div>
          </label>
          <label class="role-card <?= old('role',$posted)==='agent'?'selected':'' ?>">
            <input type="radio" name="role" value="agent" <?= old('role',$posted)==='agent'?'checked':'' ?>>
            <div class="rc-icon"><i class="fas fa-briefcase"></i></div>
            <div class="rc-label">Agent</div>
            <div class="rc-sub">I manage listings</div>
          </label>
        </div>

        <!-- Personal info -->
        <div class="section-label">Personal details</div>

        <div class="form-row">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" for="name">Full Name</label>
            <div class="input-wrap">
              <i class="fas fa-user fi"></i>
              <input class="form-input" type="text" id="name" name="name"
                     placeholder="John Doe"
                     value="<?= old('name',$posted) ?>" required autocomplete="name">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" for="username">Username</label>
            <div class="input-wrap">
              <i class="fas fa-at fi"></i>
              <input class="form-input" type="text" id="username" name="username"
                     placeholder="cooluser99"
                     value="<?= old('username',$posted) ?>" required autocomplete="username">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <div class="input-wrap">
            <i class="fas fa-envelope fi"></i>
            <input class="form-input" type="email" id="email" name="email"
                   placeholder="you@example.com"
                   value="<?= old('email',$posted) ?>" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contact">Phone Number</label>
          <div class="phone-wrap">
            <span class="phone-prefix"><i class="fas fa-flag" style="margin-right:4px;font-size:.75rem"></i>+234</span>
            <input class="form-input" type="tel" id="contact" name="contact"
                   placeholder="8012345678"
                   value="<?= old('contact',$posted) ?>"
                   maxlength="10" pattern="\d{10}" required autocomplete="tel">
          </div>
        </div>

        <!-- Password -->
        <div class="section-label">Security</div>

        <div class="form-row">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrap">
              <i class="fas fa-lock fi"></i>
              <input class="form-input" type="password" id="password" name="password"
                     placeholder="Min 8 chars"
                     required autocomplete="new-password"
                     oninput="checkStrength(this.value)"
                     style="padding-right:40px">
              <button type="button" class="pw-toggle" onclick="togglePw('password','pwIcon1')" title="Toggle">
                <i class="fas fa-eye" id="pwIcon1"></i>
              </button>
            </div>
            <div class="pw-strength" id="pwBars">
              <div class="pw-bar" id="b1"></div>
              <div class="pw-bar" id="b2"></div>
              <div class="pw-bar" id="b3"></div>
              <div class="pw-bar" id="b4"></div>
            </div>
            <div class="pw-hint" id="pwHint">At least 8 characters with a number</div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" for="confirmPassword">Confirm Password</label>
            <div class="input-wrap">
              <i class="fas fa-lock fi"></i>
              <input class="form-input" type="password" id="confirmPassword" name="confirmPassword"
                     placeholder="Repeat password"
                     required autocomplete="new-password"
                     style="padding-right:40px">
              <button type="button" class="pw-toggle" onclick="togglePw('confirmPassword','pwIcon2')" title="Toggle">
                <i class="fas fa-eye" id="pwIcon2"></i>
              </button>
            </div>
          </div>
        </div>

        <button type="submit" class="submit-btn">
          <i class="fas fa-user-plus"></i> Create my account
        </button>
      </form>

      <div class="form-footer">
        Already have an account? <a href="login.php">Login here</a>
      </div>

    </div>
  </div>

</div><!-- /split -->

<script>
/* Role card selection */
document.querySelectorAll('.role-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
  });
});

/* Password toggle */
function togglePw(id, iconId) {
  const input = document.getElementById(id);
  const icon  = document.getElementById(iconId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}

/* Password strength meter */
function checkStrength(val) {
  let score = 0;
  if (val.length >= 8)               score++;
  if (/[0-9]/.test(val))             score++;
  if (/[A-Z]/.test(val))             score++;
  if (/[^A-Za-z0-9]/.test(val))      score++;

  const colors = ['','#ef4444','#f97316','#eab308','#16a34a'];
  const labels = ['','Weak','Fair','Good','Strong'];
  const bars   = ['b1','b2','b3','b4'];

  bars.forEach((id, i) => {
    document.getElementById(id).style.background = i < score ? colors[score] : 'var(--border)';
  });
  document.getElementById('pwHint').textContent = val.length ? labels[score] : 'At least 8 characters with a number';
  document.getElementById('pwHint').style.color = colors[score] || 'var(--muted)';
}
</script>
</body>
</html>