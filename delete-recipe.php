<?php

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idRecipe = intval($_POST['idRecipe']);

    try {
        $sql = "DELETE FROM recipes WHERE idRecipe = :idRecipe";
        $stmt = $pdo_optiplant->prepare($sql);
        $stmt->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
        $result = $stmt->execute();

        // Rediriger après suppression
        header('Location: recettes.php?success='.$result);
    } catch (PDOException $e) {
        die('<div class="alert alert-danger">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>');
    }
}

