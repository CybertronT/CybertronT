<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Install via composer: composer require phpmailer/phpmailer

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.go54mail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'verify@hostelconnect.com.ng';
    $mail->Password = 'H0st3lconnect';
    // Use STARTTLS on port 587
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('verify@hostelconnect.com.ng', 'SMTP Test');
    $mail->addAddress('fawazadewale120@gmail.com');

    $mail->Subject = 'SMTP Server Test';
    $mail->Body    = 'This is a test email.';

    $mail->send();
    
    echo "✅ Message sent successfully!";
} catch (Exception $e) {
    echo "❌ SMTP test failed: {$mail->ErrorInfo}";
}
?>
