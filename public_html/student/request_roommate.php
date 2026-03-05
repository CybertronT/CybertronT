<?php
session_start();
require "../config.php";

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../login.php?redirect=$redirect");
    exit;
}
$allowed_roles = ['student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? '';
$contact   = $_SESSION['contact'] ?? '';
$role      = $_SESSION['role'] ?? 'student';

function initials($name) {
    $parts = array_filter(explode(' ', $name));
    return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($parts, 0, 2))));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Roommate Request — HostelConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="icon" type="image/png" href="../logo.png">
  <style>
/* ============================================================
   RESET & VARIABLES — identical to student_dashboard.php
============================================================ */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#0b68b1;--brand-dark:#084e87;--brand-light:#dff0ff;
  --accent:#cf6c17;--accent-dark:#a85510;--accent-light:#fff0e3;
  --dark:#0d1117;--body:#374151;--muted:#8b95a1;
  --border:#e4e9ef;--bg:#f4f7fc;--white:#ffffff;
  --green:#16a34a;--green-bg:#dcfce7;
  --red:#dc2626;--red-bg:#fee2e2;
  --yellow:#f59e0b;--yellow-bg:#fef3c7;
  --sidebar-w:248px;--topbar-h:68px;
  --shadow-sm:0 2px 12px rgba(11,104,177,.07);
  --shadow-md:0 8px 32px rgba(11,104,177,.12);
  --r:14px;--r-sm:9px;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--body);display:flex;min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:10px}

