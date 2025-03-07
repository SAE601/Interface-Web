<?php 

// Include getPageName
require_once('utils.php');

// ________________________________________________
// Vérification des paramètres GET pour détécter un changement de mode
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];

    // Mettre à jour la valeur du mode dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET mode = :mode WHERE id = :id");
    $stmt->bindParam(':mode', $mode, PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Recharger la page après la mise à jour pour appliquer le nouveau mode
    header("Location: ". getPageName());
    exit;
}
?>

<style>
    #current {
        background-size: 100% 0.15em;
    }
</style>

<header>
        <div class="animation-container" role="button" tabindex="0" aria-label="Toggle animation" onclick="toggleSunAnimation()" onkeydown="handleKeyDown(event)">
            <?php
                $user = getCurrentUserData();
                if($user != null) {
                    if($user['mode'] == 'darkside') {
                        echo('<dotlottie-player id="sun-animation" class="sun" src="https://lottie.host/adffd350-67c3-4f6d-90f3-a89c3df9df69/DCFnWViolJ.lottie" background="transparent" speed="1" style="width: 120px; height: 120px" alt="Animation Soleil" loop autoplay aria-hidden="true"></dotlottie-player>');
                    }
                    else {
                        echo('<dotlottie-player id="sun-animation" class="sun" src="https://lottie.host/dee62ecf-3431-498d-95a2-a345afea39bb/fXRedDkWvw.lottie" background="transparent" speed="1" style="width: 120px; height: 120px" alt="Animation Lune" loop autoplay aria-hidden="true"></dotlottie-player>');
                    }
                }
            ?>
        </div>
        <script>
            let isPlaying = true; // L'animation démarre en mode "lecture"

            function toggleSunAnimation() {
                const sunAnimation = document.getElementById('sun-animation');

                if (isPlaying) {
                    sunAnimation.pause();
                } else {
                    sunAnimation.play();
                }

                isPlaying = !isPlaying; // On inverse l'état
            }
            function handleKeyDown(event) {
            // Vérifie si la touche pressée est "Enter" ou "Espace"
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault(); // Empêche le défilement de la page si la touche Espace est pressée
                    toggleAnimation();
                }
            }
        </script>
        <h1 class="header-title">SAE Ombrière</h1>
        <nav>
            <div class="links">
                <ul>
                    <li><a href="dashboard.php" id="<?php echo (getPageName() == 'dashboard.php' || getPageName() == 'dashboard') ? 'current' : ''; ?>">Dashboard</a></li>
                    <li><a href="recettes.php" id="<?php echo (getPageName() == 'recettes.php'|| getPageName() == 'recettes') ? 'current' : ''; ?>">Recettes</a></li>
                    <li><a href="livestream.php" id="<?php echo (getPageName() == 'livestream.php' || getPageName() == 'livestream') ? 'current' : ''; ?>">Livestream</a></li>
                    <li><a href="profil.php" id="<?php echo (getPageName() == 'profil.php' || getPageName() == 'profil') ? 'current' : ''; ?>">Profil</a></li>
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
                <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Photo de profil" class="profile-photo" aria-hidden="true">
            </a>
        </div>
        <button class="theme-button" id="open-modal">Mode <i class="bi bi-universal-access-circle"></i></button>
</header>
<!-- Modale (page volante) -->
<div id="modal" class="modal1">
    <div class="modal1-content">
        <span class="close-modal1" id="close-modal">&times;</span>
        <h2>Modes / Paramètres de couleurs</h2>

        <div class="grid">
            <button class="btn2 btn-dark" data-mode="default">Par défaut</button>
            <button class="btn2 btn-dark" data-mode="darkside">Darkside</button>
            <button class="btn2 btn-dark" data-mode="contrast">Contrasté</button>
            <button class="btn2 btn-dark" data-mode="deuteranopie">Deuteranope</button>
            <button class="btn2 btn-dark" data-mode="protanopie">Protanope</button>
            <button class="btn2 btn-dark" data-mode="tritanopie">Tritanope</button>
            <button class="btn2 btn-dark" data-mode="achromatopsie">Achromatope</button>
            
            
        </div>
    </div>
</div>
<!-- Script pour la modal -->
<script src="js\script.js" defer></script>
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
