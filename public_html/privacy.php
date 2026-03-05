<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
   <link rel="icon" type="image/png" href="logo.png">
  <style>
    * {box-sizing:border-box;margin:0;padding:0;}
    body {font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;line-height:1.7;}
    header {background:white;padding:15px 25px;position:sticky;top:0;z-index:1000;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    header .logo {display:flex;align-items:center;gap:12px;}
    header .logo img {height:55px;}
    header .logo span {font-size:1.4rem;font-weight:600;color:#222;}
    .container {max-width:900px;margin:30px auto;padding:25px;background:white;
      border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
    h1 {font-size:2rem;margin-bottom:20px;color:#222;}
    h2 {font-size:1.3rem;margin:25px 0 10px;color:#444;}
    p {margin-bottom:15px;color:#555;}
    ul {margin-left:20px;margin-bottom:15px;}
    ul li {margin-bottom:8px;}
    footer {text-align:center;padding:20px;background:#222;color:#ddd;margin-top:40px;}
    a {color:#008CBA;text-decoration:none;}
    a:hover {text-decoration:underline;}
  </style>
</head>
<body>
<header>
  <div class="logo">
    <img src="logo.png" alt="HostelConnect">
    <span>HostelConnect</span>
  </div>
</header>

<div class="container">
    
  <h1>Privacy Policy</h1>
  <p>Date: October 1, 2025.</p>
  <p>Last updated: October 5, 2025.</p>

  <p>At <strong>HostelConnect</strong>, your privacy is important to us. This policy explains how we collect, use, and protect your information. HostelConnect is a connection platform that helps students find verified hostels and agents — we <strong>do not collect or process any payments</strong>.</p>

  <h2>1. Information We Collect</h2>
  <p>When you use HostelConnect, we may collect the following information:</p>
  <ul>
    <li>Account details (name, email, phone number, password – stored securely).</li>
    <li>Hostel listings you submit (title, description, photos, facilities, contact info).</li>
    <li>Feedback you post (comments, ratings).</li>
    <li>Usage data (views per hostel, cookies for session tracking).</li>
  </ul>

  <h2>2. How We Use Your Information</h2>
  <p>Your information is used to:</p>
  <ul>
    <li>Provide and improve our hostel listing and connection service.</li>
    <li>Allow communication between landlords/agents and students.</li>
    <li>Display feedback and ratings to other users.</li>
    <li>Track listing popularity (views count) and site activity.</li>
    <li>Maintain security and prevent abuse.</li>
  </ul>

  <h2>3. No Payments or Transactions</h2>
  <p><strong>HostelConnect does not handle or process any payments between users, landlords, or agents.</strong> Our role is strictly to connect students with verified hostel providers. All financial transactions, agreements, and payments take place directly between the student and the landlord/agent — outside of our platform.</p>
  <p>We strongly advise users to verify listings, meet safely, and avoid making online payments without proper verification. HostelConnect is not responsible for payment disputes, scams, or fraud occurring outside the platform.</p>

  <h2>4. Cookies & Tracking</h2>
  <p>We use cookies to:</p>
  <ul>
    <li>Keep you logged into your account.</li>
    <li>Prevent multiple view counts from the same user in one session.</li>
    <li>Improve your browsing experience and functionality.</li>
  </ul>
  <p>You can disable cookies in your browser, but some features may not work properly.</p>

  <h2>5. Sharing of Information</h2>
  <p>We do not sell your personal data. We may only share limited information with:</p>
  <ul>
    <li>Service providers (e.g., hosting, analytics, or email services).</li>
    <li>Law enforcement if required by law.</li>
  </ul>

  <h2>6. Data Retention</h2>
  <p>
    We keep your account data and hostel listings until you request deletion.<br>
    Feedback and reviews are stored permanently to maintain transparency for all users.<br>
    Hostel view counts and analytics are anonymized and not linked to personal details.
  </p>

  <h2>7. Security</h2>
  <p>
    We take reasonable measures to protect your information, including encryption of passwords and secure access controls.  
    However, no method of transmission over the Internet is 100% secure.
  </p>

  <h2>8. Your Rights</h2>
  <p>You have the right to:</p>
  <ul>
    <li>Request a copy of your data.</li>
    <li>Update or correct your account details.</li>
    <li>Request account deletion.</li>
  </ul>
  <p>To exercise these rights, contact us at <a href="mailto:support@hostelconnect.com.ng">support@hostelconnect.com.ng</a>.</p>

  <h2>9. Changes to this Policy</h2>
  <p>
    We may update this Privacy Policy occasionally.  
    Any changes will be posted on this page with the updated date.
  </p>

  <h2>10. Contact Us</h2>
  <p>
    If you have questions or concerns about this Privacy Policy, please contact us at  
    <a href="mailto:privacy@hostelconnect.com.ng">privacy@hostelconnect.com.ng</a>.
  </p>
</div>

<footer>
  <p>&copy; <?= date("Y") ?> HostelConnect. All rights reserved.</p>
</footer>
</body>
</html>
