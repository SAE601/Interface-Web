<?php
session_start();
require_once("config.php");

// Vérifie si l'utilisateur est connecté
require_once("utils.php");
checkAndRedirect();

// Gestion des utilisateurs
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

$profile_photo = $user['profile_photo'] ?? 'images/nyquit1.jpg'; // Photo par défaut

// Vérification des paramètres GET pour détecter un changement de mode
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];

    // Mettre à jour la valeur du mode dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET mode = :mode WHERE id = :id");
    $stmt->bindParam(':mode', $mode, PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Recharger la page après la mise à jour pour appliquer le nouveau mode
    header("Location: livestream.php");
    exit;
}

// Récupérer l'adresse IP du serveur
$server_ip = $_SERVER['SERVER_ADDR'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestream</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <!-- Styles personnalisés -->
    <?php
        // Prendre en compte le mode de couleur de l'utilisateur
        linkModeStyle();
    ?>
</head>
<body>
    <!-- En-tête -->
    <?php include("header.php"); ?>
    <script>
        document.querySelectorAll('button[data-mode]').forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                window.location.href = `livestream.php?mode=${mode}`;
            });
        });
    </script>

    <!-- Contenu principal -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="text-center mb-4">Flux vidéo en direct</h1>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="videoElement" class="embed-responsive-item" controls autoplay>
                        <source src="http://<?php echo $server_ip; ?>:5000/livestream/stream/video.m3u8" type="application/x-mpegURL">
                    </video>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        var serverIp = "<?php echo $server_ip; ?>";

        if (Hls.isSupported()) {
            var video = document.getElementById('videoElement');
            var hls = new Hls();
            hls.loadSource('http://' + serverIp + ':5000/livestream/stream/video.m3u8');
            hls.attachMedia(video);
        } else {
            console.error('HLS n\'est pas supporté par ce navigateur.');
        }
    </script>
</body>
</html>