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
            p.name AS nomPeriode,
            pl.plantName AS nomPlant,
            i.idTray
        FROM 
            recipes r
        INNER JOIN periods p ON r.idPeriod = p.idPeriod
        INNER JOIN plants pl ON r.idPlant = pl.idPlant
        LEFT JOIN irrigation i ON r.idRecipe = i.idRecipe
        ORDER BY r.idRecipe ASC;
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
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Les Recettes</title>
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

    <!-- Liste des recettes -->
    <?php foreach ($recettes as $recette): ?>
        <div class="recette-item">
            <h5>Recette N°<?= htmlspecialchars($recette['idRecipe']) ?></h5>
            <button
                    class="btn btn-details"
                    data-id="<?= htmlspecialchars($recette['idRecipe']) ?>"
                    data-period="<?= isset($recette['nomPeriode']) ? htmlspecialchars($recette['nomPeriode']) : 'Non défini'; ?>"
                    data-plant="<?= isset($recette['nomPlant']) ? htmlspecialchars($recette['nomPlant']) : 'Non défini'; ?>"
                    data-daily="<?= isset($recette['daily']) && $recette['daily'] ? 'Quotidien' : 'Non quotidien'; ?>"
                    data-idtray="<?= isset($recette['idTray']) ? htmlspecialchars($recette['idTray']) : 'Non défini'; ?>"
                    data-watering="<?= isset($recette['watering']) ? htmlspecialchars($recette['watering']) : 'Non définie'; ?>"
                    data-daily-watering="<?= isset($recette['dailyWatering']) ? htmlspecialchars($recette['dailyWatering']) : 'Non définie'; ?>"
                    data-nitrogen="<?= isset($recette['nitrogen']) ? htmlspecialchars($recette['nitrogen']) : 'Non définie'; ?>"
                    data-phosphorus="<?= isset($recette['phosphorus']) ? htmlspecialchars($recette['phosphorus']) : 'Non définie'; ?>"
                    data-potassium="<?= isset($recette['potassium']) ? htmlspecialchars($recette['potassium']) : 'Non définie'; ?>"
                    data-humidity="<?= isset($recette['humidityThreshold']) ? htmlspecialchars($recette['humidityThreshold']) : 'Non définie'; ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#detailsModal">
                Voir Détails
            </button>
        </div>
    <?php endforeach; ?>

</div>

<!-- Modal pour afficher les détails d'une recette -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- En-tête du modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Détails de la Recette</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Corps du modal -->
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">Types</th>
                            <th scope="col">Valeurs</th>
                        </tr>
                        </thead>
                        <tbody id="detailsTableBody">
                        <!-- Les lignes dynamiques s'inséreront ici -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('detailsModal');
        const tableBody = document.getElementById('detailsTableBody');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            // Récupération des données via les attributs data-*
            const idRecipe = button.getAttribute('data-id');
            const idPeriod = button.getAttribute('data-period');
            const namePlant = button.getAttribute('data-plant');
            const daily = button.getAttribute('data-daily');
            const idTray = button.getAttribute('data-idtray');
            const watering = button.getAttribute('data-watering');
            const dailyWatering = button.getAttribute('data-daily-watering');
            const nitrogen = button.getAttribute('data-nitrogen');
            const phosphorus = button.getAttribute('data-phosphorus');
            const potassium = button.getAttribute('data-potassium');
            const humidityThreshold = button.getAttribute('data-humidity');


            // Effacer les anciennes données du tableau
            tableBody.innerHTML = '';

            // Ajout dynamique des nouvelles données
            const rows = [
                { label: 'ID de la Recette', value: idRecipe },
                { label: 'Période', value: idPeriod },
                { label: 'Plante', value: namePlant },
                { label: 'Fréquence', value: daily },
                { label: 'Bac Associé', value: idTray },
                { label: 'Arrosage', value: watering },
                { label: 'Arrosage Quotidien', value: dailyWatering },
                { label: 'Azote', value: nitrogen },
                { label: 'Phosphore', value: phosphorus },
                { label: 'Potassium', value: potassium },
                { label: 'Seuil d\'humidité', value: humidityThreshold },

            ];

            rows.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${row.label}</td><td>${row.value}</td>`;
                tableBody.appendChild(tr);
            });
        });
    });
</script>
</body>
    </html>

