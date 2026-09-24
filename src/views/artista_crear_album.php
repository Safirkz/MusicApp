<?php
session_start();

// Validar que solo artistas puedan entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {

    header("Location: ../index.php");

    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// ==========================================
// OBTENER ARTISTA DEL USUARIO LOGUEADO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$sql = "EXEC sp_ObtenerArtistaPorUsuario ?";

$params = array($id_usuario);

$stmt = sqlsrv_query($conn, $sql, $params);

$artista = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Validar existencia
if (!$artista) {

    echo "
    <div class='alert alert-danger'>
        No se encontró el perfil del artista.
    </div>
    ";

    include("../partials/footer.php");

    exit();
}

?>
<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Crear Álbum 💿
        </h2>
        <p class="text-muted">
            Publica un nuevo álbum en la plataforma.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="artista_gestion_catalogo.php" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="../controllers/procesar_album_artista.php" method="POST" enctype="multipart/form-data">
            <input 
                type="hidden"
                name="accion"
                value="crear_album">
            <input 
                type="hidden"
                name="id_artista"
                value="<?php echo $artista['id_artista']; ?>">
            <!-- Portada -->
            <div class="mb-4 text-center">
                <img
                    src="../../resources/img/default_album.png"
                    class="img-fluid rounded shadow-sm mb-3"
                    style="width: 250px; height: 250px; object-fit: cover;">
                <div class="col-md-4 mx-auto">
                    <input
                        type="file"
                        name="imagen"
                        class="form-control"
                        required>
                </div>
            </div>
            <div class="row">
                <!-- Título -->
                <div class="col-md-8 mb-3">
                    <label class="form-label">
                        Título del Álbum
                    </label>
                    <input
                        type="text"
                        name="titulo"
                        class="form-control"
                        required>
                </div>
                <!-- Fecha -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        Fecha de Lanzamiento
                    </label>
                    <input
                        type="date"
                        name="fecha_lanzamiento"
                        class="form-control"
                        required>
                </div>
            </div>
            <!-- Tipo oculto -->
            <input 
                type="hidden"
                name="tipo"
                value="album">
            <!-- Botón -->
            <button type="submit" class="btn btn-dark">
                Crear Álbum
            </button>
        </form>
    </div>
</div>
<?php include("../partials/footer.php"); ?>