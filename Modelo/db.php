<?php
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'test';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Conexión MariaDB</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .success { color: green; font-size: 24px; }
    </style>
</head>
<body>
    <div class='success'> Conexión exitosa a MariaDB!</div>
</body>
</html>";
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Conexión MariaDB</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .error { color: red; font-size: 20px; }
    </style>
</head>
<body>
    <div class='error'> Error de conexión: " . htmlspecialchars($e->getMessage()) . "</div>
</body>
</html>";
}
