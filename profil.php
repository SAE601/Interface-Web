<?php
session_start();
require_once("config.php");

// Vérifie si l'utilisateur est connecté
require_once("utils.php");
checkAndRedirect();

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();


$color_mode = $user['mode'];

$profile_photo = $user['profile_photo'] ?? 'images\nyquit1.jpg'; // Photo par défaut
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/promote_script.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <?php
        // Prendre en compte le mode de couleur de l'utilisateur
        linkModeStyle();
    ?>
</head>

<body>

    <?php include("header.php");?>
    <!-- Script de la modal -->
    <script>
        document.querySelectorAll('button[data-mode]').forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                window.location.href = `profil.php?mode=${mode}`;
            });
        });
    </script>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class=\"dashboard-container\" ><p>" . $_SESSION['message'] . "</p></div>";
        $_SESSION['message'] = NULL;
    }
    ?>

    <div class="dashboard-container">
        <h2>Paramètres</h2>
        <p>Vous pouvez gérer votre profil et modifier les paramètres de mot de passe de photo de profil etc.</p>
        <a class="btn logout-btn" href="logout.php" role="button">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
        <a class="btn btn-primary" href="dashboard.php" role="button">
            <i class="fa-solid fa-arrow-left"></i> Retour au Tableau de bord
        </a>
    </div>

    <div class="dashboard-container" style='text-align: center'>
        <h2>Photo de profil</h2>
        <!-- Afficher la photo de profil de l'utilisateur -->
        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Photo de profil" class="profile-photo">
        <!-- Bouton pour modifier la photo -->
        <form style="padding-top: 20px;" action="modifier_photo.php" method="GET">
            <button type="submit" class="modify-button">Modifier</button>
        </form>
    </div>

    <style>
        .profile-photo {
            width: 150px;
            /* Taille souhaitée */
            height: 150px;
            /* Taille souhaitée */
            border-radius: 50%;
            /* Effet cercle */
            object-fit: cover;
            /* Rognage contrôlé */
            object-position: top;
            /* Centre l'image */
            border: 3px solid #ccc;
            /* Bordure optionnelle */
        }
    </style>

    <div class="dashboard-container">
        <h2>Informations du compte </h2>
        <?php

        try {
            // Connect to the database
            $bdd = $pdo;
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bdd->exec("set names utf8");
            // Get the user ID from the session
            $id = $_SESSION['user_id'];

            // Prepare the SQL query with a placeholder
            $stmt = $bdd->prepare("SELECT username, email, lerole FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo "Nom de l'utilisateur : " . $user['username'] . "<br/>";
                echo "Adresse email : " . $user['email'] . "<br/>";
                echo "Rôle : " .  $user['lerole'] . "<br/>";
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

    <div class="dashboard-container">
        <h2>Changer le mode d'affichage</h2>
        <form id="modeForm">
            <label for="mode">Sélectionnez un mode :</label>
            <select name="mode" id="mode" required>
                <?php
                // Définir la valeur de mode (par défaut si non définie)
                $mode = isset($color_mode) ? $color_mode : 'defaut';
                ?>
                <option value="defaut" <?php echo ($mode === 'defaut') ? 'selected' : ''; ?>>Défaut</option>
                <option value="deuteranopie" <?php echo ($mode === 'deuteranopie') ? 'selected' : ''; ?>>Deutéranopie</option>
                <option value="tritanopie" <?php echo ($mode === 'tritanopie') ? 'selected' : ''; ?>>Tritanopie</option>
                <option value="protanopie" <?php echo ($mode === 'protanopie') ? 'selected' : ''; ?>>Protanopie</option>
                <option value="achromatopsie" <?php echo ($mode === 'achromatopsie') ? 'selected' : ''; ?>>Achromatopsie</option>
                <option value="contrast" <?php echo ($mode === 'contrast') ? 'selected' : ''; ?>>Contraste</option>
                <option value="darkside" <?php echo ($mode === 'darkside') ? 'selected' : ''; ?>>Darkside</option>
            </select>
        </form>

        <!-- JavaScript pour soumettre automatiquement le formulaire -->
        <script>
            document.getElementById('mode').addEventListener('change', function() {
                const mode = this.value;
                window.location.href = `profil.php?mode=${mode}`;
            });
        </script>
    </div>

    <?php if ($user && $user['lerole'] === 'admin'): ?>
        <div class="dashboard-container">
            <h2>Section Administrateur</h2>
            <p>Cette section est uniquement accessible aux administrateurs.</p>
            <!-- Liste des utilisateurs -->
            <h3>Liste des utilisateurs</h3>
            <?php
            try {
                // Récupérer tous les utilisateurs depuis la base de données
                $stmt = $pdo->prepare("SELECT id, username, email, lerole, last_login, profile_photo FROM users");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($users) {
                    echo "<table class='table table-striped table-dark'>";
                    echo "<thead><tr><th>Photo de profil</th><th>Nom d'utilisateur</th><th>Dernière connexion</th><th>Rôle</th><th>Action</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($users as $user) {
                        echo "<tr>";
                        // Afficher la photo de profil
                        $profile_photo = $user['profile_photo'] ?? 'images/nyquit1.jpg'; // Photo par défaut
                        echo "<td><img src='" . htmlspecialchars($profile_photo) . "' alt='Photo de profil' class='profile-photo' style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover;'></td>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        // Afficher la date et l'heure de dernière connexion si elle n'est pas NULL
                        echo "<td>";
                        if ($user['last_login'] !== null) {
                            echo htmlspecialchars($user['last_login']);
                        } else {
                            echo ""; // Affiche rien si last_login est NULL
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($user['lerole']) . "</td>";
                        // Ajouter un bouton "Promouvoir" uniquement pour les membres
                        if ($user['lerole'] === 'membre') {
                            echo "<td style='display: flex;'>
                                <form method='POST' action='promote_user.php' style='display:inline; margin-right: 20px;'>
                                    <input type='hidden' name='user_id' value='" . htmlspecialchars($user['id']) . "'>
                                    <button type='submit' class='btn btn-success'><i class='fas fa-crown'></i> Promouvoir</button>
                                </form>
                            </td>";
                        } else {
                            echo "<td></td>"; // Pas de bouton pour les autres rôles
                        }
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>Aucun utilisateur trouvé.</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Erreur lors de la récupération des utilisateurs : " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    <?php endif; ?>
    <?php require_once("footer.php"); ?>
</body>

</html>