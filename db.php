<?php
declare(strict_types=1);

$host = '127.0.0.1';
$database = 'aurythia';
$username = 'root';
$password = '';

$pdo = new PDO(
    "mysql:host=$host;dbname=$database;charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$pdo->exec(
    "INSERT IGNORE INTO metal (idmet, nomMet) VALUES
        ('A', 'argent'),
        ('O', 'or'),
        ('P', 'platine')"
);
$pdo->exec(
    "INSERT IGNORE INTO expert (idExp, nomExp, mail) VALUES
        ('A22', 'chokri sfaxi', 'expert@aurythia.local'),
        ('A23', 'mohamed jerbi', 'expert@aurythia.local'),
        ('A24', 'radhia gafsi', 'expert@aurythia.local')"
);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
