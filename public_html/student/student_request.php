<?php
session_start();
require "../config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$allowed_roles = ['student'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

$user_id   = intval($_SESSION['user_id']);
$user_name = $_SESSION['name'] ?? "User";

// Handle availability toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $request_id   = intval($_POST['id']);
    $availability = isset($_POST['availability']) ? 'available' : 'unavailable';

    $stmt = $conn->prepare("UPDATE roommate_requests SET availability = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $availability, $request_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Refresh page after update
    header("Location: student_request.php?success=Availability updated");
    exit;
}

// Fetch roommate requests
$stmt = $conn->prepare("SELECT id, campus, area, room_type, budget_min, budget_max, status, availability 
                        FROM roommate_requests 
                        WHERE user_id = ? 
                        ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HostelConnect • My Requests</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  html, body{height:100%;}
  body{font-family:'Poppins',sans-serif;background:#f5f7fa;color:#333;display:flex;min-height:100vh;}

  .sidebar{width:220px;background:#008CBA;color:#fff;flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;padding:20px;transition:0.3s;left:0;z-index:1000;}
  .sidebar h2{font-size:1.4rem;margin-bottom:20px;color:#fff;}
  .sidebar a{color:#fff;text-decoration:none;padding:10px 12px;margin-bottom:8px;border-radius:6px;display:block;}
  .sidebar a:hover{background:#005f8f;}
  .sidebar-toggle{display:none;position:fixed;top:15px;left:15px;font-size:1.5rem;background:#008CBA;color:#fff;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;z-index:1100;}
  .sidebar-toggle:hover{background:#005f8f;}

  .main{margin-left:220px;flex:1;display:flex;flex-direction:column;padding:20px;transition:0.3s;}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
  .topbar h1{color:#008CBA;font-size:1.8rem;}
  .topbar .welcome{color:#333;font-weight:500;}
  .main-content{flex:1;}

  .table-container{overflow-x:auto;} /* ✅ scrollable on small screens */
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);min-width:700px;}
  th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #eee;}
  th{background:#008CBA;color:#fff;}
  tr:hover{background:#f1f9ff;}

  .status{padding:4px 8px;border-radius:5px;font-size:0.8rem;font-weight:600;text-transform:capitalize;}
  .status.pending{background:#fff3cd;color:#856404;}
  .status.approved{background:#d4edda;color:#155724;}
  .status.rejected{background:#f8d7da;color:#721c24;}

  .btn{padding:6px 12px;border:none;border-radius:6px;background:#008CBA;color:#fff;font-size:0.9rem;font-weight:500;cursor:pointer;text-decoration:none;display:inline-block;margin:2px 0;white-space:nowrap;}
  .btn:hover{background:#005f8f;}
  .btn.edit { background:#28a745; } 
  .btn.edit:hover { background:#1e7e34; }

  .empty{padding:20px;text-align:center;color:#777;background:#fff;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.05);}
  .alert-success{background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:15px;}
  .alert-error{background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:15px;}

  footer{text-align:center;margin-top:auto;font-size:.9rem;color:#666;}

  /* Toggle Switch */
  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
  }
  .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
  }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0;
    right: 0; bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }
  input:checked + .slider {
    background-color: #28a745;
  }
  input:checked + .slider:before {
    transform: translateX(26px);
  }

  @media(max-width:768px){
    .sidebar{position:fixed;left:-250px;top:0;bottom:0;width:220px;flex-direction:column;overflow-y:auto;}
    .sidebar.active{left:0;}
    .sidebar-toggle{display:block;}
    .main{margin-left:0;margin-top:10px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
    table{font-size:0.85rem;}
    th,td{padding:8px;}

    /* ✅ Stack action buttons vertically */
    td:last-child {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
  }
</style>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>

<div class="sidebar" id="sidebar">
  <h2>HostelConnect</h2>
  <a href="student_dashboard.php">Dashboard</a>
  <a href="request_roommate.php">Post Request</a>
  <a href="student_request.php">My Requests</a>
  <a href="student_profile.php">Profile</a>
  <a href="../logout.php">Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>My Requests</h1>
    <div class="welcome">Hi, <?= htmlspecialchars($user_name) ?></div>
  </div>

  <div class="main-content">
    <?php if($success): ?><div class="alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (!empty($requests)): ?>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Campus</th>
              <th>Area</th>
              <th>Room Type</th>
              <th>Budget</th>
              <th>Status</th>
              <th>Availability</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td><?= htmlspecialchars($req['campus']) ?></td>
                <td><?= htmlspecialchars($req['area']) ?></td>
                <td><?= htmlspecialchars($req['room_type']) ?></td>
                <td>₦<?= number_format($req['budget_min']) ?> - ₦<?= number_format($req['budget_max']) ?></td>
                <td><span class="status <?= strtolower($req['status']) ?>"><?= htmlspecialchars($req['status']) ?></span></td>
                <td>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to change availability?');">
                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                    <label class="switch">
                      <input type="checkbox" name="availability" value="available"
                             onchange="this.form.submit()"
                             <?= ($req['availability'] === 'available') ? 'checked' : '' ?>>
                      <span class="slider round"></span>
                    </label>
                  </form>
                </td>
                <td>
                  <a class="btn edit" href="edit_request.php?id=<?= $req['id'] ?>">Edit</a>
                  <a class="btn" href="roommate_detail.php?id=<?= $req['id'] ?>">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty">You haven’t posted any roommate requests yet.</div>
    <?php endif; ?>
  </div>

  <footer>
    &copy; <?= date("Y") ?> HostelConnect
  </footer>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');
});
</script>

</body>
</html>
