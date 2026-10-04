<?php
require __DIR__ . '/../config.php';

echo "Config loaded successfully.<br>";

try {
    $pdo = db();
    echo "Database connection successful.<br>";

    $username = "junaid";
    $password = "Admin12345";
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $check->execute([$username]);

    if ($check->fetch()) {
        $st = $pdo->prepare(
            "UPDATE admins SET password_hash = ? WHERE username = ?"
        );
        $st->execute([$hash, $username]);
        echo "Admin password updated successfully.";
    } else {
        $st = $pdo->prepare(
            "INSERT INTO admins (username, password_hash) VALUES (?, ?)"
        );
        $st->execute([$username, $hash]);
        echo "Admin created successfully.";
    }
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
} catch (Throwable $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}