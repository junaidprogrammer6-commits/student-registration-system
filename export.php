<?php
require __DIR__ . '/../config.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students-' . date('Y-m-d') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, [
    'Registration No', 'Full Name', 'Father Name', 'Email', 'Phone', 'Gender', 'CNIC',
    'Address', 'City', 'Province', 'Postal Code', 'Qualification', 'Course', 'Institute',
    'Admission Year', 'Status', 'Registered At',
]);
foreach ($students as $s) {
    fputcsv($out, [
        $s['registration_number'], $s['full_name'], $s['father_name'], $s['email'], $s['phone'], $s['gender'], $s['cnic'],
        $s['address'], $s['city'], $s['province'], $s['postal_code'], $s['qualification'], $s['course'], $s['institute'],
        $s['admission_year'], $s['status'], $s['created_at'],
    ]);
}
fclose($out);
