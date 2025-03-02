<?php


$host = 'localhost';
$db_users = 'compte_utilisateur';
$db_optiplant = 'optiplant';
$user = 'root';
$pass = '';
$port = '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db_users;charset=$charset";
$dsn_optiplant = "mysql:host=$host;port=$port;dbname=$db_optiplant;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

try {
     $pdo_optiplant = new PDO($dsn_optiplant, $user, $pass, $options);
     $pdo_optiplant->exec("SET time_zone = '+01:00'");
} catch (\PDOException $e) {
     die('<div class="alert alert-danger text-center" role="alert">Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>