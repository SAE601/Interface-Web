<?php

include('config.php');
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .recette-item {
            background: #ffffff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .recette-item h5 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .btn-details {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-details:hover {
            background-color: #0056b3;
        }
    </style>

    <?php
/*
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
}*/

?>
</head>
<body>

<div class="container mt-5">
    <!-- Bouton Retour -->
    <div class="row">
        <div class="col-12">
            <a href="dashboard.php" class="btn btn-back">⬅ Retour au tableau de bord</a>
        </div>
    </div>

    <h2 class="text-center mb-4">Liste des Recettes</h2>

    <?php foreach ($recettes as $recette) : ?>
        <div class="recette-item">
            <h5>Recette N°<?= htmlspecialchars($recette['idRecipe']) ?> </h5>
            <a class="btn btn-details" data-id="<?= htmlspecialchars($recette['idRecipe']) ?>" data-bs-toggle="modal" data-bs-target="#modalRecette">
               Voir Détails
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="modalRecette" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Détails de la Recette</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                Sélectionnez une recette pour voir les détails.
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', function() {
            let recetteID = this.getAttribute('data-id');
            if (recetteID) {
                fetch('recette_details.php?id=' + recetteID)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('modalBody').innerHTML = data;
                    })
                    .catch(error => {
                        document.getElementById('modalBody').innerHTML = '<div class="alert alert-danger">Erreur de chargement des détails.</div>';
                    });
            }
        });
    });
</script>

</body>
</html>
