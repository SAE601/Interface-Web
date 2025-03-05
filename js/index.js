document.addEventListener('DOMContentLoaded', function () {
    usernameField = document.getElementById('username');
    passwordField = document.getElementById('password');

    // Vide les champs de saisie lorsque la page est chargée
    window.onload = function() {
        if(usernameField) {
            usernameField.value = '';
        }

        if(passwordField) {
            passwordField.value = '';
        }
    };
});