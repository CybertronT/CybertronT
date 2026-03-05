<?php
require "config.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);

    $stmt = $conn->prepare("SELECT id, full_name, email_verified FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['email_verified']) {
            // Already verified → go back to login
            header("Location: login.php?success=" . urlencode("✅ Your account is already verified. Please log in."));
            exit;
        }

        // New token
        $token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare("UPDATE users SET verify_token=?, token_created_at=NOW() WHERE id=?");
        $stmt->bind_param("si", $token, $row['id']);
        $stmt->execute();

        $verifyLink = "https://hostelconnect.com.ng/verify.php?token=$token";
        $name = $row['full_name'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
                $mail->Host       = 'smtp.go54mail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'verify@hostelconnect.com.ng';// change
                $mail->Password   = 'H0st3lconnect';   // change
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

            $mail->setFrom('verify@hostelconnect.com.ng', 'HostelConnect');
            $mail->addAddress($email, $name);

          $mail->isHTML(true);
$mail->Subject = "Your new HostelConnect verification link";

$mail->Body = "
    <h3>Hello $name,</h3>
    <p>You requested a new verification link for your <strong>HostelConnect</strong> account.</p>
    <p>Please verify your email within 24 hours by clicking the link below:</p>
    <p><a href='$verifyLink'>$verifyLink</a></p>
    <p>If you did not request this please ignore this message.</p>
    <p> The HostelConnect Team</p>
";

$mail->AltBody = "Hello $name,\n\nYou requested a new verification link for your HostelConnect account.\nPlease verify within 24 hours using this link: $verifyLink\n\nIf you didn’t request this, ignore this email.\n\n— The HostelConnect Team";

$mail->send();


            // Redirect to login with success message
            header("Location: login.php?success=" . urlencode("📩 A new verification email has been sent to $email. Check your inbox."));
            exit;
        } catch (Exception $e) {
            header("Location: login.php?error=" . urlencode("❌ Could not send verification email. Try again later."));
            error_log("Resend mail error: {$mail->ErrorInfo}");
            exit;
        }
    } else {
        header("Location: login.php?error=" . urlencode("❌ No account found with that email."));
        exit;
    }
} else {
    header("Location: login.php?error=" . urlencode("❌ Invalid request."));
    exit;
}
?>
