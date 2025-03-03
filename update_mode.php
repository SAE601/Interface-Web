<?php
session_start();
include("config.php"); // Inclure la configuration de la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
    $mode = $_POST['mode'];
    $user_id = $_SESSION['user_id'];

    try {
        $bdd = $pdo ;
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->exec("set names utf8");
        // Mettre à jour le mode dans la base de données
        $stmt = $bdd->prepare("UPDATE users SET mode = :mode WHERE id = :user_id");
        $stmt->execute(['mode' => $mode, 'user_id' => $user_id]);

        // Rediriger vers la page profil avec un message de succès
        $_SESSION['message'] = "Le mode d'affichage a été mis à jour avec succès.";
        header('Location: profil.php');
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, rediriger avec un message d'erreur
        $_SESSION['error'] = "Erreur lors de la mise à jour du mode : " . $e->getMessage();
        header('Location: profil.php');
        exit();
    }
} else {
    // Rediriger si le formulaire n'a pas été soumis correctement
    header('Location: profil.php');
    exit();
}
?>