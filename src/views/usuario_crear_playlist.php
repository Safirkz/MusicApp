<?php
session_start();

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../partials/header.php");
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Crear Playlist 🎵
        </h2>
        <p class="text-muted">
            Crea una playlist personalizada para organizar tu música.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a
            href="usuario_playlists.php"
            class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form
                    action="../controllers/procesar_playlist.php"
                    method="POST"
                    enctype="multipart/form-data">
                    <!-- Acción -->
                    <input
                        type="hidden"
                        name="accion"
                        value="crear">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">
                            Nombre de la Playlist
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            maxlength="100"
                            required>
                    </div>
                    <!-- Imagen -->
                    <div class="mb-4">
                        <label class="form-label">
                            Imagen de la Playlist
                        </label>
                        <input
                            type="file"
                            name="imagen"
                            class="form-control"
                            accept="image/*"
                            required>
                    </div>
                    <!-- Botón -->
                    <button
                        type="submit"
                        class="btn btn-success w-100">
                        Crear Playlist
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include("../partials/footer.php"); ?>