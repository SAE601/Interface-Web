<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'optiplant_fillupdate';
$username = 'root';
$password = '';

// Connexion à la base de données
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Requête SQL pour récupérer les données
$sql = "SELECT * FROM trays";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$bacs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour filtrer les données
$sql = "SELECT * FROM `irrigation` ORDER BY dateTime DESC;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$irrigations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour filtrer les données
$sql = "SELECT * FROM `periods`";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$periode = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Information sur le bac</title>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="/css/bootstrap.css" rel="stylesheet">
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
        <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab2" aria-selected="true">Datas</a>
    </li>
</ul>

<!-- Contenu des onglets -->
<div class="tab-content mt-3">
    <!-- Onglet 1 -->
    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">

    </div>

    <!-- Onglet 2 -->
    <div class="tab-pane fade show" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
        <dotlottie-player src="https://lottie.host/f3d3a4f4-e71a-4086-a12e-3269501f3ae3/04VPaSqE4w.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
    </div>

    <!-- Onglet 3 -->
    <div class="tab-pane fade show" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
        <div class="container py-4">
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
                                <?php if ($irrigation['idTray'] == $_GET['trays']): ?>
                                    <tr id="row-<?php echo $index; ?>" onclick="scrollToRow('row-<?php echo $index; ?>');">
                                        <td><?php echo htmlspecialchars($irrigation['dateTime']); ?></td>
                                        <td><?php echo htmlspecialchars($irrigation['idRecipe']); ?></td>
                                    </tr>
                                <?php endif; ?>
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

