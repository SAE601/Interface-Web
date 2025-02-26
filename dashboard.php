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

// Connexion à la base de données
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Configuration du fuseau horaire pour la session MySQL
$pdo->exec("SET time_zone = '+01:00'");

// Récupérer l'heure actuelle dans le fuseau horaire 'Europe/Paris'
$queryHour = "SELECT CURRENT_TIME AS time";
$stmt = $pdo->prepare($queryHour);
$stmt->execute();

// Résultat
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les données
$sql = "SELECT * FROM bacs";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$bacs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour filtrer les données
$sql = "SELECT * FROM `irrigation` WHERE `idBac` = 2 ORDER BY dateHeure DESC;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Récupération des résultats
$irrigations = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Page d'Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
                    <p><i>
                            <?php
                            // Affiche l'heure actuelle (format: Heures:Minutes:Secondes)
                            echo "Mise à jour : " . $result['time'];
                            ?>
                        </i>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Météo La Garde</h5>
                    <p class="card-text">Information météorologique pour La Garde.</p>
                    <p><i>
                            <?php
                            // Affiche l'heure actuelle (format: Heures:Minutes:Secondes)
                            echo "Mise à jour : " . $result['time'];
                            ?>
                        </i>
                    </p>
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
            <div class="card" data-toggle="collapse" href="#bacInfo1" aria-expanded="true" aria-controls="bacInfo1">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Bac 1
                    </h5>
                </div>
                <div class="collapse" id="bacInfo1">
                    <div class="card-body">
                        <p class="card-text"><?php echo "Énergie consommée : " . $bacs[0]['energieconsommee'] . " kWh"; ?></p>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">En savoir plus</button>
                    <div class="container py-4">
                        <h4 class="text-center">Données d'Irrigation</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date et Heure</th>
                                <th>Recette</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($irrigations)): ?>
                                <?php foreach ($irrigations as $irrigation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($irrigation['dateHeure']); ?></td>
                                        <td><?php echo htmlspecialchars($irrigation['idRecette']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucune donnée trouvée</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" data-toggle="collapse" href="#bacInfo2" role="button" aria-expanded="false" aria-controls="bacInfo2">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Bac 2
                    </h5>
                </div>
                <div class="collapse" id="bacInfo2">
                    <div class="card-body">
                        <p class="card-text"><?php echo "Énergie consommée : " . $bacs[1]['energieconsommee'] . " kWh"; ?></p>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">En savoir plus</button>
                    <div class="container py-4">
                        <h4 class="text-center">Données d'Irrigation</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date et Heure</th>
                                <th>Recette</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($irrigations)): ?>
                                <?php foreach ($irrigations as $irrigation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($irrigation['dateHeure']); ?></td>
                                        <td><?php echo htmlspecialchars($irrigation['idRecette']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucune donnée trouvée</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" data-toggle="collapse" href="#bacInfo3" role="button" aria-expanded="false" aria-controls="bacInfo3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Bac 3
                    </h5>
                </div>
                <div class="collapse" id="bacInfo3">
                    <div class="card-body">
                        <p class="card-text">Informations sur le bac 3.</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">En savoir plus</button>
                    <div class="container py-4">
                        <h4 class="text-center">Données d'Irrigation</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date et Heure</th>
                                <th>Recette</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($irrigations)): ?>
                                <?php foreach ($irrigations as $irrigation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($irrigation['dateHeure']); ?></td>
                                        <td><?php echo htmlspecialchars($irrigation['idRecette']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucune donnée trouvée</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" data-toggle="collapse" href="#bacInfo4" role="button" aria-expanded="false" aria-controls="bacInfo4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Bac 4
                    </h5>
                </div>
                <div class="collapse" id="bacInfo4">
                    <div class="card-body">
                        <p class="card-text"><?php echo "Énergie consommée : " . $bacs[0]['energieconsommee'] . " kWh"; ?></p>
                    </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">En savoir plus</button>
                    <div class="container py-4">
                        <h4 class="text-center">Données d'Irrigation</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date et Heure</th>
                                <th>Recette</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($irrigations)): ?>
                                <?php foreach ($irrigations as $irrigation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($irrigation['dateHeure']); ?></td>
                                        <td><?php echo htmlspecialchars($irrigation['idRecette']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">Aucune donnée trouvée</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
<script src="js/index.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/météo_script.js"></script>
</body>
</html>