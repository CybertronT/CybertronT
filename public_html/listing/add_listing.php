<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? '';
$role      = $_SESSION['role'] ?? '';
$contact   = $_SESSION['contact'] ?? '';

// --- Listing limit check ---
$listing_limit = 5;
$stmt = $conn->prepare("SELECT COUNT(*) FROM hostels WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_count);
$stmt->fetch();
$stmt->close();

$limit_reached = $current_count >= $listing_limit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • Add Listing</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary:#008CBA;
    --accent:#e67e22;
    --bg:#f5f7fa;
    --card:#fff;
    --text:#333;
    --muted:#666;
    --ring:#e5e7eb;
}

*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:row;}

/* Sidebar */
.sidebar{width:220px;background:var(--primary);color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;padding:20px;transition:left 0.3s;z-index:1000;}
.sidebar h2{font-size:1.4rem;margin-bottom:20px;}
.sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
.sidebar a:hover{background:#005f8f;}

/* Sidebar Toggle Button */
#sidebarToggle{display:none;position:fixed;top:15px;left:15px;z-index:1100;background:var(--primary);color:#fff;border:none;padding:10px 12px;border-radius:6px;font-size:1.2rem;cursor:pointer;}

/* Main content */
.main{flex:1;margin-left:220px;padding:20px;transition:margin-left 0.3s;}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;}
.topbar h1{color:var(--primary);font-size:1.8rem;}
.topbar .welcome{color:var(--text);font-weight:500;}

