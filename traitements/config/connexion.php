<?php
// connexion.php - Connexion PDO à la base MySQL

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

$host = 'localhost';
$dbname = 'air_maken';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
     // En développement XAMPP, on affiche l'erreur, mais on sécurise le tout
     die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
