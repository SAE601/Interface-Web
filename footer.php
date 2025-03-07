<footer class="footer">
    <div class="image-plant" role="button" tabindex="0" aria-label="Toggle animation" onclick="togglePlantAnimation()" onkeydown="handleKeyDown(event)">
        <dotlottie-player id="plant-animation" src="https://lottie.host/1097792b-4eee-4f24-a968-b00fd8fe2892/SHmD24Bfp5.lottie" background="transparent" speed="1" style="width: 200px; height: 200px; " loop autoplay aria-hidden="true"></dotlottie-player>
    </div>
    <script>
            let isPlaying = true; // L'animation démarre en mode "lecture"

            function togglePlantAnimation() {
                const plantAnimation = document.getElementById('plant-animation');
                
                if (isPlaying) {
                    plantAnimation.pause();
                } else {
                    plantAnimation.play();
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
    <h3> &copy; 2025 Site Web SAE Ombrière. Tous droits réservés.</h3>
</footer>