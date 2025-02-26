<?php

include("config.php");

session_start(); // Démarre la session

// Vérifie si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); // Redirige vers le dashboard
    exit(); // Arrête l'exécution du script
}

$message = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
    } else {
        $message = 'Identifiant ou mot de passe incorrect';
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Accueil</title>

    <style>
    .buttons-container {
        text-align: center;
        margin-top: 50px;
    }
    .btn {
        margin: 10px;
        padding: 10px 20px;
        background-color:rgb(95, 95, 96);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    .btn:hover {
        background-color:rgb(5, 60, 118);
    }
    body {  
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    }

    .login-container {
        max-width: 400px;
        margin: 100px auto;
        background-color: #fff;
        padding: 20px 30px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        margin-top: 0;
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #007BFF;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }

    p {
        color: red;
        font-weight: bold;
    }
</style>

</head>
<body>

<div class="login-container">
    <h2>Connexion</h2>

    <?php if (!empty($message)): ?>
        <p style="color:red"><?= $message ?></p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <div>
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username">
        </div>

        <div>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password">
        </div>

        <div>
            <input type="submit" value="Se connecter">
            <a href="register.php" class="btn">S'inscrire</a>
        </div>
    </form>
</div>

<!-- Inclure le fichier JavaScript -->
<script src="index.js"></script>
</body>
</html>