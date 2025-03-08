<?php


require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idRecipe = intval($_POST['idRecipe']);
    $idPeriod = intval($_POST['period']);
    $idPlant = $_POST['plant'];
    $watering = $_POST['watering'];
    $dailyWatering = $_POST['dailyWatering'];
    $nitrogen = $_POST['nitrogen'];
    $phosphorus = $_POST['phosphorus'];
    $potassium = $_POST['potassium'];
    $humidityThreshold = $_POST['humidity'];

    try {
        // Requête de mise à jour pour la table recipes
        if ($idRecipe){
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
        }
        else{
            $sql = "INSERT INTO recipes (
                     idPeriod, 
                     idPlant, 
                     watering, 
                     dailyWatering, 
                     nitrogen, 
                     phosphorus, 
                     potassium, 
                     humidityThreshold
                )
                VALUES (
                        :idPeriod, 
                        :idPlant, 
                        :watering, 
                        :dailyWatering, 
                        :nitrogen, 
                        :phosphorus, 
                        :potassium, 
                        :humidityThreshold
                    )";
        }

        try {
            $stmt = $pdo_optiplant->prepare($sql);
            if ($idRecipe)
                $stmt->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
            $stmt->bindParam(':idPeriod', $idPeriod);
            $stmt->bindParam(':idPlant', $idPlant);
            $stmt->bindParam(':watering', $watering);
            $stmt->bindParam(':dailyWatering', $dailyWatering);
            $stmt->bindParam(':nitrogen', $nitrogen);
            $stmt->bindParam(':phosphorus', $phosphorus);
            $stmt->bindParam(':potassium', $potassium);
            $stmt->bindParam(':humidityThreshold', $humidityThreshold);
            $result = $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        header('Location: recettes.php?success='.$result);

    } catch (PDOException $e) {
        die('<div class="alert alert-danger">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>');
    }
}