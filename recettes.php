<?php
session_start();

include('config.php');

// Vérifie si l'utilisateur est connecté
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


try {
    // Requête pour récupérer les recettes avec les informations des tables groupe, periode et irrigation
    $sql = "
        SELECT 
            r.*, 
            i.idTray 
        FROM 
            recipes r
        INNER JOIN periods p ON r.idPeriod = p.idPeriod
        LEFT JOIN irrigation i ON r.idRecipe = i.idRecipe  -- LEFT JOIN pour inclure toutes les recettes
        ORDER BY r.idRecipe ASC  -- Trie les recettes par idRecette
    ";


    $stmt = $pdo_optiplant->prepare($sql);
    $stmt->execute();
    $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<div class="alert alert-danger text-center" role="alert">Erreur lors de la récupération des données : ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Recettes</title>
    <!-- Intégration de Bootstrap CSS -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_enfant.css">
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
    }  elseif ($user['mode'] == 'enfant') {
        echo '<link rel="stylesheet" href="css/style_enfant.css">';
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
<div class="container py-4">

    <!-- Bouton Retour -->
    <div class="row mb-4">
        <div class="col-12 text-left">
            <a href="dashboard.php" class="btn btn-back">⬅ Retour au tableau de bord</a>
        </div>
    </div>

    <!-- Titre Principal -->
    <div class="row">
        <div class="col-12">
            <h1 class="page-title text-center">Les Recettes</h1>
        </div>
    </div>

    <!-- Affichage principal des recettes -->
    <div class="row recipes-container">
        <?php if (empty($recettes)): ?>
            <!-- Si aucune recette n'est trouvée -->
            <div class="col-12 text-center">
                <div class="alert alert-warning">Aucune recette n'est disponible pour le moment.</div>
            </div>
        <?php else: ?>
            <?php foreach ($recettes as $recette): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <!-- Titre de la Recette -->
                            <h5 class="card-title">
                                Recette  : N°<?= isset($recette['idRecipe']) ? htmlspecialchars($recette['idRecipe']) : 'Non disponible' ?>
                            </h5>

                            <!-- Description de la Recette -->
                            <p class="card-text">
                                <?= isset($recette['description']) ? nl2br(htmlspecialchars($recette['description'])) : '...'; ?>
                            </p>

                            <!-- Informations liées au groupe et à la période -->
                            <p>
                                <strong>Groupe : </strong>
                                <?= isset($recette['nomGroupe']) ? htmlspecialchars($recette['nomGroupe']) : 'Non défini'; ?><br>
                                <strong>Période : </strong>
                                <?= isset($recette['nomPeriode']) ? htmlspecialchars($recette['nomPeriode']) : 'Non défini'; ?><br>
                                <strong>Bac associé :</strong> Bac
                                <?= isset($recette['idTray']) ? htmlspecialchars($recette['idTray']) : 'Non défini'; ?>
                            </p>

                            <!-- Informations Complètes -->
                            <p>
                                <strong>Arrosage : </strong>
                                <?= isset($recette['watering']) ? htmlspecialchars($recette['watering']) : 'N/A'; ?><br>
                                <strong>Arrosage du jour : </strong>
                                <?= isset($recette['dailyWatering']) ? htmlspecialchars($recette['dailyWatering']) : 'Non définie'; ?><br>
                                <strong>Fréquence : </strong>
                                <?= isset($recette['daily']) && $recette['daily'] ? 'Quotidien' : 'Non quotidien'; ?><br>
                                <strong>Azote : </strong>
                                <?= isset($recette['nitrogen']) ? htmlspecialchars($recette['nitrogen']) : 'Non définie'; ?><br>
                                <strong>Phosphore : </strong>
                                <?= isset($recette['phosphorus']) ? htmlspecialchars($recette['phosphorus']) : 'Non définie'; ?><br>
                                <strong>Potassium : </strong>
                                <?= isset($recette['potassium']) ? htmlspecialchars($recette['potassium']) : 'Non définie'; ?><br>
                                <strong>Seuil d'humidité : </strong>
                                <?= isset($recette['humidityThreshold']) ? htmlspecialchars($recette['humidityThreshold']) : 'Non définie'; ?>
                            </p>

                            <!-- Bouton En savoir plus -->
                            <?php if (isset($recette['idTray']) && !empty($recette['idTray'])): ?>
                                <!-- Si un bac est associé -->
                                <a href="essai.php?trays=<?= isset($recette['idTray']) ? htmlspecialchars($recette['idTray']) : '' ?>"
                                   class="btn btn-en-savoir-plus mt-auto">
                                    En savoir plus sur le bac <?= htmlspecialchars($recette['idTray']) ?>
                                </a>
                            <?php else: ?>
                                <!-- Si aucun bac n'est associé -->
                                <p class="text-muted">Aucun bac associé pour cette recette.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script src="/js/bootstrap.js"></script>
</body>
</html>
