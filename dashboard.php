<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'optiplant';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configuration du fuseau horaire pour la session MySQL
    $pdo->exec("SET time_zone = 'Europe/Paris'");
    // Récupérer l'heure actuelle dans le fuseau horaire 'Europe/Paris'
    $sql = "SELECT NOW() AS current_time";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // Résultat
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer les données
    $sql = "SELECT * FROM plantes";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Récupération des résultats
    $plantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gestion des erreurs
    echo '<div class="alert alert-danger text-center" role="alert">Erreur lors de la connexion ou de l\'exécution de la requête : ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Page d'Accueil</title>
    <!-- Intégration de Bootstrap CSS -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
        }
        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <!-- Ligne 1 : Deux colonnes pour la météo -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div id="api-meteo-div" class="card-body">
                    <h5 class="card-title">Météo France</h5>
                    <p>
                    <span id="meteo-city"></span> - Température: <span id="meteo-temperature"></span>°C - Humidité: <span id="meteo-humidity"></span>% - Conditions: <span id="meteo-description"></span>
                    </p>
                    <p><i><?php
                            // Affiche l'heure actuelle (format: Heures:Minutes:Secondes)
                            echo "Mise à jour : " . $result['current_time'];
                            ?></i></p>
                </div>
            </div>
        </div>d
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Météo La Garde</h5>
                    <p class="card-text">Information météorologique pour La Garde.</p>
                    <p><i><?php
                            // Affiche l'heure actuelle (format: Heures:Minutes:Secondes)
                            echo "Mise à jour : " . $result['current_time'];
                            ?>
                        </i></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ligne 2 : Titre de la page -->
    <div class="row">
        <div class="col-12">
            <div class="text-center my-4">
                <h1 class="text-primary">Dashboard - Page d'Accueil</h1>
            </div>
        </div>
    </div>

    <!-- Ligne 3 : 4 blocs pour les bacs -->
    <div class="row text-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bac 1</h5>
                    <p class="card-text">Informations sur le bac 1.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bac 2</h5>
                    <p class="card-text">Informations sur le bac 2.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bac 3</h5>
                    <p class="card-text">Informations sur le bac 3.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bac 4</h5>
                    <p class="card-text">Informations sur le bac 4.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernière ligne : Infos utilisateur + bouton de déconnexion -->
    <div class="row">
        <div class="dashboard-container">
            <h2>Informations du compte : </h2>
            <?php

            try {
                // Connect to the database
                $bdd = new PDO('mysql:host=localhost;port=3306;dbname=compte_utilisateur', 'root', '');
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $bdd->exec("set names utf8");

                // Get the user ID from the session
                $id = $_SESSION['user_id'];

                // Prepare the SQL query with a placeholder
                $stmt = $bdd->prepare("SELECT username, email FROM users WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                // Execute the query
                $stmt->execute();

                // Fetch the result
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    echo "Nom de l'utilisateur : " . $user['username'] . "<br>";
                    echo "Adresse Email reliée : " . $user['email'] . "<br>";
                } else {
                    echo "Aucun utilisateur trouvé avec cet ID.";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            ?>
            <br>
            <a class="btn btn-primary" href="changemdp.php" role="button">Changer de mot de passe</a>
        </div>

    </div>
</div>


<!-- Intégration de Bootstrap JS -->
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/météo_script.js"></script>
</body>
</html>