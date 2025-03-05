document.addEventListener('DOMContentLoaded', function () {
    modalElement = document.getElementById('modal');
    openModalElement = document.getElementById('open-modal');
    closeModalElement = document.getElementById('close-modal');

    if(modalElement) {
        // Masquer la modale au chargement de la page
        modalElement.style.display = 'none';

        // Ouvrir la modale
        openModalElement?.addEventListener('click', function (event) {
            event.preventDefault();
            modalElement.style.display = 'flex'; // Afficher la modale
        });

        // Fermer la modale
        closeModalElement?.addEventListener('click', function () {
            modalElement.style.display = 'none'; // Masquer la modale
        });

        // Fermer la modale en cliquant en dehors
        window.addEventListener('click', function (event) {
            if (event.target === modalElement) {
                modalElement.style.display = 'none'; // Masquer la modale
            }
        });
    }

});