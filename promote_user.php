<?php
    session_start();
    include("config.php") ;

//$req = "select le role du gars dont le user_id = " . $_POST['userid'];

//$req2 = "update ... set rôle = admin where user_id = " . $_POST['userid'];



    // Vérifier si l'utilisateur est un administrateur
    if (!isset($_SESSION['user_id'])) {
        sleep(2);
        header('Location: profil.php');
       // exit;
    }

    // Vérifier si l'ID de l'utilisateur à promouvoir est présent
    if (!isset($_POST['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID utilisateur manquant']);
        exit;
    }

    $id = $_POST['user_id'];

    // Connexion à la base de données
    $bdd = $pdo ;
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->exec("set names utf8");


    // Mettre à jour le rôle de l'utilisateur
    $stmt = $bdd->prepare("UPDATE users SET lerole = 'admin' WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    // Rediriger vers le dashboard.
    header('Location: profil.php');
    exit;

?>