<?php
session_start();
require_once("config.php");

// Vérifie si l'utilisateur est connecté
require_once("utils.php");
checkAndRedirect();

// Définir les photos disponibles
$photos = ['images/nyquit1.jpg', 'images/nyquit2.jpg', 'images/nyquit3.jpg', 'images/nyquit4.jpg', 'images/nyquit5.jpg', 'images/nyquit6.jpg', 'images/nyquit7.jpg', 'images/nyquit8.jpg'];

// Vérifier si une photo a été sélectionnée
if (isset($_GET['photo'])) {
    $selected_photo = $_GET['photo'];

    // Mettre à jour la photo de profil dans la base de données
    $user_id = $_SESSION['user_id'];
    $sql = "UPDATE users SET profile_photo = :profile_photo WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['profile_photo' => $selected_photo, 'user_id' => $user_id]);

    // Rediriger vers la page profil.php
    header('Location: profil.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la photo de profil</title>
    <style>
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        .photo-item {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .photo-item:hover {
            border-color: #007bff;
        }
    </style>
    <?php
        // Prendre en compte le mode de couleur de l'utilisateur
        linkModeStyle();
    ?>
</head>
<body>
    <div class="dashboard-container">
        <h2>Choisir une nouvelle photo de profil</h2>
        <div class="photo-grid">
            <?php foreach ($photos as $photo): ?>
                <a href="change-photo.php?photo=<?php echo urlencode($photo); ?>">
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo" class="photo-item">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>