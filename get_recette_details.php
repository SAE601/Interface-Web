<?php
include('config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-warning">ID de recette invalide.</div>';
    exit;
}

$recetteID = intval($_GET['id']);

try {
    $sql = "
        SELECT 
            r.*, 
            p.name AS nomPeriode,
            i.idTray 
        FROM recipes r
        INNER JOIN periods p ON r.idPeriod = p.idPeriod
        LEFT JOIN irrigation i ON r.idRecipe = i.idRecipe 
        WHERE r.idRecipe = :id
    ";

    $stmt = $pdo_optiplant->prepare($sql);
    $stmt->bindParam(':id', $recetteID, PDO::PARAM_INT);
    $stmt->execute();
    $recette = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recette) {
        echo '<div class="alert alert-danger">Recette non trouvée.</div>';
        exit;
    }

} catch (PDOException $e) {
    die('<div class="alert alert-danger text-center" role="alert">Erreur lors de la récupération des données : ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>

<h5 class="card-title">Recette : N°<?= htmlspecialchars($recette['idRecipe']) ?></h5><br>

<p>
    <strong>Période : </strong>
    <?= isset($recette['nomPeriode']) ? htmlspecialchars($recette['nomPeriode']) : 'Non défini'; ?><br>
    <strong>Bac associé :</strong> Bac
    <?= isset($recette['idTray']) ? htmlspecialchars($recette['idTray']) : 'Non défini'; ?>
</p>

<p>
    <strong>Arrosage : </strong>
    <?= isset($recette['watering']) ? htmlspecialchars($recette['watering']) : 'Non définie'; ?><br>
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