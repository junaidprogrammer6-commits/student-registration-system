<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$active = 'dashboard';

$total = (int)db()->query('SELECT COUNT(*) FROM students')->fetchColumn();
$courses = (int)db()->query('SELECT COUNT(DISTINCT course) FROM students')->fetchColumn();
$newRegs = (int)db()->query("SELECT COUNT(*) FROM students WHERE created_at >= NOW() - INTERVAL 7 DAY")->fetchColumn();
$activeCount = (int)db()->query("SELECT COUNT(*) FROM students WHERE status = 'Active'")->fetchColumn();
$recent = db()->query('SELECT * FROM students ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

<h2 style="margin-top:0">Dashboard</h2>
<div class="stats">
  <div class="stat"><b><?= $total ?></b><span>Total Students</span></div>
  <div class="stat"><b><?= $courses ?></b><span>Total Courses</span></div>
  <div class="stat"><b><?= $newRegs ?></b><span>New Registrations (7 days)</span></div>
  <div class="stat"><b><?= $activeCount ?></b><span>Active Students</span></div>
</div>

<h3>Recent Registrations</h3>
<div class="card" style="padding:0;overflow-x:auto">
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>City</th><th>Date</th><th>Action</th></tr>
<?php if (!$recent): ?><tr><td colspan="7" style="color:var(--mut)">No students yet.</td></tr><?php endif; ?>
<?php foreach ($recent as $s): ?>
<tr>
  <td><?= (int)$s['id'] ?></td>
  <td><?= h($s['full_name']) ?></td>
  <td><?= h($s['email']) ?></td>
  <td><span class="badge"><?= h($s['course']) ?></span></td>
  <td><?= h($s['city']) ?></td>
  <td><?= h(date('d M Y', strtotime($s['created_at']))) ?></td>
  <td class="actions">
    <a href="view_student.php?id=<?= (int)$s['id'] ?>">View</a>
    <a href="edit_student.php?id=<?= (int)$s['id'] ?>">Edit</a>
    <a href="delete_student.php?id=<?= (int)$s['id'] ?>" style="color:var(--err)">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
</body>
</html>
