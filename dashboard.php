<?php
session_start();

include("config.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// ________________________________________________
// Gestion des USERS

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

$profile_photo = $user['profile_photo'] ?? 'images\nyquit1.jpg'; // Photo par défaut

// ________________________________________________
// Récupérer l'heure actuelle dans le fuseau horaire 'Europe/Paris'
$queryHour = "SELECT CURRENT_TIME AS time";
$stmt = $pdo_optiplant->prepare($queryHour);
$stmt->execute();
// Résultat
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les données
$sql = "SELECT * FROM trays";
$stmt = $pdo_optiplant->prepare($sql);
$stmt->execute();
// Récupération des résultats
$bacs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour filtrer les données
$sql = "SELECT * FROM `irrigation` ORDER BY dateTime DESC;";
$stmt = $pdo_optiplant->prepare($sql);
$stmt->execute();
// Récupération des résultats
$irrigations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les 5 dernières alertes
$sql = "SELECT message, dateTime FROM alerts ORDER BY dateTime DESC LIMIT 5";
$stmt = $pdo_optiplant->prepare($sql);
$stmt->execute();
// Récupérer les alertes
$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Page d'Accueil</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <?php

    // Prendre en compte le mode de couleur de l'utilisateur
    try {
        $id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT mode FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['mode'] == 'deuteranopie') {
            echo '<link rel="stylesheet" href="css/style_deuteranopie.css">';
        } elseif ($user['mode'] == 'tritanopie') {
            echo '<link rel="stylesheet" href="css/style_tritanopie.css">';
        } elseif ($user['mode'] == 'protanopie') {
            echo '<link rel="stylesheet" href="css/style_protanopie.css">';
        } elseif ($user['mode'] == 'achromatopsie') {
            echo '<link rel="stylesheet" href="css/style_achromatopsie.css">';
        } elseif ($user['mode'] == 'contrast') {
            echo '<link rel="stylesheet" href="css/style_contrast.css">';
        } elseif ($user['mode'] == 'darkside') {
            echo '<link rel="stylesheet" href="css/style_darkside.css">';
        } else {
            echo '<link rel="stylesheet" href="css/style_defaut.css">';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    ?>
</head>

<body>
    <!-- page du haut -->
    <?php include("header.php"); ?>
    <!-- Script de la modal -->
    <script>
        document.querySelectorAll('button[data-mode]').forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                window.location.href = `dashboard.php?mode=${mode}`;
            });
        });
    </script>

    <div class="dashboard-container">
        <!-- Ligne 1 : Deux colonnes pour la météo -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div id="api-meteo-div" class="card-body">
                        <h5 class="card-title">Météo France</h5>
                        <p>
                            <span id="meteo-city"></span> - Température: <span id="meteo-temperature"></span>°C -
                            Humidité: <span id="meteo-humidity"></span>% - Conditions: <span
                                id="meteo-description"></span>
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
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>

        <!-- Ligne 3 : 4 blocs pour les bacs -->
        <div class="row text-center">
            <div class="col-md-3">
                <div class="card" href="#bacInfo1" role="button" aria-expanded="true"
                    aria-controls="bacInfo1">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Bac 1
                        </h5>
                    </div>
                    <div id="bacInfo1">
                        <a class="btn btn-dark" href="essai.php?trays=1">Info sur la BAC</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" href="#bacInfo2" role="button" aria-expanded="false"
                    aria-controls="bacInfo2">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Bac 2
                        </h5>
                    </div>
                    <div id="bacInfo2">
                        <a class="btn btn-dark" href="essai.php?trays=2">Info sur la BAC</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" href="#bacInfo3" role="button" aria-expanded="false"
                    aria-controls="bacInfo3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Bac 3
                        </h5>
                    </div>
                    <div id="bacInfo3">
                        <a class="btn btn-dark" href="essai.php?trays=3">Info sur la BAC</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" href="#bacInfo4" role="button" aria-expanded="false"
                    aria-controls="bacInfo4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Bac 4
                        </h5>
                    </div>
                    <div id="bacInfo4">
                        <a class="btn btn-dark" href="essai.php?trays=4">Infos sur la BAC</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <!-- Carte pour les recettes -->
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Les Recettes</h5>
                        <p class="card-text">Cliquez pour accéder aux recettes </p>
                        <a href="recettes.php" class="btn btn-success btn-lg shadow">Voir les Recettes</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert box -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Dernières Alertes</h5>
                        <ul class="mb-0">
                            <?php if (!empty($alerts)): ?>
                                <?php foreach ($alerts as $alert): ?>
                                    <li>
                                        <small>(<?php echo date('d/m/Y H:i', strtotime($alert['dateTime'])); ?>)</small>
                                        <?php echo htmlspecialchars($alert['message']); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune alerte pour le moment.</p>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        // Empêcher la fermeture du collapse
        document.getElementById('bacInfo1').addEventListener('hide.bs.collapse', function(event) {
            event.preventDefault(); // Empêche l'action de fermeture
            alert("Le collapse ne peut pas être fermé !");
        });
    </script>



    <!-- Intégration de Bootstrap JS -->
    <script src="js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="js/meteo_script.js"></script>

    <?php require_once("footer.php"); ?>

</body>

</html>