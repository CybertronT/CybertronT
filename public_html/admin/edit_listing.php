<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? '';
$role = $_SESSION['role'] ?? '';
$contact = $_SESSION['contact'] ?? '';

// Get listing ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listings.php");
    exit;
}
$listing_id = (int)$_GET['id'];

// Fetch listing
$stmt = $conn->prepare("SELECT * FROM hostels WHERE id = ? AND user_id = ? ");
$stmt->bind_param("ii", $listing_id, $user_id, );
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();

if (!$listing) {
    echo "<script>alert('You are not authorized to edit this listing'); window.location.href='listings.php';</script>";
    exit;
}

// Pre-fill variables
$title = $listing['title'];
$type = $listing['type'];
$city = $listing['city'];
$area = $listing['area'];
$price = $listing['price'];
$period = $listing['period'];
$facilities = json_decode($listing['facilities'], true) ?? [];
$description = $listing['description'];
$photos = json_decode($listing['photos'], true) ?? [];
$agentFee = $listing['agentFee'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Listing • HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#008CBA; --bg:#f5f7fa; --card:#fff; --text:#333; --ring:#e5e7eb;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;}
    .sidebar{width:220px;background:var(--primary);color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;padding:20px;}
    .sidebar h2{font-size:1.4rem;margin-bottom:20px;}
    .sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
    .sidebar a:hover{background:#005f8f;}
    .main{flex:1;margin-left:220px;padding:20px;}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .topbar h1{color:var(--primary);}
    .card{background:var(--card);padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
    .field{display:flex;flex-direction:column;gap:6px;margin:10px 0;}
    .row{display:grid;gap:10px;}
    @media(min-width:640px){.row.cols-2{grid-template-columns:1fr 1fr;}}
    input, select, textarea{border:1px solid var(--ring);border-radius:10px;padding:10px 12px;background:#fff;}
    textarea{min-height:90px;resize:vertical;}
    .checklist{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;}
    .chip{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--ring);border-radius:999px;padding:8px 12px;background:#fff;}
    .btn-link{cursor:pointer;display:inline-block;border-radius:10px;padding:10px 14px;font-weight:600;background:var(--primary);color:#fff;text-decoration:none;}
    .btn-link:hover{background:#005f8f;}
    .thumbs{display:flex;flex-direction:column;gap:8px;margin-top:8px;}
    .thumb-box{display:flex;align-items:center;gap:10px;}
    .thumb-box img{width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid var(--ring);}
  </style>
</head>
<body>
<div class="sidebar">
  <h2>HostelConnect</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="add_listing.php">Add Listing</a>
  <a href="listings.php">My Listings</a>
  <a href="profile.php">Profile</a>
  <a href="logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Edit Listing</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($role) ?>)</div>
  </div>

  <section class="card">
    <form id="editForm" action="update_listing.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="listing_id" value="<?= $listing_id ?>">

      <?php if($role === 'agent'): ?>
      <div class="field">
        <label for="agentFee">Agent Fee (₦)</label>
        <input id="agentFee" name="agentFee" type="number" min="0" value="<?= htmlspecialchars($agentFee) ?>">
      </div>
      <?php endif; ?>

      <div class="row cols-2">
        <div class="field">
          <label for="title">Property Title</label>
          <input id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div class="field">
          <label for="roomType">Room / Unit Type</label>
          <select id="roomType" name="roomType" required>
            <option <?= $type=='Self-contain'?'selected':'' ?>>Self-contain</option>
            <option <?= $type=='Single room'?'selected':'' ?>>Single room</option>
            <option <?= $type=='2-bedroom'?'selected':'' ?>>2-bedroom</option>
            <option <?= $type=='Shared room'?'selected':'' ?>>Shared room</option>
          </select>
        </div>
      </div>

      <div class="row cols-2">
        <div class="field">
          <label for="city">City</label>
          <input id="city" name="city" value="<?= htmlspecialchars($city) ?>" required>
        </div>
        <div class="field">
          <label for="area">Suburb / Area</label>
          <input id="area" name="area" value="<?= htmlspecialchars($area) ?>" required>
        </div>
      </div>

      <div class="row cols-2">
        <div class="field">
          <label for="price">Price (₦)</label>
          <input id="price" name="price" type="number" min="0" value="<?= htmlspecialchars($price) ?>" required>
        </div>
        <div class="field">
          <label for="period">Billing Period</label>
          <select id="period" name="period">
            <option <?= $period=='per month'?'selected':'' ?>>per month</option>
            <option <?= $period=='per semester'?'selected':'' ?>>per semester</option>
            <option <?= $period=='per year'?'selected':'' ?>>per year</option>
          </select>
        </div>
      </div>

      <div class="field">
        <label>Facilities</label>
        <div class="checklist">
          <?php
            $allFacilities = ["Ensuite bathroom","Shared kitchen","Wi-Fi","Power backup","Furnished","Water supply"];
            foreach($allFacilities as $f){
                $checked = in_array($f,$facilities) ? "checked":""; 
                echo "<label class='chip'><input type='checkbox' name='facilities[]' value='$f' $checked> $f</label>";
            }
          ?>
        </div>
      </div>

      <div class="field">
        <label for="desc">Short Description</label>
        <textarea id="desc" name="description"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <div class="field">
        <label>Current Photos</label>
        <div class="thumbs">
          <?php foreach($photos as $p): ?>
            <div class="thumb-box">
              <img src="uploads/<?= htmlspecialchars($p) ?>" alt="photo">
              <label><input type="checkbox" name="remove_photos[]" value="<?= htmlspecialchars($p) ?>"> Remove</label>
            </div>
          <?php endforeach; ?>
        </div>
        <label>Upload New Photos (optional)</label>
        <input type="file" name="photos[]" multiple accept="image/*">
      </div>

      <button type="submit" class="btn-link">Update Listing</button>
    </form>
  </section>
</div>
</body>
</html>
