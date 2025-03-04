<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_enfant.css">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
        
    <!-- <style> img {position: fixed; bottom: -100px;left: 0; width: 100%;height: auto } </style> -->
</head>

<body>
    <header>
        
        <div class="animation-container" onclick="this.classList.toggle('active')"> 
            <dotlottie-player class="sun" src="https://lottie.host/dee62ecf-3431-498d-95a2-a345afea39bb/fXRedDkWvw.lottie" background="transparent" speed="1" style="width: 150px; height: 150px" loop autoplay></dotlottie-player>
            <dotlottie-player class="moon" src="https://lottie.host/adffd350-67c3-4f6d-90f3-a89c3df9df69/DCFnWViolJ.lottie" background="transparent" speed="1" style="width: 150px; height: 150px" loop autoplay></dotlottie-player>
        </div>
        <h1>SAE Ombrière</h1>
        <nav>
            <div class="links">
                <ul>
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Recettes</a></li>
                    <li><a href="changemdp.php">ChangerM2p</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                    <li class="profile-link"><a href="#">Profil</a></li>
                </ul>
            </div>
            <div class="menu-container">
                <span class="menu-text">Menu</span>
                <button class="hamburger" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                
                </button>
            </div>
        </nav>
        <div class="logo">
            <a href="profil.php">
                <img src="Nyquit.png" alt="image nyquit">
                <p class="logo-text">Profil</p>
            </a>
        </div>
        
    </header>

<!-- C'est le main qu'il faut changer !!!!!!! -->
    <main>
        <div class="dashboard-container">
            <h2>Bienvenue sur votre tableau de bord</h2>
            <p>Vous avez maintenant accès à votre tableau de contrôle (dashboard pour les bilingues qui sont pas claqués au TOEIC genre Valentin)</p>
            <a class="btn btn-primary" href="logout.php" role="button">Se déconnecter</a>
        </div>

        <div class="dashboard-container">
            <h2>Rubrique 1</h2>
        </div>

        <div class="dashboard-container">
            <h2>Informations du compte : </h2>
            <?php

            try {
                // Connect to the database
                $bdd = new PDO('mysql:host=localhost;port=3306;dbname=compte_utilisateur', 'root', '');
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $bdd->exec("set names utf8");

                // Get the user ID from the session
                $id = $_SESSION['user_id'];

                // Prepare the SQL query with a placeholder
                $stmt = $bdd->prepare("SELECT username, email FROM users WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                // Execute the query
                $stmt->execute();

                // Fetch the result
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    echo "Nom de l'utilisateur : " . $user['username'] . "<br>";
                    echo "Adresse Email reliée : " . $user['email'] . "<br>";
                } else {
                    echo "Aucun utilisateur trouvé avec cet ID.";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            ?>
            <br>
            <a class="btn btn-primary" href="changemdp.php" role="button">Changer de mot de passe</a>
        </div>
        
    </main>
<!-- Fin du main -->

    <footer class="footer">
        <div class="image-plant">   
            <dotlottie-player src="https://lottie.host/1097792b-4eee-4f24-a968-b00fd8fe2892/SHmD24Bfp5.lottie" background="transparent" speed="1" style="width: 300px; height: 300px; " loop autoplay></dotlottie-player>
        </div>
        <h3> &copy; 2025 Site Web SAE Ombrière. Tous droits réservés.</h3>
    </footer>

    <!-- Script pour gérer le menu déroulant -->
    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.links').classList.toggle('active');
        });
        // Permettre de cliquer aussi sur "Menu" pour ouvrir/fermer
        document.querySelector('.menu-text').addEventListener('click', function() {
            document.querySelector('.links').classList.toggle('active');
        });
    </script>
</body>

</html>