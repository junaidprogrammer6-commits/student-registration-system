<?php require __DIR__ . '/config.php'; $active = 'home'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Registration System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="hero">
<h1>Student Registration System</h1>
<p>Register your information easily and securely through our online student registration system.</p>
<div class="cta">
<a class="btn" href="register.php">Register Now</a>
<a class="btn sec" href="login.php">Student Login</a>
</div>
</div>

<div class="w">
<div class="features">
<div class="feature"><b>Easy Registration</b><span>Students can register online by providing their personal and academic information.</span></div>
<div class="feature"><b>Student Records</b><span>Student information is stored in a database for easy management.</span></div>
<div class="feature"><b>Easy Management</b><span>Admin can view, update and delete student records.</span></div>
<div class="feature"><b>Secure System</b><span>Login and session-based access keeps student and admin areas protected.</span></div>
</div>
</div>
</body>
</html>
