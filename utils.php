<?php
    function getPageName() {
        $url = $_SERVER['REQUEST_URI'];

        if (preg_match('/\/([^\/?]+)(?:\.php)?(?:\?.*)?$/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    function checkAndRedirect() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = getPageName();
            header('Location: index.php');
            exit;
        }
    }

    function getCurrentUserData() {
        require('config.php');
        // Prendre en compte le mode de couleur de l'utilisateur
        try {
            $id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT mode FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    function linkModeStyle() {
        // Prendre en compte le mode de couleur de l'utilisateur
        $user = getCurrentUserData();
        if($user != null) {
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
        }
    }
?>