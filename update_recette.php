<?php


include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idRecipe = intval($_POST['idRecipe']);
    $idPeriod = intval($_POST['period']);
    $idPlant = $_POST['plant'];
    $idTray = $_POST['tray'];
    $watering = $_POST['watering'];
    $dailyWatering = $_POST['dailyWatering'];
    $nitrogen = $_POST['nitrogen'];
    $phosphorus = $_POST['phosphorus'];
    $potassium = $_POST['potassium'];
    $humidityThreshold = $_POST['humidity'];

    try {
        // Requête de mise à jour pour la table recipes
        $sql = "UPDATE recipes SET 
                    idPeriod = :idPeriod,
                    idPlant = :idPlant,
                    watering = :watering,
                    dailyWatering = :dailyWatering,
                    nitrogen = :nitrogen,
                    phosphorus = :phosphorus,
                    potassium = :potassium,
                    humidityThreshold = :humidityThreshold
                WHERE idRecipe = :idRecipe";

        $stmt = $pdo_optiplant->prepare($sql);
        $stmt->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
        $stmt->bindParam(':idPeriod', $idPeriod);
        $stmt->bindParam(':idPlant', $idPlant);
        $stmt->bindParam(':watering', $watering);
        $stmt->bindParam(':dailyWatering', $dailyWatering);
        $stmt->bindParam(':nitrogen', $nitrogen);
        $stmt->bindParam(':phosphorus', $phosphorus);
        $stmt->bindParam(':potassium', $potassium);
        $stmt->bindParam(':humidityThreshold', $humidityThreshold);
        $stmt->execute();

        // Ajout de la mise à jour pour la table irrigation
        $sqlIrrigation = "UPDATE irrigation SET idTray = :idTray WHERE idRecipe = :idRecipe";
        $stmtIrrigation = $pdo_optiplant->prepare($sqlIrrigation);
        $stmtIrrigation->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
        $stmtIrrigation->bindParam(':idTray', $idTray, PDO::PARAM_INT);
        $stmtIrrigation->execute();

        header('Location: recettes.php?success=1');
    } catch (PDOException $e) {
        die('<div class="alert alert-danger">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>');
    }
}