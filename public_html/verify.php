<?php 
require "config.php";

$status  = "error";
$message = "Invalid or expired verification token.";
$link    = "<a class='btn' href='login.php'>Login</a>";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id, token_created_at FROM users WHERE verify_token=? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $createdAt = strtotime($row['token_created_at']);
        if (time() - $createdAt <= 86400) { // 24 hours
            $stmt = $conn->prepare("UPDATE users SET email_verified=1, verify_token=NULL, token_created_at=NULL WHERE id=?");
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();

            $status  = "success";
            $message = "Your email has been successfully verified!";
        } else {
            $message = "Your verification link has expired.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification • HostelConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Poppins',sans-serif;background:#f5f7fa;display:flex;align-items:center;justify-content:center;height:100vh;}
  .card{background:#fff;padding:30px 25px;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,0.1);text-align:center;max-width:400px;width:100%;}
  .logo img{height:70px;margin-bottom:15px;}
  h1{font-size:1.6rem;color:#008CBA;margin-bottom:10px;}
  p{font-size:1rem;color:#555;margin-bottom:20px;}
  .success{color:#28a745;font-weight:600;}
  .error{color:#dc3545;font-weight:600;}
  .btn{display:inline-block;padding:10px 18px;border-radius:6px;background:#008CBA;color:#fff;text-decoration:none;font-weight:600;transition:0.3s;}
  .btn:hover{background:#006b91;}
</style>
</head>
<body>
  <div class="card">
    <div class="logo">
      <img src="logo.png" alt="HostelConnect">
    </div>
    <h1>Email Verification</h1>
    <p class="<?= $status ?>"><?= htmlspecialchars($message) ?></p>
    <?= $link ?>
  </div>
</body>
</html>
