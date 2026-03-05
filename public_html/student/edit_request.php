<?php
session_start();
require "../config.php";


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}


$allowed_roles = [ 'student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}


$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? '';
$role = $_SESSION['role'] ?? 'student';

$id = intval($_GET['id'] ?? 0);

// Fetch request
$stmt = $conn->prepare("SELECT * FROM roommate_requests WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Request not found or not yours.");
}

$photos = $request['photos'] ? json_decode($request['photos'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Request • HostelConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:#f5f7fa;display:flex;min-height:100vh;color:#333;}

/* Sidebar */
.sidebar{
  width:220px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;
  position:fixed;top:0;bottom:0;left:0;padding:20px;transition:0.3s;z-index:1000;
}
.sidebar h2{font-size:1.4rem;margin-bottom:20px;}
.sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
.sidebar a:hover{background:#005f8f;}
.sidebar.active{left:-250px;}

/* Main */
.main{flex:1;margin-left:220px;display:flex;flex-direction:column;transition:0.3s;}

/* Topbar */
.topbar{
  display:flex;justify-content:space-between;align-items:center;background:#fff;padding:15px 20px;
  box-shadow:0 2px 6px rgba(0,0,0,0.08);
}
.topbar h1{color:#008CBA;font-size:1.5rem;}
.topbar .welcome{font-weight:500;}
.topbar .menu-toggle{display:none;font-size:1.5rem;background:#008CBA;color:#fff;border:none;border-radius:6px;padding:6px 12px;cursor:pointer;}

/* Container */
.container {
  max-width: 1000px;
  width: 100%;
  margin: 30px auto;
  padding: 20px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
h2{text-align:center;color:#008CBA;margin-bottom:20px;}
.field{margin-bottom:15px;display:flex;flex-direction:column;}
label{margin-bottom:6px;font-weight:600;}
input, select, textarea{padding:10px;border:1px solid #e5e7eb;border-radius:8px;width:100%;}
textarea{resize:vertical;min-height:100px;}
.btn{padding:12px 18px;background:#008CBA;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;}
.btn:hover{background:#005f8f;}

/* Thumbnails */
.thumbs img{width:70px;height:70px;object-fit:cover;border-radius:6px;margin:6px;border:1px solid #ddd;}
.thumb-box{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.thumb-box label{font-weight:normal;}

/* Responsive */
@media(max-width:768px){
  .sidebar{left:-250px;}
  .sidebar.show{left:0;}
  .main{margin-left:0;}
  .topbar .menu-toggle{display:block;}
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h2>HostelConnect</h2>
  <a href="student_dashboard.php">Dashboard</a>
  
  <a href="request_roommate.php">Post Request</a>
  <a href="student_request.php">My Requests</a>
   <a href="student_profile.php">Profile</a>
  <a href="../logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">
  <!-- Topbar -->
  <div class="topbar">
    <button class="menu-toggle" id="menuToggle">&#9776;</button>
    <h1>Edit Request</h1>
    <div class="welcome">Hi, <?= htmlspecialchars($user_name) ?> (<?= ucfirst($role) ?>)</div>
  </div>

  <!-- Form -->
  <div class="container">
    <form action="update_request.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $request['id'] ?>">

      <!-- Campus -->
      <div class="field">
        <label for="campus">Campus / School</label>
        <select id="campus" name="campus" required>
          <option value="">Choose…</option>
          <option <?= ($request['campus'] === 'University of Ilesa - Ilesa') ? 'selected' : '' ?>>University of Ilesa - Ilesa</option>
          <option <?= ($request['campus'] === 'Osun State University - Osun') ? 'selected' : '' ?>>Osun State University - Osun</option>
          <option <?= ($request['campus'] === 'University of Ibadan - Ibadan') ? 'selected' : '' ?>>University of Ibadan - Ibadan</option>
          <option <?= ($request['campus'] === 'Obafemi Awolowo University - Ile-Ife') ? 'selected' : '' ?>>Obafemi Awolowo University - Ile-Ife</option>
        </select>
      </div>

      <!-- Area -->
      <div class="field">
        <label for="area">Preferred Area / Suburb</label>
        <input id="area" name="area" type="text" value="<?= htmlspecialchars($request['area']) ?>" required>
      </div>

      <!-- Room Type -->
      <div class="field">
        <label for="room_type">Room Type</label>
        <select id="room_type" name="room_type" required>
          <option value="">Select type…</option>
          <option value="Self-contain" <?= ($request['room_type'] === 'Self-contain') ? 'selected' : '' ?>>Self-contain</option>
          <option value="Single room" <?= ($request['room_type'] === 'Single room') ? 'selected' : '' ?>>Single room</option>
          <option value="2-bedroom" <?= ($request['room_type'] === '2-bedroom') ? 'selected' : '' ?>>2-bedroom</option>
          <option value="Shared room" <?= ($request['room_type'] === 'Shared room') ? 'selected' : '' ?>>Shared room</option>
        </select>
      </div>

      <!-- Budget -->
      <div class="field">
        <label>Budget (₦)</label>
        <input type="number" name="budget_min" value="<?= $request['budget_min'] ?>" required>
        <input type="number" name="budget_max" value="<?= $request['budget_max'] ?>" required style="margin-top:6px;">
      </div>

      <!-- Gender -->
      <div class="field">
        <label>Gender Preference</label>
        <select name="gender_pref" required>
          <option value="">Select…</option>
          <option value="Any" <?= ($request['gender_pref'] === 'Any') ? 'selected' : '' ?>>Any</option>
          <option value="Male" <?= ($request['gender_pref'] === 'Male') ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= ($request['gender_pref'] === 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
      </div>

      <!-- Religion -->
      <div class="field">
        <label>Religion Preference</label>
        <select name="religion_pref" required>
          <option value="">Select…</option>
          <option value="Any" <?= ($request['religion_pref'] === 'Any') ? 'selected' : '' ?>>Any</option>
          <option value="Christianity" <?= ($request['religion_pref'] === 'Christianity') ? 'selected' : '' ?>>Christianity</option>
          <option value="Islam" <?= ($request['religion_pref'] === 'Islam') ? 'selected' : '' ?>>Islam</option>
          <option value="Other" <?= ($request['religion_pref'] === 'Other') ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <!-- Move-in Date -->
      <div class="field">
        <label for="move_in_date">Move-in Date</label>
        <input id="move_in_date" name="move_in_date" type="date" value="<?= $request['move_in_date'] ?>">
      </div>

      <!-- Description -->
      <div class="field">
        <label for="description">Additional Details</label>
        <textarea id="description" name="description"><?= htmlspecialchars($request['description']) ?></textarea>
      </div>

      <!-- Photos -->
      <div class="field">
        <h3>Existing Photos</h3>
        <div class="thumbs">
          <?php foreach ($photos as $photo): ?>
            <div class="thumb-box">
              <img src="uploads/roommates/<?= htmlspecialchars($photo) ?>" alt="">
              <label><input type="checkbox" name="remove_photos[]" value="<?= htmlspecialchars($photo) ?>"> Remove</label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="field">
        <h3>Add New Photos</h3>
        <input type="file" name="photos[]" multiple accept="image/*">
      </div>

      <button type="submit" class="btn">Update Request</button>
    </form>
  </div>
</div>
<script>
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('show');
});

// --- File validation ---
const fileInput = document.querySelector('input[type="file"][name="photos[]"]');
if (fileInput) {
  fileInput.addEventListener('change', function () {
    const maxSize = 5 * 1024 * 1024; // 5 MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/jpg', 'image/png'];

    for (const file of this.files) {
      if (!allowedTypes.includes(file.type)) {
        alert(`File "${file.name}" is not a valid type. Allowed: JPG, JPEG, PNG.`);
        this.value = ''; // reset
        return;
      }
      if (file.size > maxSize) {
        alert(`File "${file.name}" is too large. Max size is 5MB.`);
        this.value = ''; // reset
        return;
      }
    }
  });
}
</script>


</body>
</html>
