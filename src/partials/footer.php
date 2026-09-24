<?php
// /partials/footer.php
?>
    </div>
    <!-- Espacio para evitar que el reproductor tape contenido -->
    <div style="height: 120px;"></div>
    <!-- Reproductor global -->
    <div class="bg-dark border-top shadow fixed-bottom py-3">
        <div class="container">
            <div class="row align-items-center">
                <!-- Información canción -->
                <div class="col-md-3 text-white mb-2 mb-md-0">
                    <div id="infoCancion">
                        <div class="fw-bold" id="tituloCancion">
                            Ninguna canción reproduciéndose
                        </div>
                        <small class="fw-bold" id="artistaCancion">
                            MusicApp
                        </small>
                    </div>
                </div>
                <!-- Reproductor -->
                <div class="col-md-9">
                    <audio
                        id="reproductorGlobal"
                        controls
                        class="w-100">
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-3 text-muted">

        <small>

            &copy; <?php echo date("Y"); ?> MusicApp - Proyecto de Bases de Datos

        </small>

    </footer>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

    // ==========================================
    // REPRODUCTOR GLOBAL
    // ==========================================

    function reproducirCancion(
        idCancion,
        audio,
        titulo,
        artista
    ) {
        const reproductor = document.getElementById('reproductorGlobal');
        const tituloCancion = document.getElementById('tituloCancion');
        const artistaCancion = document.getElementById('artistaCancion');

        // Cambiar información
        tituloCancion.textContent = titulo;
        artistaCancion.textContent = artista;

        // Cambiar audio
        reproductor.src = '../../resources/audio/' + audio;

        // Reproducir
        reproductor.play();

        // ==========================================
        // GUARDAR EN HISTORIAL
        // ==========================================

        fetch('../controllers/procesar_historial.php', {

            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },

            body: 'id_cancion=' + idCancion
        });
    }

    </script>
</body>
</html>