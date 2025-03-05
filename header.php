<?php 

function getPageName() {
    $url = $_SERVER['REQUEST_URI'];

    if (preg_match('/\/([a-zA-Z0-9_-]+)\.php/', $url, $matches)) {
        return $matches[1];
    }
    return null;
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
                <li><a href="dashboard.php" id="<?php echo (getPageName() == 'dashboard') ? 'current' : ''; ?>">Dashboard</a></li>

                    <li><a href="recettes.php" id="<?php echo (getPageName() == 'recettes') ? 'current' : ''; ?>">Recettes</a></li>
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
</header>