/* Card / Form */
.card{background:var(--card);padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);margin-bottom:20px;}
.field{display:flex;flex-direction:column;gap:6px;margin:10px 0;}
.row{display:grid;gap:10px;}
@media(min-width:640px){.row.cols-2{grid-template-columns:1fr 1fr;}}
input, select, textarea{border:1px solid var(--ring);border-radius:10px;padding:10px 12px;background:#fff;}
textarea{min-height:90px;resize:vertical;}
.checklist{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;}
.chip{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--ring);border-radius:999px;padding:8px 12px;background:#fff;}
.thumbs{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
.thumbs img{width:62px;height:62px;object-fit:cover;border-radius:8px;border:1px solid var(--ring);}
.btn-link{cursor:pointer;display:inline-block;border-radius:10px;padding:10px 14px;font-weight:600;background:var(--primary);color:#fff;text-decoration:none;text-align:center;border:none;}
.btn-link:hover{background:#005f8f;}
.hidden{display:none;}
.limit-msg{text-align:center;padding:40px 20px;}
.limit-msg h2{color:var(--primary);margin-bottom:10px;}
.limit-msg p{margin:6px 0;}
.limit-msg a{display:inline-block;margin-top:20px;padding:10px 16px;background:var(--primary);color:#fff;text-decoration:none;border-radius:8px;}
.limit-msg a:hover{background:#005f8f;}
footer{text-align:center;margin-top:40px;font-size:.9rem;color:var(--muted);}

/* Responsive */
@media(max-width:768px){
    .sidebar{left:-250px;width:250px;position:fixed;top:0;bottom:0;}
    #sidebarToggle{display:block;}
    .main{margin-left:0;padding:70px 15px 15px 15px;}
}
</style>
</head>
<body>

<!-- Hamburger button for mobile -->
<button id="sidebarToggle">☰</button>

<div class="sidebar" id="sidebar">
   <h2>HostelConnect</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="add_listing.php">Add Listing</a>
  <a href="listings.php">My Listings</a>
  <a href="profile.php">Profile</a>
  <a href="logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>Add Listing</h1>
    <div class="welcome">Welcome, <?= htmlspecialchars($user_name) ?> (<?= htmlspecialchars($role) ?>)</div>
  </div>

  <section class="card">
    <?php if ($limit_reached): ?>
      <div class="limit-msg">
        <h2>Limit Reached</h2>
        <p>You have reached the maximum of <?= $listing_limit ?> listings allowed.</p>
        <p>Please contact support to upgrade your account.</p>
         <p><a href="mailto:support@hostelconnect.com.ng">support@hostelconnect.com.ng</a></p>
        <p><a href="tel:+2347040357149">+2347040357149</a></p>
        <a href="listings.php">Go to My Listings</a>
      </div>
    <?php else: ?>
      <form id="listForm" action="add_hostel.php" method="post" enctype="multipart/form-data" autocomplete="on">

        <!-- Agent Fee only for agents -->
        <?php if($role === 'agent'): ?>
        <div class="field">
            <label for="agentFee">Agent Fee (₦)</label>
            <input id="agentFee" name="agentFee" type="text" placeholder="Agent Fee">
        </div>
        <?php endif; ?>

        <div class="row cols-2">
          <div class="field">
            <label for="title">Property Title</label>
            <input id="title" name="title" placeholder="e.g., 2-Bedroom Ensuite by South Gate" required>
          </div>
          <div class="field">
            <label for="roomType">Room / Unit Type</label>
            <select id="roomType" name="roomType" required>
              <option value="">Select type…</option>
              <option>Self-contain</option>
              <option>Single room</option>
              <option>2-bedroom</option>
              <option>Shared room</option>
            </select>
          </div>
        </div>

        <div class="row cols-2">
          <div class="field">
            <label for="city">Campus/School (e.g University of Ilesa - Ilesa)</label>
            <select id="city" name="city" required>
              <option value="">Choose…</option>
              <option>Kwara State University - Malete Kwara State</option>
              <option>University of Ilesa - Ilesa</option>
              <option>Osun state University - Osun</option>
              <option>University of Ibadan - Ibadan</option>
              <option>Obafemi Awolowo University - Ile-Ife</option>
              <option>Lagos State University - Lagos</option>
              <option>Ekiti State University - Ekiti</option>
              <option>Other</option>
            </select>
          </div>
          <div class="field">
            <label for="area">Suburb / Area</label>
            <input id="area" name="area" placeholder="e.g., Bodija" required>
          </div>
        </div>

        <div class="row cols-2">
          <div class="field">
            <label for="price">Price (₦)</label>
            <input id="price" name="price" type="text" placeholder="e.g., 20,000" required>
          </div>
          <div class="field">
            <label for="period">Billing Period</label>
            <select id="period" name="period" required>
              <option>per year</option>
              <option>per semester</option>
              <option>per month</option>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Facilities</label>
          <div class="checklist">
            <label class="chip"><input type="checkbox" name="facilities[]" value="Ensuite bathroom"> Ensuite bathroom</label>
            <label class="chip"><input type="checkbox" name="facilities[]" value="Shared kitchen"> Shared kitchen</label>
            <label class="chip"><input type="checkbox" name="facilities[]" value="Wi-Fi"> Wi-Fi</label>
            <label class="chip"><input type="checkbox" name="facilities[]" value="Power backup"> Power backup</label>
            <label class="chip"><input type="checkbox" name="facilities[]" value="Furnished"> Furnished</label>
            <label class="chip"><input type="checkbox" name="facilities[]" value="Water supply"> Water supply</label>
          </div>
        </div>

        <div class="field">
          <label for="desc">Short Description</label>
          <textarea id="desc" name="description" placeholder="Brief details, rules, key highlights…" required></textarea>
        </div>

        <div class="field" id="photoUploadSection" style="display:none;">
          <label>Upload Photos</label>
          <div id="photoUploads"></div>
        </div>

        <div class="field" id="photoUploadNotice">
          <p style="color:#f00;">Please select a room/unit type before uploading images.</p>
        </div>

        <button type="submit" class="btn-link">Submit Listing</button>
      </form>
    <?php endif; ?>
  </section>
</div>

<script>
// JS stays unchanged (formatting, preview, sidebar toggle, etc.)
(() => {
  const $ = id => document.getElementById(id);
  const roomTypeSelect = $('roomType');
  const photoUploadsDiv = $('photoUploads');
  const photoUploadSection = $('photoUploadSection');
  const photoUploadNotice = $('photoUploadNotice');
  const sidebar = $('sidebar');
  const toggleBtn = $('sidebarToggle');
  const priceInput = $('price');
  const agentFeeInput = $('agentFee');

  function formatWithCommas(input) {
    let value = input.value.replace(/,/g, '');
    if (!isNaN(value) && value.length > 0) {
      input.value = parseInt(value, 10).toLocaleString();
    }
  }

  if (priceInput) {
    priceInput.addEventListener('input', () => formatWithCommas(priceInput));
  }
  if (agentFeeInput) {
    agentFeeInput.addEventListener('input', () => formatWithCommas(agentFeeInput));
  }

  const form = document.getElementById('listForm');
  if (form) {
    form.addEventListener('submit', () => {
      if (priceInput) priceInput.value = priceInput.value.replace(/,/g, '');
      if (agentFeeInput) agentFeeInput.value = agentFeeInput.value.replace(/,/g, '');
    });
  }

  toggleBtn.addEventListener('click', () => {
    sidebar.style.left = sidebar.style.left === '0px' ? '-250px' : '0px';
  });

  function renderThumbFile(file, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
      alert(`File "${file.name}" is too large (max 5MB).`);
      return;
    }
    const img = document.createElement('img');
    img.style.width = '70px';
    img.style.height = '70px';
    img.style.objectFit = 'cover';
    const reader = new FileReader();
    reader.onload = e => img.src = e.target.result;
    reader.readAsDataURL(file);
    container.appendChild(img);
  }

  function createRoomField(index, numberLabel) {
    const labelText = numberLabel ? `Room ${index}` : 'Room';
    const inputId = numberLabel ? `photoRoom${index}` : 'photoRoom';
    const thumbId = numberLabel ? `thumbRoom${index}` : 'thumbRoom';
    const wrapper = document.createElement('div');
    wrapper.className = 'sub-field room-upload';
    wrapper.innerHTML = `
      <label>${labelText}</label>
      <input type="file" name="photos[]" id="${inputId}" accept="image/*" required />
      <div id="${thumbId}" class="thumbs"></div>`;
    photoUploadsDiv.appendChild(wrapper);
  }

  function createStaticField(labelText, inputId, thumbId) {
    const wrapper = document.createElement('div');
    wrapper.className = 'sub-field';
    wrapper.innerHTML = `
      <label>${labelText}</label>
      <input type="file" name="photos[]" id="${inputId}" accept="image/*" required />
      <div id="${thumbId}" class="thumbs"></div>`;
    photoUploadsDiv.appendChild(wrapper);
  }

  function attachPreviewHandlers() {
    const inputs = photoUploadsDiv.querySelectorAll('input[type="file"]');
    inputs.forEach(inp => {
      inp.onchange = () => {
        const id = inp.id;
        let thumbId;
        if (id.startsWith('photoRoom')) thumbId = id.replace('photo', 'thumb');
        else if (id === 'photoKitchen') thumbId = 'thumbKitchen';
        else if (id === 'photoBathroom') thumbId = 'thumbBathroom';
        else if (id === 'photoOutside') thumbId = 'thumbOutside';

        if (inp.files[0]) {
          if (inp.files[0].size > 5 * 1024 * 1024) {
            alert(`File "${inp.files[0].name}" is too large (max 5MB).`);
            inp.value = '';
            document.getElementById(thumbId).innerHTML = '';
            return;
          }
          renderThumbFile(inp.files[0], thumbId);
        }
      };
    });
  }

  function buildPhotoFields() {
    photoUploadsDiv.innerHTML = '';
    const isTwoBedroom = /\b2[- ]?bed/i.test(roomTypeSelect.value);
    if (isTwoBedroom) {
      createRoomField(1, true);
      createRoomField(2, true);
    } else {
      createRoomField(1, false);
    }
    createStaticField('Kitchen', 'photoKitchen', 'thumbKitchen');
    createStaticField('Bathroom', 'photoBathroom', 'thumbBathroom');
    createStaticField('Outside / Compound', 'photoOutside', 'thumbOutside');
    attachPreviewHandlers();
  }

  if (roomTypeSelect) {
    roomTypeSelect.addEventListener('change', () => {
      if (roomTypeSelect.value) {
        photoUploadNotice.style.display = 'none';
        photoUploadSection.style.display = 'block';
        buildPhotoFields();
      } else {
        photoUploadNotice.style.display = 'block';
        photoUploadSection.style.display = 'none';
      }
    });
  }
})();
</script>
</body>
</html>
