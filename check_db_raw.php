<?php
$dsn = 'mysql:host=127.0.0.1;dbname=admission_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $stmt = $pdo->query("SELECT * FROM users WHERE email = 'emmanuelocheme86@gmail.com'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "FOUND RAW: " . $user['id'] . " - " . $user['name'] . " - " . $user['email'] . "\n";
    } else {
        echo "NOT FOUND RAW\n";
    }
    
    $stmt = $pdo->query("SELECT count(*) FROM users");
    echo "Total Users RAW: " . $stmt->fetchColumn() . "\n";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
