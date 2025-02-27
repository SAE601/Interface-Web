<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'optiplant_fillupdate';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<div class="alert alert-danger text-center" role="alert">Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()) . '</div>');
}

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


    $stmt = $pdo->prepare($sql);
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
    <style>
        body {
            background-color: #f9f9f9;
        }
        .page-title {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 30px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-title {
            font-size: 1.25rem;
            color: #007bff;
            font-weight: bold;
        }
        .card-text {
            font-size: 0.95rem;
            color: #6c757d;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        .recipes-container {
            margin-top: 30px;
            /* Espacement entre les lignes dans la grille */
            row-gap: 30px;
        }
        .btn-en-savoir-plus {
            margin-top: 20px;
            background-color: #28a745;
            color: white;
        }
        .btn-en-savoir-plus:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
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

                            <?php if (isset($recette['idTray']) && !empty($recette['idTray'])): ?>
                                <!-- Si un bac est associé -->
                                <a href="dashboard.php?tray=<?= htmlspecialchars($recette['idTray']) ?>" class="btn btn-en-savoir-plus">
                                    En savoir plus sur le Bac <?= htmlspecialchars($recette['idTray']) ?>
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
</body>
</html>