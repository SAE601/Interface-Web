$(document).ready(function() {
    // Gérer le clic sur le bouton "Promouvoir"
    $('.btn-promote').click(function() {
        var userId = $(this).data('user-id'); // Récupérer l'ID de l'utilisateur
        var button = $(this); // Référence du bouton cliqué

        // Envoyer une requête AJAX
        $.ajax({
            url: 'promote_user.php',
            method: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // Mettre à jour l'affichage du rôle
                    button.closest('tr').find('td:nth-child(4)').text('admin');
                    button.remove(); // Supprimer le bouton après la promotion
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Erreur lors de la communication avec le serveur.');
            }
        });
    });
});