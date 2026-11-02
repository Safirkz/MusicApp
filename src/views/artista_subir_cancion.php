<?php
session_start();

// Validar que solo artistas puedan entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
require_once __DIR__ . '/../facades/ArtistaFacade.php';
include("../partials/header.php");

$facade = new ArtistaFacade($conn);

// ==========================================
// OBTENER ARTISTA LOGUEADO
// ==========================================
$id_usuario = $_SESSION['id_usuario'];
$artista = $facade->obtenerArtistaPorUsuario($id_usuario);

// ==========================================
// VALIDAR ÁLBUM PRESELECCIONADO
// ==========================================
$id_album_fijo = isset($_GET['id_album']) ? $_GET['id_album'] : null;
$album_fijo = null;

if ($id_album_fijo) {
    $album_fijo = $facade->obtenerAlbumPorId($id_album_fijo);

    // Validar propiedad del álbum
    if (!$album_fijo || $album_fijo['id_artista'] != $artista['id_artista']) {
        echo "
        <div class='alert alert-danger'>
            No tienes acceso a este álbum.
        </div>
        ";
        include("../partials/footer.php");
        exit();
    }
}

// ==========================================
// OBTENER ÁLBUMES DEL ARTISTA (PARA EL DESPLEGABLE)
// ==========================================
$albumes = $facade->obtenerAlbumesArtista($artista['id_artista']);
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Subir Canción 🎶
        </h2>
        <p class="text-muted">
            Publica una nueva canción en la plataforma.
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
        <form action="../gateway/ArtistaGateway.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="crear_cancion">
            <input
                type="hidden"
                name="id_artista"
                value="<?php echo $artista['id_artista']; ?>">
            <!-- Imagen -->
            <div class="mb-4 text-center">
                <img
                    src="../../resources/img/default_song.png"
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
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Título de la Canción
                    </label>
                    <input
                        type="text"
                        name="titulo"
                        class="form-control"
                        required>
                </div>
                <!-- Duración -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        Duración (segundos)
                    </label>
                    <input
                        type="number"
                        name="duracion"
                        class="form-control"
                        min="1"
                        required>
                </div>
                <!-- Audio -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        Archivo MP3
                    </label>
                    <input
                        type="file"
                        name="audio"
                        class="form-control"
                        accept=".mp3"
                        required>
                </div>
            </div>
            <!-- Álbum -->
            <div class="mb-4">
                <label class="form-label">
                    Álbum
                </label>
                <?php if ($album_fijo) { ?>
                    <!-- Álbum fijo -->
                    <input
                        type="text"
                        class="form-control"
                        value="<?php echo htmlspecialchars($album_fijo['titulo']); ?>"
                        disabled>
                    <input
                        type="hidden"
                        name="id_album"
                        value="<?php echo $album_fijo['id_album']; ?>">
                <?php } else { ?>
                    <!-- Selección manual -->
                    <select name="id_album" class="form-select">
                        <option value="">
                            Sencillo
                        </option>
                        <?php foreach ($albumes as $album) { ?>
                            <option value="<?php echo $album['id_album']; ?>">
                                <?php echo htmlspecialchars($album['titulo']); ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>
            <!-- Botón -->
            <button type="submit" class="btn btn-dark">
                Publicar Canción
            </button>
        </form>
    </div>
</div>
<?php include("../partials/footer.php"); ?>

