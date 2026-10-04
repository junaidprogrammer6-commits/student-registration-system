<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$active = 'students';

$_SESSION['csrf'] ??= bin2hex(random_bytes(16));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        db()->prepare('DELETE FROM students WHERE id = ?')->execute([(int)$_POST['id']]);
    }
    header('Location: students.php?deleted=1');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT full_name FROM students WHERE id = ?');
$stmt->execute([$id]);
$name = $stmt->fetchColumn();
if ($name === false) { header('Location: students.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Delete Student</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
<div class="modal-bg">
<div class="modal">
<h3 style="margin-top:0">Are you sure you want to delete this student?</h3>
<p><?= h($name) ?> will be permanently removed. This cannot be undone.</p>
<form method="post" class="row-btns">
  <input type="hidden" name="id" value="<?= $id ?>">
  <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
  <a class="btn sec" href="students.php">Cancel</a>
  <button class="btn danger" type="submit">Delete Student</button>
</form>
</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
</body>
</html>
