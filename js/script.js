document.addEventListener('DOMContentLoaded', function () {

    // Masquer la modale au chargement de la page
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modal').style.display = 'none';
    });

    // Ouvrir la modale
    document.getElementById('open-modal').addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById('modal').style.display = 'flex'; // Afficher la modale
    });

    // Fermer la modale
    document.getElementById('close-modal').addEventListener('click', function () {
        document.getElementById('modal').style.display = 'none'; // Masquer la modale
    });

    // Fermer la modale en cliquant en dehors
    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('modal')) {
            document.getElementById('modal').style.display = 'none'; // Masquer la modale
        }
    });

});