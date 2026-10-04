<?php
// ---- InfinityFree database settings ----
const DB_DSN  = 'mysql:host=sql301.infinityfree.com;dbname=if0_43083478_student_registration;charset=utf8mb4';
const DB_USER = 'if0_43083478';
const DB_PASS = '67Z7jMjajf';

// Admin login now checks the `admins` database table instead of a fixed
// password here. Use admin/create_admin.php once to create your admin
// account, then delete that file (see README.md).

function db(): PDO {
    static $pdo;
    return $pdo ??= new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function h(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

const COURSES = ['DAE Software Engineering', 'Computer Science', 'Information Technology', 'Web Development', 'Graphic Designing', 'Other'];
const CITIES  = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Multan', 'Peshawar', 'Quetta', 'Hyderabad', 'Other'];

/** Generates the next registration number like STU-2026-001 */
function next_registration_number(): string {
    $year = date('Y');
    $stmt = db()->prepare("SELECT registration_number FROM students WHERE registration_number LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute(["STU-$year-%"]);
    $last = $stmt->fetchColumn();
    $seq = $last ? ((int)substr($last, -3)) + 1 : 1;
    return sprintf('STU-%s-%03d', $year, $seq);
}
