<?php
// Configuration des informations de connexion à la base de données
$host = 'localhost'; // Hôte de la base de données
$dbname = 'optiplant'; // Nom de la base de données
$username = 'root'; // Nom d'utilisateur
$password = ''; // Mot de passe

try {
    // Création de l'objet PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configuration du mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie à la base de données";
} catch (PDOException $e) {
    // Gestion des erreurs
    die("Erreur de connexion : " . $e->getMessage());
}

// Requête pour récupérer toutes les lignes d'une table
try {
    // Écrire la requête
    $sql = "SELECT * FROM plantes";

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Récupérer les résultats
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des résultats
    foreach ($resultats as $ligne) {
        echo "Nom : " . $ligne['nomPlante'] . "<br/>";
        echo "Type : " . $ligne['typePlante'] . "<br/>";
        echo "<br/>";
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}
