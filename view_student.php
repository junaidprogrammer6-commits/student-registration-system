<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$active = 'students';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$s) { header('Location: students.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Details</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

<h2 style="margin-top:0">Student Details</h2>

<div class="card">
<h3 style="margin-top:0">Personal Details</h3>
<div class="detail-grid">
<div><span>Student ID</span><b><?= (int)$s['id'] ?></b></div>
<div><span>Registration No.</span><b><?= h($s['registration_number']) ?></b></div>
<div><span>Full Name</span><b><?= h($s['full_name']) ?></b></div>
<div><span>Father Name</span><b><?= h($s['father_name']) ?></b></div>
<div><span>Email</span><b><?= h($s['email']) ?></b></div>
<div><span>Phone</span><b><?= h($s['phone']) ?></b></div>
<div><span>Date of Birth</span><b><?= h($s['dob'] ?: '—') ?></b></div>
<div><span>Gender</span><b><?= h($s['gender']) ?></b></div>
<div><span>CNIC / B-Form</span><b><?= h($s['cnic'] ?: '—') ?></b></div>
</div>
</div>

<div class="card">
<h3 style="margin-top:0">Address Details</h3>
<div class="detail-grid">
<div><span>Address</span><b><?= h($s['address'] ?: '—') ?></b></div>
<div><span>City</span><b><?= h($s['city']) ?></b></div>
<div><span>Province</span><b><?= h($s['province'] ?: '—') ?></b></div>
<div><span>Postal Code</span><b><?= h($s['postal_code'] ?: '—') ?></b></div>
</div>
</div>

<div class="card">
<h3 style="margin-top:0">Academic Details</h3>
<div class="detail-grid">
<div><span>Qualification</span><b><?= h($s['qualification'] ?: '—') ?></b></div>
<div><span>Course</span><b><?= h($s['course']) ?></b></div>
<div><span>Institute</span><b><?= h($s['institute'] ?: '—') ?></b></div>
<div><span>Admission Year</span><b><?= h($s['admission_year'] ?: '—') ?></b></div>
</div>
</div>

<div class="card">
<h3 style="margin-top:0">Account Details</h3>
<div class="detail-grid">
<div><span>Registration Date</span><b><?= h(date('d M Y', strtotime($s['created_at']))) ?></b></div>
<div><span>Account Status</span><b><span class="status-pill <?= h($s['status']) ?>"><?= h($s['status']) ?></span></b></div>
</div>
</div>

<div style="display:flex;gap:10px">
<a class="btn" href="edit_student.php?id=<?= (int)$s['id'] ?>">Edit Student</a>
<a class="btn danger" href="delete_student.php?id=<?= (int)$s['id'] ?>">Delete Student</a>
<a class="btn sec" href="students.php">Back to Students</a>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
</body>
</html>
