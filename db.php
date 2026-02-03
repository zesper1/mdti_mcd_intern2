<?php
$host = 'localhost';
$db   = 'sample_db';
$user = 'root';
$pass = 'P@ssw0rd@r00t';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // In production, log the error instead of echoing it
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>