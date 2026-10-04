<?php
require __DIR__ . '/config.php';
$active = 'register';

$errors = [];
$success = false;
$f = array_fill_keys([
    'full_name','father_name','email','phone','dob','gender','cnic',
    'address','city','province','postal_code',
    'qualification','course','institute','admission_year',
    'username'
], '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($f as $k => $v) $f[$k] = trim($_POST[$k] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm_password'] ?? '');

    if ($f['full_name'] === '' || mb_strlen($f['full_name']) > 100) $errors['full_name'] = 'Enter your full name.';
    if ($f['father_name'] === '') $errors['father_name'] = "Enter your father's name.";
    if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email.';
    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $f['phone'])) $errors['phone'] = 'Enter a valid phone number.';
    if (!in_array($f['gender'], ['Male', 'Female', 'Other'], true)) $errors['gender'] = 'Select a gender.';
    if ($f['city'] === '') $errors['city'] = 'Select a city.';
    if ($f['course'] === '') $errors['course'] = 'Select a course.';
    if ($f['username'] === '' || !preg_match('/^[a-zA-Z0-9_.]{3,30}$/', $f['username'])) {
        $errors['username'] = 'Username must be 3-30 characters (letters, numbers, _ or .).';
    }
    if (mb_strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $pdo = db();
            $dup = $pdo->prepare('SELECT COUNT(*) FROM students WHERE email = ? OR username = ?');
            $dup->execute([$f['email'], $f['username']]);
            if ((int)$dup->fetchColumn() > 0) {
                $errors['general'] = 'Email or username is already registered.';
            } else {
                $regNo = next_registration_number();
                $hash  = password_hash($password, PASSWORD_DEFAULT);
                $stmt  = $pdo->prepare(
                    'INSERT INTO students
                     (registration_number, full_name, father_name, email, phone, dob, gender, cnic,
                      address, city, province, postal_code, qualification, course, institute,
                      admission_year, username, password)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $regNo, $f['full_name'], $f['father_name'], $f['email'], $f['phone'],
                    $f['dob'] ?: null, $f['gender'], $f['cnic'],
                    $f['address'], $f['city'], $f['province'], $f['postal_code'],
                    $f['qualification'], $f['course'], $f['institute'], $f['admission_year'],
                    $f['username'], $hash,
                ]);
                $success = $regNo;
                $f = array_fill_keys(array_keys($f), '');
            }
        } catch (PDOException $e) {
            $errors['general'] = $e->getCode() === '23000'
                ? 'Email or username is already registered.'
                : 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register | Student Registration System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="w" style="max-width:720px;padding-top:40px;padding-bottom:60px">
<div class="card">
<h2 style="margin-top:0">Student Registration</h2>

<?php if ($success): ?>
  <p class="msg ok">Student registered successfully. Your registration number is <strong><?= h($success) ?></strong>. You can now <a href="login.php">log in</a>.</p>
<?php endif; ?>
<?php if (!empty($errors['general'])): ?>
  <p class="msg err"><?= h($errors['general']) ?></p>
<?php endif; ?>

<form method="post" novalidate id="regForm">
  <fieldset>
    <legend>Personal Information</legend>
    <div class="row">
      <div class="field"><label>Full Name</label><input name="full_name" value="<?= h($f['full_name']) ?>">
        <?php if (!empty($errors['full_name'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['full_name']) ?></small><?php endif; ?></div>
      <div class="field"><label>Father Name</label><input name="father_name" value="<?= h($f['father_name']) ?>">
        <?php if (!empty($errors['father_name'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['father_name']) ?></small><?php endif; ?></div>
    </div>
    <div class="row">
      <div class="field"><label>Email</label><input name="email" type="email" value="<?= h($f['email']) ?>">
        <?php if (!empty($errors['email'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['email']) ?></small><?php endif; ?></div>
      <div class="field"><label>Phone Number</label><input name="phone" value="<?= h($f['phone']) ?>">
        <?php if (!empty($errors['phone'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['phone']) ?></small><?php endif; ?></div>
    </div>
    <div class="row">
      <div class="field"><label>Date of Birth</label><input name="dob" type="date" value="<?= h($f['dob']) ?>"></div>
      <div class="field"><label>Gender</label>
        <select name="gender">
          <option value="">Select gender</option>
          <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
            <option <?= $f['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['gender'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['gender']) ?></small><?php endif; ?></div>
    </div>
    <div class="field"><label>CNIC / B-Form Number</label><input name="cnic" value="<?= h($f['cnic']) ?>"></div>
  </fieldset>

  <fieldset>
    <legend>Address Information</legend>
    <div class="field"><label>Address</label><input name="address" value="<?= h($f['address']) ?>"></div>
    <div class="row">
      <div class="field"><label>City</label>
        <select name="city">
          <option value="">Select city</option>
          <?php foreach (CITIES as $c): ?><option <?= $f['city'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
        </select>
        <?php if (!empty($errors['city'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['city']) ?></small><?php endif; ?></div>
      <div class="field"><label>Province</label><input name="province" value="<?= h($f['province']) ?>"></div>
    </div>
    <div class="field"><label>Postal Code</label><input name="postal_code" value="<?= h($f['postal_code']) ?>"></div>
  </fieldset>

  <fieldset>
    <legend>Academic Information</legend>
    <div class="row">
      <div class="field"><label>Highest Qualification</label><input name="qualification" value="<?= h($f['qualification']) ?>"></div>
      <div class="field"><label>Course</label>
        <select name="course">
          <option value="">Select course</option>
          <?php foreach (COURSES as $c): ?><option <?= $f['course'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
        </select>
        <?php if (!empty($errors['course'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['course']) ?></small><?php endif; ?></div>
    </div>
    <div class="row">
      <div class="field"><label>Institute Name</label><input name="institute" value="<?= h($f['institute']) ?>"></div>
      <div class="field"><label>Admission Year</label><input name="admission_year" value="<?= h($f['admission_year']) ?>" placeholder="e.g. 2026"></div>
    </div>
  </fieldset>

  <fieldset>
    <legend>Account Information</legend>
    <div class="field"><label>Username</label><input name="username" value="<?= h($f['username']) ?>">
      <?php if (!empty($errors['username'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['username']) ?></small><?php endif; ?></div>
    <div class="row">
      <div class="field"><label>Password</label><input name="password" type="password">
        <?php if (!empty($errors['password'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['password']) ?></small><?php endif; ?></div>
      <div class="field"><label>Confirm Password</label><input name="confirm_password" type="password">
        <?php if (!empty($errors['confirm_password'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['confirm_password']) ?></small><?php endif; ?></div>
    </div>
  </fieldset>

  <button class="btn" type="submit">Register Student</button>
  <p style="margin-top:16px;color:var(--mut);font-size:.9rem">Already have an account? <a href="login.php">Login</a></p>
</form>
</div>
</div>
</body>
</html>
