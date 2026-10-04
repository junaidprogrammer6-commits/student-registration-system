<?php
require __DIR__ . '/../config.php';
session_start();

if (!empty($_SESSION['admin'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['email'] ?? '');
    $pass     = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $error = 'No admin account found. Run admin/create_admin.php once to create one.';
    } elseif (password_verify($pass, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="navbar"><div class="w">
<div class="brand"><span class="dot"></span><h1>Student Registration System</h1></div>
<nav><a href="../index.php">Back to site</a><?php include __DIR__ . '/../includes/theme_toggle.php'; ?></nav>
</div></div>
<div class="w" style="max-width:400px;padding-top:60px">
<div class="card">
<h2 style="margin-top:0">Admin Login</h2>
<?php if ($error): ?><p class="msg err"><?= h($error) ?></p><?php endif; ?>
<form method="post">
  <div class="field"><label>Admin Username</label><input name="email" autofocus></div>
  <div class="field"><label>Password</label><input name="password" type="password"></div>
  <button class="btn" type="submit">Login</button>
</form>
</div>
</div>
</body>
</html>
