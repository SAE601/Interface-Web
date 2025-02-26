// Détecte l'événement de retour en arrière
window.onpageshow = function(event) {
    if (event.persisted) { // Vérifie si la page est chargée depuis le cache
        // Envoie une requête pour déconnecter l'utilisateur
        fetch('logout.php')
            .then(response => {
                // Redirige vers la page de connexion après la déconnexion
                window.location.href = 'index.php';
            });
    }
};