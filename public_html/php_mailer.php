<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'mail.hostelconnect.com.ng';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'verify@hostelconnect.com.ng';
    $mail->Password   = 'H0st3lconnect';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('verify@hostelconnect.com.ng', 'HostelConnect');
    // Optional defaults
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

} catch (Exception $e) {
    error_log('Mailer initialization failed: ' . $e->getMessage());
}
?>


