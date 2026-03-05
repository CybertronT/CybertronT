<?php
require "config.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = "";
$type = "error"; // success or error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $message = " Please enter your email address.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, email_verified FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (!$row['email_verified']) {
                $message = " Your email is not verified. 
                    <a href='resend.php?email=" . urlencode($email) . "' style='color:#0b79d0;'>Resend verification email</a>";
            } else {
                // Generate password reset token (valid for 24 hrs)
                $token = bin2hex(random_bytes(32));
                $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_token_created=NOW() WHERE id=?");
                $stmt->bind_param("si", $token, $row['id']);
                $stmt->execute();

                $resetLink = "https://hostelconnect.com.ng/reset_password.php?token=$token";
                $name = $row['full_name'];

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                   $mail->Host       = 'mail.hostelconnect.com.ng';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'verify@hostelconnect.com.ng';// change
                $mail->Password   = 'H0st3lconnect';   // change
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                    $mail->setFrom('verify@hostelconnect.com.ng','HostelConnect');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = "HostelConnect - Password Reset";
                    $mail->Body    = "
                        <h2>Hello $name,</h2>
                        <p>You requested to reset your password. This link is valid for 24 hours:</p>
                        <p><a href='$resetLink'>Reset My Password</a></p>
                    ";
                    $mail->AltBody = "Hello $name,\n\nReset your password here (valid 24h): $resetLink";

                    $mail->send();
                    $message = " A password reset link has been sent to <strong>$email</strong>. Redirecting to login...";
                    $type = "success";
                } catch (Exception $e) {
                    $message = " Could not send reset email. Please try again later.";
                    error_log("Reset mail error: {$mail->ErrorInfo}");
                }
            }
        } else {
            $message = " No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • Forgot Password</title>
<style>
body{font-family:'Poppins',sans-serif;background:#f5f7fa;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
.box{max-width:450px;padding:30px;background:#fff;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.08);text-align:center;}
h1{color:#0b79d0;font-size:1.5rem;margin-bottom:15px;}
.message{padding:15px;border-radius:8px;font-size:1rem;margin-bottom:20px;}
.message.success{background:#e6f9ec;color:#1b7a2a;border:1px solid #b2e6c2;}
.message.error{background:#fdeaea;color:#a94442;border:1px solid #f5c6cb;}
a.btn{display:inline-block;padding:10px 18px;background:#0b79d0;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;}
a.btn:hover{background:#095a9d;}
form{display:flex;flex-direction:column;gap:15px;margin-top:15px;}
input{padding:12px;border:1px solid #ccc;border-radius:8px;font-size:1rem;}
button{padding:12px;border:none;border-radius:8px;background:#0b79d0;color:#fff;font-weight:600;cursor:pointer;}
button:hover{background:#095a9d;}
</style>
<?php if($message): ?>
<meta http-equiv="refresh" content="5;url=login.php">
<?php endif; ?>
</head>
<body>
  <div class="box">
    <h1>Forgot Password</h1>
    <?php if($message): ?>
      <div class="message <?= $type ?>"><?= $message ?></div>
      <p>You will be redirected to <a href="login.php">Login</a> in 5 seconds.</p>
    <?php else: ?>
      <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
      </form>
      <p style="margin-top:15px;">Remember your password? <a href="login.php" style="color:#0b79d0;">Login</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
