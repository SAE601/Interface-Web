<?php
include("config.php");

$message = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $profile_photo = $_POST['profile_photo'] ?? 'images/nyquit1.jpg'; // Par défaut, utiliser nyquit1.jpg

    // Vérifier si le nom d'utilisateur existe déjà
    $sql_check_username = "SELECT id FROM users WHERE username = :username";
    $stmt_check_username = $pdo->prepare($sql_check_username);
    $stmt_check_username->execute(['username' => $username]);

    //vérifier si l'email existe
    $sql_check_email = "SELECT id FROM users WHERE email = :email";
    $stmt_check_email = $pdo->prepare($sql_check_email);
    $stmt_check_email->execute(['email' => $email]);

    if ($stmt_check_username->rowCount() > 0) {
        // Le nom d'utilisateur existe déjà
        $message = 'Ce nom d\'utilisateur est déjà utilisé.';
    } elseif ($stmt_check_email->rowCount() > 0) {
        $message = 'Cet email est déjà utilisé.';
    } else {
        // Vérifier les contraintes du mot de passe
        if (strlen($password) < 8 || !preg_match('/\d/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[^a-zA-Z\d]/', $password)) {
            $message = 'Le mot de passe doit contenir au moins une lettre, un chiffre, une majuscule et un caractère spécial';
        } elseif (preg_match('/\s/', $password)) {
            $message = 'Le mot de passe ne doit pas contenir d\'espaces';
        } else {
            // Hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer l'utilisateur dans la base de données
            $sql = "INSERT INTO users (email, username, password, profile_photo) VALUES (:email, :username, :password, :profile_photo)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'email' => $email,
                'username' => $username,
                'password' => $hashedPassword,
                'profile_photo' => $profile_photo
            ]);

            if ($result) {
                $message = 'Inscription réussie!';
                header('Location: index.php');
                exit();
            } else {
                $message = 'Erreur lors de l\'inscription.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css"> <!-- Lien vers le fichier CSS externe -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Style pour la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .photo-option {
            display: inline-block;
            margin: 10px;
            cursor: pointer;
        }

        .photo-option img {
            width: 100px;
            height: 100px;
            border: 2px solid transparent;
        }

        .photo-option img.selected {
            border-color: blue;
        }

        /* Style pour l'affichage de la photo choisie */
        .selected-photo-container {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
        }

        .selected-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
    </style>
    
    <title>Inscription</title>
</head>
<body>

<div class="login-container">
    <h2>Inscription</h2>

    <?php if (!empty($message)): ?>
        <p style="color:red"><?= $message ?></p>
    <?php endif; ?>

    <form action="register.php" method="post">
        <div>
            <label for="email">Adresse e-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <!-- Champ caché pour stocker la photo de profil sélectionnée -->
        <input type="hidden" id="profile_photo" name="profile_photo" value="images/nyquit1.jpg">

        <!-- Bouton pour ouvrir la fenêtre modale et afficher la photo choisie -->
        <div>
            <button type="button" onclick="openModal()">Choisir la photo de profil</button>
            <div class="selected-photo-container">
                <img id="selectedPhotoPreview" src="images/nyquit1.jpg" alt="Photo choisie" class="selected-photo">
            </div>
        </div>

        <div>
            <input type="submit" value="S'inscrire">
            <a class="btn" href="index.php">
                <i class="fas fa-arrow-left"></i> Page de connexion
            </a>
        </div>

    </form>
</div>

<!-- Fenêtre modale pour choisir la photo de profil -->
<div id="photoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Choisissez votre photo de profil</h3>
        <?php
        $photos = ['images/nyquit1.jpg', 'images/nyquit2.jpg', 'images/nyquit3.jpg', 'images/nyquit4.jpg', 'images/nyquit5.jpg', 'images/nyquit6.jpg', 'images/nyquit7.jpg', 'images/nyquit8.jpg'];
        foreach ($photos as $photo): ?>
            <div class="photo-option" onclick="selectPhoto('<?= $photo ?>')">
                <img src="<?= $photo ?>" alt="Photo de profil">
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Fonction pour ouvrir la fenêtre modale
    function openModal() {
        document.getElementById('photoModal').style.display = 'block';
    }

    // Fonction pour fermer la fenêtre modale
    function closeModal() {
        document.getElementById('photoModal').style.display = 'none';
    }

    // Fonction pour sélectionner une photo
    function selectPhoto(photo) {
        // Mettre à jour le champ caché avec la photo sélectionnée
        document.getElementById('profile_photo').value = photo;

        // Mettre à jour l'aperçu de la photo choisie
        document.getElementById('selectedPhotoPreview').src = photo;

        // Fermer la fenêtre modale
        closeModal();

        // Optionnel : Ajouter un feedback visuel pour la sélection
        const images = document.querySelectorAll('.photo-option img');
        images.forEach(img => img.classList.remove('selected'));
        event.target.classList.add('selected');
    }
</script>

</body>
</html>