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
// OBTENER ARTISTA LOGUEADO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];
$sqlArtista = "EXEC sp_ObtenerArtistaPorUsuario ?";
$paramsArtista = array($id_usuario);
$stmtArtista = sqlsrv_query($conn, $sqlArtista, $paramsArtista);
$artista = sqlsrv_fetch_array($stmtArtista, SQLSRV_FETCH_ASSOC);

// ==========================================
// VALIDAR ÁLBUM PRESELECCIONADO
// ==========================================

$id_album_fijo = isset($_GET['id_album']) ? $_GET['id_album'] : null;

$album_fijo = null;

// Si viene un álbum por URL
if ($id_album_fijo) {

    $sqlAlbum = "
        SELECT
            id_album,
            titulo
        FROM Album
        WHERE id_album = ?
        AND id_artista = ?
    ";

    $paramsAlbum = array(
        $id_album_fijo,
        $artista['id_artista']
    );

    $stmtAlbum = sqlsrv_query($conn, $sqlAlbum, $paramsAlbum);

    $album_fijo = sqlsrv_fetch_array($stmtAlbum, SQLSRV_FETCH_ASSOC);

    // Si el álbum no pertenece al artista
    if (!$album_fijo) {

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
// OBTENER ÁLBUMES DEL ARTISTA
// ==========================================

$sqlAlbumes = "
    SELECT
        id_album,
        titulo
    FROM Album
    WHERE id_artista = ?
    AND activo = 1
    AND tipo = 'album'
    ORDER BY titulo ASC
";

$paramsAlbumes = array($artista['id_artista']);

$stmtAlbumes = sqlsrv_query($conn, $sqlAlbumes, $paramsAlbumes);

$albumes = array();

while ($row = sqlsrv_fetch_array($stmtAlbumes, SQLSRV_FETCH_ASSOC)) {

    $albumes[] = $row;
}

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
        <form action="../controllers/procesar_cancion_artista.php" method="POST" enctype="multipart/form-data">
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

