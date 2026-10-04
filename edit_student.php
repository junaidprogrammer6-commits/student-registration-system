<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$active = 'students';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$s) { header('Location: students.php'); exit; }

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $cnic = trim($_POST['cnic'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $institute = trim($_POST['institute'] ?? '');
    $admission_year = trim($_POST['admission_year'] ?? '');
    $status = trim($_POST['status'] ?? 'Active');

    if ($full_name === '') $errors['full_name'] = 'Enter a valid name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email.';
    if ($city === '') $errors['city'] = 'Select a city.';
    if ($course === '') $errors['course'] = 'Select a course.';

    if (!$errors) {
        try {
            db()->prepare(
                'UPDATE students SET full_name=?, father_name=?, email=?, phone=?, dob=?, gender=?, cnic=?,
                 address=?, city=?, province=?, postal_code=?, qualification=?, course=?, institute=?,
                 admission_year=?, status=? WHERE id=?'
            )->execute([
                $full_name, $father_name, $email, $phone, $dob ?: null, $gender, $cnic,
                $address, $city, $province, $postal_code, $qualification, $course, $institute,
                $admission_year, $status, $id,
            ]);
            $success = true;
            $stmt->execute([$id]);
            $s = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors['general'] = $e->getCode() === '23000' ? 'This email is already used by another student.' : 'Something went wrong.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Student</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

<h2 style="margin-top:0">Edit Student</h2>
<?php if ($success): ?><p class="msg ok">Student information updated successfully.</p><?php endif; ?>
<?php if (!empty($errors['general'])): ?><p class="msg err"><?= h($errors['general']) ?></p><?php endif; ?>

<div class="card">
<form method="post" novalidate>
  <fieldset>
    <legend>Personal Information</legend>
    <div class="row">
      <div class="field"><label>Full Name</label><input name="full_name" value="<?= h($s['full_name']) ?>">
        <?php if (!empty($errors['full_name'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['full_name']) ?></small><?php endif; ?></div>
      <div class="field"><label>Father Name</label><input name="father_name" value="<?= h($s['father_name']) ?>"></div>
    </div>
    <div class="row">
      <div class="field"><label>Email</label><input name="email" type="email" value="<?= h($s['email']) ?>">
        <?php if (!empty($errors['email'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['email']) ?></small><?php endif; ?></div>
      <div class="field"><label>Phone</label><input name="phone" value="<?= h($s['phone']) ?>"></div>
    </div>
    <div class="row">
      <div class="field"><label>Date of Birth</label><input name="dob" type="date" value="<?= h($s['dob']) ?>"></div>
      <div class="field"><label>Gender</label>
        <select name="gender"><?php foreach (['Male', 'Female', 'Other'] as $g): ?><option <?= $s['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option><?php endforeach; ?></select>
      </div>
    </div>
    <div class="field"><label>CNIC / B-Form</label><input name="cnic" value="<?= h($s['cnic']) ?>"></div>
  </fieldset>

  <fieldset>
    <legend>Address</legend>
    <div class="field"><label>Address</label><input name="address" value="<?= h($s['address']) ?>"></div>
    <div class="row">
      <div class="field"><label>City</label>
        <select name="city"><?php foreach (CITIES as $c): ?><option <?= $s['city'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select>
        <?php if (!empty($errors['city'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['city']) ?></small><?php endif; ?></div>
      <div class="field"><label>Province</label><input name="province" value="<?= h($s['province']) ?>"></div>
    </div>
    <div class="field"><label>Postal Code</label><input name="postal_code" value="<?= h($s['postal_code']) ?>"></div>
  </fieldset>

  <fieldset>
    <legend>Academic</legend>
    <div class="row">
      <div class="field"><label>Qualification</label><input name="qualification" value="<?= h($s['qualification']) ?>"></div>
      <div class="field"><label>Course</label>
        <select name="course"><?php foreach (COURSES as $c): ?><option <?= $s['course'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select>
        <?php if (!empty($errors['course'])): ?><small class="hint" style="color:var(--err)"><?= h($errors['course']) ?></small><?php endif; ?></div>
    </div>
    <div class="row">
      <div class="field"><label>Institute</label><input name="institute" value="<?= h($s['institute']) ?>"></div>
      <div class="field"><label>Admission Year</label><input name="admission_year" value="<?= h($s['admission_year']) ?>"></div>
    </div>
  </fieldset>

  <fieldset>
    <legend>Status</legend>
    <div class="field" style="max-width:220px"><label>Account Status</label>
      <select name="status"><?php foreach (['Active', 'Inactive'] as $st): ?><option <?= $s['status'] === $st ? 'selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select>
    </div>
  </fieldset>

  <button class="btn" type="submit">Update Student</button>
  <a class="btn sec" href="students.php">Cancel</a>
</form>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
</body>
</html>