/* ============================================================
   SIDEBAR — identical to student_dashboard.php
============================================================ */
.sidebar{
  width:var(--sidebar-w);min-width:var(--sidebar-w);
  background:linear-gradient(180deg,#0a5fa0 0%,#073d6e 100%);
  display:flex;flex-direction:column;
  position:fixed;top:0;bottom:0;left:0;
  z-index:200;transition:transform .3s cubic-bezier(.22,1,.36,1);
  box-shadow:4px 0 24px rgba(11,104,177,.18);
}
.sb-brand{display:flex;align-items:center;gap:10px;padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:10px}
.sb-brand img{width:36px;height:36px;border-radius:8px}
.sb-brand span{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#fff;letter-spacing:-.2px}
.sb-nav{flex:1;padding:8px 12px;overflow-y:auto}
.sb-section{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.28);padding:12px 8px 6px;margin-top:4px}
.sb-link{display:flex;align-items:center;gap:11px;padding:11px 12px;border-radius:var(--r-sm);color:rgba(255,255,255,.65);font-size:.88rem;font-weight:500;text-decoration:none;margin-bottom:2px;transition:background .2s,color .2s;position:relative}
.sb-link i{width:18px;text-align:center;font-size:.9rem;flex-shrink:0}
.sb-link:hover{background:rgba(255,255,255,.1);color:#fff}
.sb-link.active{background:rgba(255,255,255,.15);color:#fff;font-weight:600}
.sb-link.active::before{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:#fbbf24;border-radius:0 3px 3px 0}
.sb-link.danger{color:rgba(255,120,120,.75)}
.sb-link.danger:hover{background:rgba(220,38,38,.15);color:#fca5a5}
.sb-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.08)}
.sb-user{display:flex;align-items:center;gap:10px}
.sb-avatar{width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,rgba(255,255,255,.25),rgba(255,255,255,.1));border:2px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:.85rem;font-weight:800;color:#fff}
.sb-user-info .sb-name{font-size:.85rem;font-weight:700;color:#fff;line-height:1.2}
.sb-user-info .sb-role{font-size:.72rem;color:rgba(255,255,255,.4);text-transform:capitalize}

/* ============================================================
   MAIN — identical to student_dashboard.php
============================================================ */
.main{flex:1;margin-left:var(--sidebar-w);display:flex;flex-direction:column;min-height:100vh;transition:margin-left .3s cubic-bezier(.22,1,.36,1)}

/* Topbar */
.topbar{height:var(--topbar-h);display:flex;align-items:center;justify-content:space-between;background:var(--white);padding:0 28px;border-bottom:1px solid var(--border);box-shadow:var(--shadow-sm);position:sticky;top:0;z-index:100;gap:16px}
.topbar-left{display:flex;align-items:center;gap:14px}
.menu-toggle{display:none;width:40px;height:40px;border-radius:var(--r-sm);background:var(--brand-light);border:1.5px solid rgba(11,104,177,.15);color:var(--brand);font-size:1rem;cursor:pointer;align-items:center;justify-content:center;transition:background .2s}
.menu-toggle:hover{background:#cce4f8}
.topbar-title{font-family:'Playfair Display',serif;font-size:1.25rem;font-weight:700;color:var(--dark)}
.topbar-right{display:flex;align-items:center;gap:10px}
.topbar-welcome{font-size:.87rem;color:var(--muted);font-weight:500}
.topbar-welcome strong{color:var(--dark)}
.topbar-badge{display:inline-flex;align-items:center;gap:5px;background:var(--brand-light);color:var(--brand);border:1px solid rgba(11,104,177,.15);border-radius:100px;padding:5px 12px;font-size:.76rem;font-weight:700;text-transform:capitalize}

/* ============================================================
   CONTENT
============================================================ */
.content{padding:28px;flex:1;max-width:860px;width:100%}

/* Page intro */
.page-intro{margin-bottom:26px}
.page-intro h2{font-family:'Playfair Display',serif;font-size:1.45rem;font-weight:800;color:var(--dark);letter-spacing:-.3px;margin-bottom:4px}
.page-intro p{color:var(--muted);font-size:.9rem}

/* ============================================================
   FORM CARD
============================================================ */
.form-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--r);padding:32px;
  box-shadow:var(--shadow-sm);
}

/* Section headers inside form */
.form-section-label{
  font-size:.72rem;font-weight:700;text-transform:uppercase;
  letter-spacing:1.2px;color:var(--muted);
  margin-bottom:16px;margin-top:28px;
  display:flex;align-items:center;gap:8px;
}
.form-section-label:first-child{margin-top:0}
.form-section-label::after{content:'';flex:1;height:1px;background:var(--border)}

/* 2-col grid for paired fields */
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}
.field-row.three{grid-template-columns:1fr 1fr 1fr}

/* Field group */
.field{margin-bottom:18px}
.field:last-of-type{margin-bottom:0}
.field-label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:7px}
.input-wrap{position:relative}
.input-wrap .fi{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.88rem;pointer-events:none;transition:color .2s}
.form-input,.form-select,.form-textarea{
  width:100%;padding:12px 13px 12px 38px;
  border:1.5px solid var(--border);border-radius:var(--r-sm);
  font-family:'DM Sans',sans-serif;font-size:.92rem;color:var(--dark);
  background:var(--bg);outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
}
.form-input:focus,.form-select:focus,.form-textarea:focus{
  border-color:var(--brand);box-shadow:0 0 0 3px rgba(11,104,177,.1);background:var(--white);
}
.form-input:focus ~ .fi,.input-wrap:focus-within .fi{color:var(--brand)}
.form-input::placeholder,.form-textarea::placeholder{color:#bcc4ce}
.form-select{
  appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238b95a1' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 14px center;background-color:var(--bg);
  padding-right:36px;
}
.form-textarea{padding:12px 13px;min-height:110px;resize:vertical;line-height:1.65;height:auto}

/* No icon variant */
.no-icon .form-input,
.no-icon .form-select{padding-left:13px}

/* Budget row — side by side with dash */
.budget-row{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:10px}
.budget-dash{color:var(--muted);font-size:1.1rem;text-align:center;font-weight:600;padding-top:22px}

/* Photo upload fields */
.photo-upload-field{
  background:var(--bg);border:1.5px dashed var(--border);
  border-radius:var(--r-sm);padding:18px;margin-bottom:12px;
  transition:border-color .2s;
}
.photo-upload-field:hover{border-color:var(--brand)}
.photo-upload-label{font-size:.8rem;font-weight:700;color:var(--body);margin-bottom:10px;display:flex;align-items:center;gap:7px}
.photo-upload-label i{color:var(--brand);font-size:.85rem}
.photo-file-input{
  width:100%;padding:10px 12px;
  border:1.5px solid var(--border);border-radius:var(--r-sm);
  background:var(--white);color:var(--body);
  font-family:'DM Sans',sans-serif;font-size:.85rem;cursor:pointer;
  transition:border-color .2s;
}
.photo-file-input:hover{border-color:var(--brand)}
.photo-thumb-wrap{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.photo-thumb{width:68px;height:68px;object-fit:cover;border-radius:8px;border:2px solid var(--border);display:block}

/* Submit row */
.submit-row{display:flex;align-items:center;gap:14px;margin-top:28px;padding-top:24px;border-top:2px solid var(--bg)}
.submit-btn{
  display:inline-flex;align-items:center;gap:8px;
  padding:14px 32px;border:none;border-radius:var(--r-sm);
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:.95rem;
  cursor:pointer;box-shadow:0 4px 16px rgba(11,104,177,.25);
  transition:transform .2s,box-shadow .2s;
}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(11,104,177,.38)}
.cancel-link{color:var(--muted);font-size:.88rem;font-weight:600;text-decoration:none;transition:color .2s}
.cancel-link:hover{color:var(--red)}

/* Footer */
.dash-footer{text-align:center;padding:18px;font-size:.8rem;color:var(--muted);border-top:1px solid var(--border);background:var(--white)}

/* Overlay */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:199;backdrop-filter:blur(2px)}
.sb-overlay.show{display:block}

/* ============================================================
   RESPONSIVE
============================================================ */
@media(max-width:900px){
  .sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
  .sidebar.show{transform:translateX(0)}
  .main{margin-left:0}
  .menu-toggle{display:flex}
  .field-row{grid-template-columns:1fr}
  .field-row.three{grid-template-columns:1fr}
  .budget-row{grid-template-columns:1fr auto 1fr}
}
@media(max-width:600px){
  .content{padding:18px 16px}
  .form-card{padding:20px 18px}
  .topbar{padding:0 18px}
  .budget-row{grid-template-columns:1fr}
  .budget-dash{display:none}
  .submit-row{flex-direction:column;align-items:stretch}
  .submit-btn{justify-content:center}
}
  </style>
</head>
<body>

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ============================================================
     SIDEBAR — identical to student_dashboard.php
============================================================ -->
<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <img src="../logo.png" alt="HostelConnect">
    <span>HostelConnect</span>
  </div>
  <nav class="sb-nav">
    <div class="sb-section">Main</div>
    <a href="student_dashboard.php" class="sb-link"><i class="fas fa-home"></i> Dashboard</a>
    <a href="../hostel.php" class="sb-link"><i class="fas fa-building"></i> Browse Hostels</a>
    <a href="../roommates.php" class="sb-link"><i class="fas fa-users"></i> Find Roommates</a>
    <div class="sb-section">My Activity</div>
    <a href="request_roommate.php" class="sb-link active"><i class="fas fa-plus-circle"></i> Post Request</a>
    <a href="student_request.php" class="sb-link"><i class="fas fa-folder-open"></i> My Requests</a>
    <div class="sb-section">Account</div>
    <a href="student_profile.php" class="sb-link"><i class="fas fa-user-circle"></i> My Profile</a>
    <a href="../logout.php" class="sb-link danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-avatar"><?= initials($user_name) ?></div>
      <div class="sb-user-info">
        <div class="sb-name"><?= htmlspecialchars($user_name) ?></div>
        <div class="sb-role"><?= ucfirst($role) ?></div>
      </div>
    </div>
  </div>
</aside>

<!-- ============================================================
     MAIN
============================================================ -->
<div class="main">

  <div class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" id="menuToggle" onclick="openSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <div class="topbar-title">Post Roommate Request</div>
    </div>
    <div class="topbar-right">
      <span class="topbar-welcome">Hi, <strong><?= htmlspecialchars($user_name) ?></strong></span>
      <span class="topbar-badge"><i class="fas fa-graduation-cap"></i> <?= ucfirst($role) ?></span>
    </div>
  </div>

  <div class="content">

    <div class="page-intro">
      <h2>Post a Roommate Request</h2>
      <p>Fill in the details below — your request will be reviewed and published after approval</p>
    </div>

    <div class="form-card">
      <form id="roommateForm" action="submit_roommate_request.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <!-- Location -->
        <div class="form-section-label"><i class="fas fa-map-marker-alt" style="color:var(--brand)"></i> Location & Room</div>

        <div class="field-row">
          <div class="field">
            <label class="field-label" for="campus">Campus / School</label>
            <div class="input-wrap">
              <i class="fas fa-graduation-cap fi"></i>
              <select class="form-select" id="campus" name="campus" required>
                <option value="">Choose campus…</option>
                <option>Kwara State University - Malete Kwara State</option>
                <option>University of Ilesa - Ilesa</option>
                <option>Osun State University - Osun</option>
                <option>University of Ibadan - Ibadan</option>
                <option>Obafemi Awolowo University - Ile-Ife</option>
                <option>Lagos State University - Lagos</option>
                <option>Ekiti State University - Ekiti</option>
                <option>Other</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="field-label" for="area">Preferred Area / Suburb</label>
            <div class="input-wrap">
              <i class="fas fa-map-pin fi"></i>
              <input class="form-input" type="text" id="area" name="area" placeholder="e.g., Agbowo, Yaba" required>
            </div>
          </div>
        </div>

        <div class="field">
          <label class="field-label" for="room_type">Room Type</label>
          <div class="input-wrap">
            <i class="fas fa-door-open fi"></i>
            <select class="form-select" id="room_type" name="room_type" required>
              <option value="">Select type…</option>
              <option value="Self-contain">Self-contain</option>
              <option value="Single room">Single room</option>
              <option value="2-bedroom">2-bedroom</option>
              <option value="Shared room">Shared room</option>
            </select>
          </div>
        </div>

        <!-- Budget -->
        <div class="form-section-label"><i class="fas fa-wallet" style="color:var(--accent)"></i> Budget</div>

        <div class="field no-icon">
          <label class="field-label">Budget Range (₦)</label>
          <div class="budget-row">
            <div class="input-wrap no-icon">
              <input class="form-input" style="padding-left:13px" type="number" name="budget_min" placeholder="Minimum ₦" required min="0">
            </div>
            <div class="budget-dash">–</div>
            <div class="input-wrap no-icon">
              <input class="form-input" style="padding-left:13px" type="number" name="budget_max" placeholder="Maximum ₦" required min="0">
            </div>
          </div>
        </div>

        <!-- Preferences -->
        <div class="form-section-label"><i class="fas fa-sliders-h" style="color:var(--brand)"></i> Roommate Preferences</div>

        <div class="field-row">
          <div class="field">
            <label class="field-label">Gender Preference</label>
            <div class="input-wrap">
              <i class="fas fa-user fi"></i>
              <select class="form-select" name="gender_pref" required>
                <option value="">Select…</option>
                <option value="Any">Any</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="field-label">Religion Preference</label>
            <div class="input-wrap">
              <i class="fas fa-praying-hands fi"></i>
              <select class="form-select" name="religion_pref" required>
                <option value="">Select…</option>
                <option value="Any">Any</option>
                <option value="Christianity">Christianity</option>
                <option value="Islam">Islam</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
        </div>

        <div class="field">
          <label class="field-label" for="move_in_date">Move-in Date</label>
          <div class="input-wrap">
            <i class="fas fa-calendar-alt fi"></i>
            <input class="form-input" id="move_in_date" name="move_in_date" type="text"
                   placeholder="MM/DD/YYYY" required
                   onfocus="this.type='date'" onblur="if(!this.value) this.type='text'">
          </div>
        </div>

        <!-- Description -->
        <div class="form-section-label"><i class="fas fa-align-left" style="color:var(--brand)"></i> About You</div>

        <div class="field">
          <label class="field-label" for="description">Additional Details</label>
          <textarea class="form-textarea" id="description" name="description"
                    placeholder="E.g., I prefer quiet roommates, I'm a final year student, I keep late nights…"></textarea>
        </div>

        <!-- Photos -->
        <div class="form-section-label"><i class="fas fa-camera" style="color:var(--brand)"></i> Photos</div>
        <p style="font-size:.84rem;color:var(--muted);margin-bottom:16px">Upload photos of the space — max 5MB each, JPG/PNG/WebP only</p>
        <div id="photoUploads"></div>

        <!-- Submit -->
        <div class="submit-row">
          <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane"></i> Submit Request
          </button>
          <a href="student_dashboard.php" class="cancel-link">Cancel</a>
        </div>

      </form>
    </div>
  </div>

  <div class="dash-footer">
    &copy; <?= date('Y') ?> HostelConnect &mdash; Built for Nigerian students 🇳🇬
  </div>

</div><!-- /main -->

<script>
/* ── Sidebar ───────────────────────────────────── */
function openSidebar()  { document.getElementById('sidebar').classList.add('show');   document.getElementById('sbOverlay').classList.add('show'); }
function closeSidebar() { document.getElementById('sidebar').classList.remove('show'); document.getElementById('sbOverlay').classList.remove('show'); }
document.querySelectorAll('.sb-link').forEach(a => {
  a.addEventListener('click', () => { if (window.innerWidth <= 900) closeSidebar(); });
});

/* ── Photo uploads — ALL original logic preserved ── */
const allowedTypes = ['image/jpeg','image/png','image/webp'];
const maxFileSize  = 5 * 1024 * 1024;

const roomTypeSelect   = document.getElementById('room_type');
const photoUploadsDiv  = document.getElementById('photoUploads');
const form             = document.getElementById('roommateForm');

function renderThumb(file, containerId) {
  const container = document.getElementById(containerId);
  container.innerHTML = '';
  if (!file) return;
  const img    = document.createElement('img');
  img.className = 'photo-thumb';
  const reader = new FileReader();
  reader.onload = e => img.src = e.target.result;
  reader.readAsDataURL(file);
  container.appendChild(img);
}

function createPhotoField(labelText, inputId, thumbId) {
  const wrapper = document.createElement('div');
  wrapper.className = 'photo-upload-field';
  wrapper.innerHTML = `
    <div class="photo-upload-label">
      <i class="fas fa-image"></i> ${labelText}
    </div>
    <input type="file" name="photos[]" id="${inputId}" class="photo-file-input"
           accept="image/jpeg,image/png,image/jpg,image/webp" required>
    <div id="${thumbId}" class="photo-thumb-wrap"></div>
  `;
  photoUploadsDiv.appendChild(wrapper);

  wrapper.querySelector('input').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    if (!allowedTypes.includes(file.type)) {
      alert('Invalid file type: ' + file.name + '. Use JPG, PNG or WebP.');
      e.target.value = ''; return;
    }
    if (file.size > maxFileSize) {
      alert('File too large (max 5MB): ' + file.name);
      e.target.value = ''; return;
    }
    renderThumb(file, thumbId);
  });
}

function buildPhotoFields() {
  photoUploadsDiv.innerHTML = '';
  const isTwoBedroom = /\b2[- ]?bed/i.test(roomTypeSelect.value);
  if (isTwoBedroom) {
    createPhotoField('Room 1', 'photoRoom1', 'thumbRoom1');
    createPhotoField('Room 2', 'photoRoom2', 'thumbRoom2');
  } else {
    createPhotoField('Room', 'photoRoom', 'thumbRoom');
  }
  createPhotoField('Kitchen',            'photoKitchen',  'thumbKitchen');
  createPhotoField('Bathroom',           'photoBathroom', 'thumbBathroom');
  createPhotoField('Outside / Compound', 'photoOutside',  'thumbOutside');
}

document.addEventListener('DOMContentLoaded', buildPhotoFields);
roomTypeSelect.addEventListener('change', buildPhotoFields);

// Budget validation
form.addEventListener('submit', e => {
  const minB = parseFloat(form.budget_min.value);
  const maxB = parseFloat(form.budget_max.value);
  if (minB > maxB) {
    alert('Minimum budget cannot be greater than maximum budget.');
    e.preventDefault();
    return false;
  }
});
</script>
</body>
</html>