<?php
require "config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Fetch first 20 users
$result = $conn->query("SELECT id, full_name, email FROM users ORDER BY id ASC LIMIT 20 OFFSET 60");


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = trim($row['email']);
        $name  = htmlspecialchars($row['full_name']);

        $mail = new PHPMailer(true);

        try {
            // SMTP Setup
            $mail->isSMTP();
            $mail->Host       = 'smtp.go54mail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'verify@hostelconnect.com.ng';
            $mail->Password   = 'H0st3lconnect';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // From + To
            $mail->setFrom('verify@hostelconnect.com.ng', 'HostelConnect');
            $mail->addAddress($email, $name);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = "We will Be Back Soon. Improving HostelConnect for You";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color:#333; line-height:1.6; text-align:left;'>
                    <h2 style='color:#007bff;'>We’ll Be Back Soon</h2>
                    <p>Hi <strong>$name</strong>,</p>
                    <p>Thank you for being part of the <strong>HostelConnect</strong> community.</p>
                    <p>We’re currently taking a short pause to <strong>add new features, improve the platform experience, and strengthen trust</strong> between students, landlords, and agents.</p>
                    <p>All accounts and data are completely safe — we’re simply working behind the scenes to make things better for you.</p>
                    <p>Our goal is to build a more reliable, transparent, and user-friendly HostelConnect that makes finding or listing hostels easier and more secure for everyone.</p>
                    <p>We truly appreciate your patience and continued support as we prepare to relaunch a stronger version of HostelConnect.</p>
                    <p><strong>You’ll be among the first to know once we’re back online — and we can’t wait to welcome you to the new and improved HostelConnect!</strong></p>
                    <p><strong>— The HostelConnect Team</strong></p>
                    <hr style='margin-top:30px;border:none;border-top:1px solid #ddd;'>
                    <p style='font-size:12px;color:#777;'>
                        You’re receiving this message because you have an account on HostelConnect.<br>
                        Visit <a href='https://hostelconnect.com.ng' style='color:#007bff;'>hostelconnect.com.ng</a> for updates.<br>
                        For support, contact <a href='mailto:support@hostelconnect.com.ng' style='color:#007bff;'>support@hostelconnect.com.ng</a>.
                    </p>
                </div>
            ";

            $mail->AltBody = "We’ll Be Back Soon\n\nHi $name,\n\nThank you for being part of the HostelConnect community.\n\nWe’re currently taking a short pause to add new features, improve the platform, and strengthen trust between students, landlords, and agents.\n\nAll accounts and data are safe — we’re just making things better for you.\n\nYou’ll be among the first to know once we’re back online — and we can’t wait to welcome you to the new and improved HostelConnect!\n\nWe appreciate your patience and support.\n\n— The HostelConnect Team\nsupport@hostelconnect.com.ng\nhttps://hostelconnect.com.ng";

            $mail->send();
            echo "✅ Sent to $email<br>";
            sleep(3); // 3-second delay per email

        } catch (Exception $e) {
            error_log("Failed to send to $email: {$mail->ErrorInfo}");
            echo "❌ Failed to send to $email<br>";
        }
    }

    echo "<hr>✅ Batch 1 (Users 1–20) completed successfully.";

} else {
    echo "⚠️ No users found in the database.";
}
?>
