<?php
session_start();

include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Récupération du paramètre 'trays'
$idTray = isset($_GET['trays']) ? intval($_GET['trays']) : null;

if (!$idTray) {
    die('<div class="alert alert-danger">Bac introuvable.</div>');
}

// Requête SQL pour récupérer les informations du bac correspondant
$sql = "SELECT trays.*, 
               plants.plantName AS plantName, 
               periods.name AS periodName
        FROM trays 
        LEFT JOIN plants ON trays.idPlant = plants.idPlant 
        LEFT JOIN recipes ON plants.idPlant = recipes.idPlant
        LEFT JOIN periods ON recipes.idPeriod = periods.idPeriod
        WHERE trays.idTray = :idTray";
$stmt = $pdo_optiplant->prepare($sql);
$stmt->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmt->execute();
$bac = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun bac n’est trouvé, afficher un message
if (!$bac) {
    die('<div class="alert alert-warning">Aucune information disponible pour ce bac.</div>');
}

// Requête pour récupérer les données d'irrigation des dernières 24 heures liées à ce bac
$sqlIrrigation = "SELECT *, TIMESTAMPDIFF(HOUR, dateTime, NOW()) AS hoursAgo 
                  FROM irrigation 
                  WHERE idTray = :idTray AND dateTime >= NOW() - INTERVAL 24 HOUR 
                  ORDER BY dateTime DESC";
$stmtIrrigation = $pdo_optiplant->prepare($sqlIrrigation);

$stmtIrrigation->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmtIrrigation->execute();
$irrigations = $stmtIrrigation->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les alertes spécifiques au bac
$sql = "SELECT message, dateTime FROM alerts WHERE idTray = :idTray ORDER BY dateTime DESC LIMIT 5";
$stmt = $pdo_optiplant->prepare($sql);
$stmt->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmt->execute();
// Récupérer les alertes
$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les informations des capteurs associés au bac
$sqlSensors = "SELECT type, unit, freq 
               FROM sensor 
               WHERE idTray = :idTray 
               ORDER BY type ASC";
$stmtSensors = $pdo_optiplant->prepare($sqlSensors);
$stmtSensors->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmtSensors->execute();
$sensors = $stmtSensors->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Information sur le bac</title>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="/css/bootstrap.css" rel="stylesheet">
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

<!-- Navigation des onglets -->
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Photos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="true">Summary</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="true">Datas</a>
    </li>
</ul>

<!-- Contenu des onglets -->
<div class="tab-content mt-3">
    <!-- Onglet 1 -->
    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
        <!-- Contenu spécifique Onglet Photos -->
    </div>

    <!-- Onglet 2 -->
    <div class="tab-pane fade show" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
        <!-- Utilisation d'une grille Bootstrap avec deux colonnes -->
        <div class="row align-items-center">
            <!-- Colonne pour l'image -->
            <div class="col-md-6 text-center">
                <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                <dotlottie-player src="https://lottie.host/f3d3a4f4-e71a-4086-a12e-3269501f3ae3/04VPaSqE4w.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
            </div>

            <!-- Colonne pour les informations texte -->
            <div class="col-md-6">
                <div>
                    <!-- Nom de la plante -->
                    <?php if (!empty($bac['plantName'])): ?>
                        <h3>Nom de la plante :</h3>
                        <p><?php echo htmlspecialchars($bac['plantName']); ?></p>
                    <?php else: ?>
                        <h3>Nom de la plante :</h3>
                        <p class="text-muted">Plante non spécifiée pour ce bac.</p>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <!-- Période de la plante -->
                    <?php if (!empty($bac['periodName'])): ?>
                        <h3>État actuel de la plante :</h3>
                        <p><?php echo htmlspecialchars($bac['periodName']); ?></p>
                    <?php else: ?>
                        <h3>État actuel de la plante :</h3>
                        <p class="text-muted">Période non spécifiée pour cette plante.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglet 3 -->
    <!-- Ajouter les données des capteurs dans l'onglet Datas -->
    <div class="tab-pane fade show" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
        <div class="container py-4">
            <!-- Section pour les données d'irrigation -->
            <div class="row">
                <div class="col-4">
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
                            <?php foreach ($irrigations as $index => $irrigation): ?>
                                <tr id="row-<?php echo $index; ?>" onclick="scrollToRow('row-<?php echo $index; ?>');">
                                    <td><?php echo htmlspecialchars($irrigation['dateTime']); ?></td>
                                    <td><?php echo htmlspecialchars($irrigation['idRecipe']); ?></td>
                                    <td>
                                        <?php
                                        $hoursAgo = (int) $irrigation['hoursAgo'];
                                        echo ($hoursAgo === 0) ? 'il y a moins d\'une heure' : "il y a {$hoursAgo} heures";
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Aucune donnée trouvée pour les dernières 24 heures</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section pour les données des capteurs -->
            <div class="row mt-4">
                <div class="col-8">
                    <h4 class="text-center">Données des Capteurs</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Unité</th>
                            <th>Fréquence</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($sensors)): ?>
                            <?php foreach ($sensors as $sensor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sensor['type']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['unit']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['freq']); ?> secondes</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Aucune donnée capteur disponible pour ce bac</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section des alertes -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Dernières Alertes</h5>
                            <ul class="mb-0">
                                <?php if (!empty($alerts)): ?>
                                    <!-- Affichage des 3 premières alertes -->
                                    <?php foreach (array_slice($alerts, 0, 3) as $alert): ?>
                                        <li class="alert-item">
                                            <?php echo htmlspecialchars($alert['message']); ?>
                                            <small class="text-muted">(<?php echo date('d/m/Y H:i', strtotime($alert['dateTime'])); ?>)</small>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucune alerte pour ce bac pour le moment.</p>
                                <?php endif; ?>
                            </ul>

                            <!-- Contenu caché pour les alertes restantes -->
                            <?php if (count($alerts) > 3): ?>
                                <div class="collapse" id="allAlerts">
                                    <ul class="mt-2">
                                        <?php foreach (array_slice($alerts, 3) as $alert): ?>
                                            <li class="alert-item">
                                                <?php echo htmlspecialchars($alert['message']); ?>
                                                <small class="text-muted">(<?php echo date('d/m/Y H:i', strtotime($alert['dateTime'])); ?>)</small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <!-- Bouton pour dérouler les alertes -->
                                <button class="btn btn-link mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#allAlerts" aria-expanded="false" aria-controls="allAlerts">
                                    <span>Voir tout</span> <span style="font-weight: bold; font-size: 1.2em;">+</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-left">
                <a href="dashboard.php" class="btn btn-back">⬅ Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</footer>

<script src="js/index.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
