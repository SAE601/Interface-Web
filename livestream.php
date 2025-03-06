<?php
session_start();
include("config.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

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
    header("Location: dashboard.php");
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
    <!-- Styles personnalisés -->
    <?php
    try {
        $id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT mode FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['mode'] == 'deuteranopie') {
            echo '<link rel="stylesheet" href="css/style_deuteranopie.css">';
        } elseif ($user['mode'] == 'tritanopie') {
            echo '<link rel="stylesheet" href="css/style_tritanopie.css">';
        } elseif ($user['mode'] == 'protanopie') {
            echo '<link rel="stylesheet" href="css/style_protanopie.css">';
        } elseif ($user['mode'] == 'achromatopsie') {
            echo '<link rel="stylesheet" href="css/style_achromatopsie.css">';
        } elseif ($user['mode'] == 'contrast') {
            echo '<link rel="stylesheet" href="css/style_contrast.css">';
        } elseif ($user['mode'] == 'darkside') {
            echo '<link rel="stylesheet" href="css/style_darkside.css">';
        } else {
            echo '<link rel="stylesheet" href="css/style_defaut.css">';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</head>
<body>
    <!-- En-tête -->
    <?php include("header.php"); ?>

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