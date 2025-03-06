<?php
session_start();

include('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

$color_mode = $user['mode'];

$profile_photo = $user['profile_photo'] ?? 'images\nyquit1.jpg'; // Photo par défaut

// Récupérer le paramètre 'trays'
$idTray = isset($_GET['trays']) ? intval($_GET['trays']) : null;

if (!$idTray) {
    die('<div class="alert alert-danger">Bac introuvable.</div>');
}

// Requête SQL pour récupérer les informations du bac
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

// Si aucun bac n'est trouvé, afficher un message
if (!$bac) {
    die('<div class="alert alert-warning">Aucune information disponible pour ce bac.</div>');
}

// Requête pour récupérer les données d'irrigation des dernières 24 heures
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
$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les informations des capteurs avec leurs dernières valeurs
$sqlSensorsWithData = "SELECT sensor.idSensor, sensor.type, sensor.unit, sensor.freq, data.value
                       FROM sensor
                       LEFT JOIN data ON sensor.idSensor = data.idSensor
                       WHERE sensor.idTray = :idTray 
                       ORDER BY sensor.type ASC";
$stmtSensorsWithData = $pdo_optiplant->prepare($sqlSensorsWithData);
$stmtSensorsWithData->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmtSensorsWithData->execute();
$sensorsWithData = $stmtSensorsWithData->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer le seuil d'humidité
$sqlhumidityThreshold = "SELECT MIN(humidityThreshold) AS minHumidityThreshold
    FROM PLANTS inner join RECIPES on PLANTS.idPlant = RECIPES.idPlant
	INNER JOIN TRAYS
	ON TRAYS.idPlant = PLANTS.idPlant
	WHERE TRAYS.idTray = :idTray";
$stmthumidityThreshold = $pdo_optiplant->prepare($sqlhumidityThreshold);
$stmthumidityThreshold->bindParam(':idTray', $idTray, PDO::PARAM_INT);
$stmthumidityThreshold->execute();
$humidityThreshold = $stmthumidityThreshold->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Information sur le bac</title>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="/css/bootstrap.css" rel="stylesheet">
    <script src="https://cdn.plot.ly/plotly-3.0.0.min.js" charset="utf-8"></script>
    <script src="/RGraph/libraries/RGraph.common.core.js"></script>
    <script src="/RGraph/libraries/RGraph.common.dynamic.js"></script>
    <script src="/RGraph/libraries/RGraph.thermometer.js"></script>
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
    <?php include("header.php");?>
    <!-- Navigation des onglets -->
    <div class="tab-content mt-3">
        <div class="bouton-centre-header">
            <a name="" id="tab1-btn" class="btn btn-primary" href="#tab1" role="button" data-toggle="tab">Photos</a>
            <a name="" id="tab2-btn" class="btn btn-primary" href="#tab2" role="button" data-toggle="tab">Summary</a>
            <a name="" id="tab3-btn" class="btn btn-primary" href="#tab3" role="button" data-toggle="tab">Datas</a>
        </div>
    </div>

    <!-- Contenu des onglets -->
    <div class="tab-content mt-3">
        <!-- Onglet 1 -->
        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <div class="container">
                <p>Contenu de l'onglet Photos</p>
            </div>
        </div>

        <!-- Onglet 2 -->
        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <h4>Nom de la plante :</h4>
                            <?php if (!empty($bac['plantName'])): ?>
                                <p><?php echo htmlspecialchars($bac['plantName']); ?></p>
                            <?php else: ?>
                                <h4>Nom de la plante :</h4>
                                <p class="text-muted">Plante non spécifiée pour ce bac.</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4>Période actuelle de la plante :</h4>
                            <?php if (!empty($bac['periodName'])): ?>
                                <p><?php echo htmlspecialchars($bac['periodName']); ?></p>
                            <?php else: ?>
                                <h4>Période actuelle de la plante :</h4>
                                <p class="text-muted">Période non spécifiée pour cette plante.</p>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-11"">
                                <?php
                                switch ($bac['periodName']) {
                                    case 'Semis':?>
                                        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                                        <dotlottie-player src="https://lottie.host/f3d3a4f4-e71a-4086-a12e-3269501f3ae3/04VPaSqE4w.lottie" background="transparent" speed="1" style="width: 450px; 450px;" loop autoplay></dotlottie-player>
                                        <?php
                                        break;

                                    case 'Developpement des racines' :
                                        ?>
                                        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                                        <dotlottie-player src="https://lottie.host/a8ea48f4-8774-43f0-9ba0-890ac1dda071/hnxGZo4ZyZ.lottie" background="transparent" speed="1" style="width: 450px; height: 450px" loop autoplay></dotlottie-player>
                                        <?php
                                        break;

                                    case 'Croissance végétative' :
                                        ?>
                                        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                                        <dotlottie-player src="https://lottie.host/935c3d58-6436-4629-92b0-aeda99cd32d9/KjGXI3aTlY.lottie" background="transparent" speed="1" style="width: 450px; height: 450px" loop autoplay></dotlottie-player>
                                        <?php
                                        break;

                                    case 'Floraison et fructification' :
                                        ?>
                                        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                                        <dotlottie-player src="https://lottie.host/ff40091b-80c4-4b5c-b1e6-204167316c10/sukIg9lbjv.lottie" background="transparent" speed="1" style="width: 450px; height: 450px" loop autoplay></dotlottie-player>
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <div class="col-md-1">
                                <div id='humDiv'></div>
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <canvas id="cvs" width="100" height="250"></canvas>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                </div>
            </div>
        </div>


        <!-- Onglet 3 -->
        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <h5 class="card-title">Données d'Irrigation</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date et Heure</th>
                                    <th>Recette</th>
                                    <th>Âge (en heures)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($irrigations)): ?>
                                    <?php foreach ($irrigations as $index => $irrigation): ?>
                                        <tr class="irrigation-row <?php echo $index >= 5 ? 'd-none' : ''; ?>" id="row-<?php echo $index; ?>">
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
                            <?php if (count($irrigations) > 5): ?>
                                <button class="btn btn-link mt-2" id="toggle-irrigations" data-showing="5">Voir toutes les irriguations</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Données des Capteurs</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Unité</th>
                                <th>Fréquence</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($sensorsWithData)): ?>
                                <?php foreach ($sensorsWithData as $sensor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sensor['type']); ?></td>
                                        <td>
                                            <?php echo isset($sensor['value']) ? htmlspecialchars($sensor['value']) : '<span class="text-muted">N/A</span>'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($sensor['unit']); ?></td>
                                        <td><?php echo htmlspecialchars($sensor['freq']); ?> secondes</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Aucune donnée capteur disponible pour ce bac</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Dernières Alertes</h5>
                                <ul class="mb-0">
                                    <?php if (!empty($alerts)): ?>
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

    <!-- Footer -->
    <footer class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <a href="dashboard.php" class="btn btn-primary" role="button">⬅ Retour au tableau de bord</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('toggle-irrigations');
            if (button) {
                button.addEventListener('click', function () {
                    const rows = document.querySelectorAll('.irrigation-row');
                    const isShowingAll = button.innerText === 'Voir toutes les irriguations';

                    if (isShowingAll) {
                        rows.forEach(row => row.classList.remove('d-none')); // Afficher toutes les lignes
                        button.innerText = 'Voir moins'; // Mise à jour du bouton
                    } else {
                        rows.forEach((row, index) => {
                            row.classList.toggle('d-none', index >= 5); // Masquer les lignes au-delà des 5 premières
                        });
                        button.innerText = 'Voir toutes les irriguations'; // Mise à jour du bouton
                    }
                });
            }
        });

        // JavaScript pour gérer l'affichage des onglets
        document.addEventListener('DOMContentLoaded', function() {
            // Masquer tous les onglets sauf le premier
            document.querySelectorAll('.tab-pane').forEach(function(tab) {
                if (!tab.classList.contains('active')) {
                    tab.style.display = 'none';
                }
            });

            // Gérer le clic sur les boutons
            document.querySelectorAll('[data-toggle="tab"]').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var target = this.getAttribute('href');

                    // Masquer tous les onglets
                    document.querySelectorAll('.tab-pane').forEach(function(tab) {
                        tab.style.display = 'none';
                        tab.classList.remove('show', 'active');
                    });

                    // Afficher l'onglet cible
                    document.querySelector(target).style.display = 'block';
                    document.querySelector(target).classList.add('show', 'active');
                });
            });
        });
        const humidityData = <?php echo json_encode(!empty($sensorsWithData) ? $sensorsWithData[0]['value'] : null); ?>;
        const temperatureData = <?php echo json_encode(!empty($sensorsWithData) ? $sensorsWithData[2]['value'] : null); ?>;
        const humidityThreshold = <?php echo json_encode(!empty($humidityThreshold) ? $humidityThreshold[0]['minHumidityThreshold'] : null); ?>;
        var data = [
            {
                domain: { x: [0, 1], y: [0, 1] },
                value: humidityData,
                number: { suffix: "%" },
                title: { text: "Humidité" },
                type: "indicator",
                mode: "gauge+number",
                gauge: {
                    axis: { range: [0, 100] },
                    steps: [
                        { range: [0, humidityThreshold], color: "lightgray" },
                    ],
                    threshold: {
                        line: { color: "red", width: 4 },
                        thickness: 0.75,
                        value: 90
                    }
                }
            }
        ];

        var layout = { width: 450, height: 450, margin: { t: 0, b: 0 } };
        Plotly.newPlot('humDiv', data, layout);

        new RGraph.Thermometer({
        id: 'cvs',
        min: 0,
        max: 100,
        value: temperatureData,
        }).draw();


    </script>
</body>

</html>