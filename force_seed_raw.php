<?php
$dsn = 'mysql:host=127.0.0.1;dbname=admission_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = 'emmanuelocheme86@gmail.com';
    $name = 'University Admin';
    $plainPass = 'Admin@universityportal';
    $hashedPass = password_hash($plainPass, PASSWORD_BCRYPT);
    $now = date('Y-m-d H:i:s');

    // Check if exists first
    $stmt = $pdo->prepare("SELECT count(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo "User already exists. Deleting...\n";
        $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
    }

    $sql = "INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $now, $hashedPass, $now, $now]);

    echo "Admin User Inserted Successfully.\n";

} catch (PDOException $e) {
    echo "Insertion failed: " . $e->getMessage();
}
