<?php 

function getPageName() {
    $url = $_SERVER['REQUEST_URI'];

    if (preg_match('/\/([a-zA-Z0-9_-]+)\.php/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

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
    header("Location: ". getPageName() .".php");
    exit;
}
?>

<style>
    #current {
        background-size: 100% 0.15em;
    }
</style>

<header>
        <div class="animation-container" role="button" tabindex="0" aria-label="Toggle animation" onclick="toggleAnimation()" onkeydown="handleKeyDown(event)">
        <!-- <div class="animation-container" onclick="this.classList.toggle('active')"> -->
            <dotlottie-player class="sun" src="https://lottie.host/dee62ecf-3431-498d-95a2-a345afea39bb/fXRedDkWvw.lottie" background="transparent" speed="1" style="width: 120px; height: 120px" loop autoplay aria-hidden="true"></dotlottie-player>
            <dotlottie-player class="moon" src="https://lottie.host/adffd350-67c3-4f6d-90f3-a89c3df9df69/DCFnWViolJ.lottie" background="transparent" speed="1" style="width: 120px; height: 120px" loop autoplay aria-hidden="true"></dotlottie-player>
        </div>
        <script>
            function toggleAnimation() {
                const container = document.querySelector('.animation-container');
                container.classList.toggle('active');
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
                    <li><a href="dashboard.php" id="<?php echo (getPageName() == 'dashboard' || getPageName() == 'enfant') ? 'current' : ''; ?>">Dashboard</a></li>
                    <li><a href="recettes.php" id="<?php echo (getPageName() == 'recettes') ? 'current' : ''; ?>">Recettes</a></li>
                    <li><a href="livestream.php" id="<?php echo (getPageName() == 'livestream') ? 'current' : ''; ?>">Livestream</a></li>
                    <li><a href="profil.php" id="<?php echo (getPageName() == 'profil') ? 'current' : ''; ?>">Profil</a></li>
                    <!-- <li class="profile-link"><a href="profil.php">Profil</a></li> -->
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
        <h2>Paramètres de couleurs</h2>

        <div class="grid">
            <button class="btn btn-dark" data-mode="contrast">Mode contraste élevé</button>
            <button class="btn btn-dark" data-mode="deuteranopie">Mode deuteranope</button>
            <button class="btn btn-dark" data-mode="tritanopie">Mode tritanope</button>
            <button class="btn btn-dark" data-mode="protanopie">Mode protanope</button>

            <button class="btn btn-dark" data-mode="achromatopsie">Mode achromatope</button>
            <button class="btn btn-dark" data-mode="default">Couleur par défaut</button>
            <button class="btn btn-dark" data-mode="darkside">Mode darkside</button>
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