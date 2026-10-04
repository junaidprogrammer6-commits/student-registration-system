<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$active = 'students';

$q = trim($_GET['q'] ?? '');
$courseFilter = trim($_GET['course'] ?? '');
$cityFilter = trim($_GET['city'] ?? '');
$genderFilter = trim($_GET['gender'] ?? '');

$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR registration_number LIKE ?)';
    $like = "%$q%";
    array_push($params, $like, $like, $like, $like);
}
if ($courseFilter !== '') { $where[] = 'course = ?'; $params[] = $courseFilter; }
if ($cityFilter !== '') { $where[] = 'city = ?'; $params[] = $cityFilter; }
if ($genderFilter !== '') { $where[] = 'gender = ?'; $params[] = $genderFilter; }

$sql = 'SELECT * FROM students';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$qs = http_build_query(['q' => $q, 'course' => $courseFilter, 'city' => $cityFilter, 'gender' => $genderFilter]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Students</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

<h2 style="margin-top:0">Manage Students</h2>
<?php if (isset($_GET['deleted'])): ?><p class="msg ok">Student record deleted successfully.</p><?php endif; ?>

<form method="get" class="toolbar">
<input type="text" name="q" placeholder="Search student..." value="<?= h($q) ?>" style="flex:2;min-width:200px">
<select name="course" style="flex:1;min-width:140px">
  <option value="">All Courses</option>
  <?php foreach (COURSES as $c): ?><option <?= $courseFilter === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
</select>
<select name="city" style="flex:1;min-width:120px">
  <option value="">All Cities</option>
  <?php foreach (CITIES as $c): ?><option <?= $cityFilter === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
</select>
<select name="gender" style="flex:1;min-width:110px">
  <option value="">All Genders</option>
  <?php foreach (['Male', 'Female', 'Other'] as $g): ?><option <?= $genderFilter === $g ? 'selected' : '' ?>><?= $g ?></option><?php endforeach; ?>
</select>
<button class="btn sec" type="submit">Search</button>
<a class="btn sec" href="students.php">Clear</a>
<a class="btn sec" href="export.php?<?= h($qs) ?>">Export CSV</a>
</form>

<div class="card" style="padding:0;overflow-x:auto">
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>City</th><th>Status</th><th>Action</th></tr>
<?php if (!$students): ?><tr><td colspan="8" style="color:var(--mut)">No student record found.</td></tr><?php endif; ?>
<?php foreach ($students as $s): ?>
<tr>
  <td><?= (int)$s['id'] ?></td>
  <td><?= h($s['full_name']) ?></td>
  <td><?= h($s['email']) ?></td>
  <td><?= h($s['phone']) ?></td>
  <td><span class="badge"><?= h($s['course']) ?></span></td>
  <td><?= h($s['city']) ?></td>
  <td><span class="status-pill <?= h($s['status']) ?>"><?= h($s['status']) ?></span></td>
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
