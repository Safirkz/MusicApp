<?php
session_start();

// Validar que solo artistas puedan entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {

    header("Location: ../index.php");

    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// Validar ID
if (!isset($_GET['id'])) {

    header("Location: artista_gestion_catalogo.php");

    exit();
}

$id_album = $_GET['id'];

// ==========================================
// OBTENER ARTISTA LOGUEADO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$sqlArtista = "EXEC sp_ObtenerArtistaPorUsuario ?";

$paramsArtista = array($id_usuario);

$stmtArtista = sqlsrv_query($conn, $sqlArtista, $paramsArtista);

$artista = sqlsrv_fetch_array($stmtArtista, SQLSRV_FETCH_ASSOC);

// ==========================================
// OBTENER ÁLBUM
// ==========================================

$sqlAlbum = "
SELECT
    al.*,
    a.nombre_artistico
FROM Album al
INNER JOIN Artista a
    ON al.id_artista = a.id_artista
WHERE al.id_album = ?
AND al.id_artista = ?
";

$paramsAlbum = array(
    $id_album,
    $artista['id_artista']
);

$stmtAlbum = sqlsrv_query($conn, $sqlAlbum, $paramsAlbum);

$album = sqlsrv_fetch_array($stmtAlbum, SQLSRV_FETCH_ASSOC);

// Validar propiedad
if (!$album) {

    echo "
    <div class='alert alert-danger'>
        No tienes acceso a este álbum.
    </div>
    ";

    include("../partials/footer.php");

    exit();
}

// ==========================================
// OBTENER CANCIONES DEL ÁLBUM
// ==========================================

$sqlCanciones = "
SELECT
    id_cancion,
    titulo,
    duracion,
    archivo_url,
    imagen_url,
    activo
FROM Cancion
WHERE id_album = ?
ORDER BY fecha_subida DESC
";

$paramsCanciones = array($id_album);

$stmtCanciones = sqlsrv_query($conn, $sqlCanciones, $paramsCanciones);

$canciones = array();

while ($row = sqlsrv_fetch_array($stmtCanciones, SQLSRV_FETCH_ASSOC)) {

    $canciones[] = $row;
}

?>
<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Gestionar Álbum 💿
        </h2>
        <p class="text-muted">
            Administra canciones y configuración del álbum.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="artista_gestion_catalogo.php" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<!-- Información álbum -->
<div class="card shadow-sm mb-5">
    <div class="card-body">
        <div class="row">
            <!-- Imagen -->
            <div class="col-md-3 text-center">
                <img
                    src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                    class="img-fluid rounded shadow-sm"
                    style="width: 250px; height: 250px; object-fit: cover;">
            </div>
            <!-- Información -->
            <div class="col-md-9">
                <h3>
                    <?php echo htmlspecialchars($album['titulo']); ?>
                </h3>
                <p class="text-muted mb-2">
                    👤 <?php echo htmlspecialchars($album['nombre_artistico']); ?>
                </p>
                <p class="text-muted">
                    Lanzamiento:
                    <?php echo $album['fecha_lanzamiento']->format('Y-m-d'); ?>
                </p>
                <div class="mb-3">
                    <?php if ($album['activo'] == 1) { ?>
                        <span class="badge bg-success">
                            Activo
                        </span>
                    <?php } else { ?>
                        <span class="badge bg-danger">
                            Inactivo
                        </span>
                    <?php } ?>
                    <span class="badge bg-dark">
                        Canciones:
                        <?php echo count($canciones); ?>
                    </span>
                </div>
                <!-- Acciones -->
                <div class="d-flex gap-2 flex-wrap">
                    <a href="artista_subir_cancion.php?id_album=<?php echo $album['id_album']; ?>" class="btn btn-primary">
                        Subir Canción
                    </a>
                    <form action="../controllers/procesar_album.php" method="POST">
                        <input
                            type="hidden"
                            name="id_album"
                            value="<?php echo $album['id_album']; ?>">
                        <?php if ($album['activo'] == 1) { ?>
                            <input
                                type="hidden"
                                name="accion"
                                value="inactivar">
                            <button type="submit" class="btn btn-warning">
                                Inactivar Álbum
                            </button>
                        <?php } else { ?>
                            <input
                                type="hidden"
                                name="accion"
                                value="activar">
                            <button type="submit" class="btn btn-success">
                                Reactivar Álbum
                            </button>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Canciones -->
<h4 class="mb-3">
    Canciones del Álbum 🎶
</h4>
<div class="row">
    <?php if (count($canciones) > 0) { ?>
        <?php foreach ($canciones as $cancion) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img
                        src="../../resources/img/canciones/<?php echo $cancion['imagen_url']; ?>"
                        class="card-img-top"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($cancion['titulo']); ?>
                        </h5>
                        <div class="mb-3">
                            <span class="badge bg-dark">
                                <?php echo gmdate("i:s", $cancion['duracion']); ?>
                            </span>
                            <?php if ($cancion['activo'] == 1) { ?>
                                <span class="badge bg-success">
                                    Activa
                                </span>
                            <?php } else { ?>
                                <span class="badge bg-danger">
                                    Inactiva
                                </span>
                            <?php } ?>
                        </div>
                        <!-- Botón reproducir -->
                        <div class="mb-3">
                            <button
                                class="btn btn-success w-100"
                                onclick="reproducirCancion(
                                    '<?php echo $cancion['id_cancion']; ?>',
                                    '<?php echo $cancion['archivo_url']; ?>',
                                    '<?php echo htmlspecialchars($cancion['titulo'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($album['nombre_artistico'], ENT_QUOTES); ?>'
                                )">
                                ▶ Reproducir
                            </button>
                        </div>
                        <div class="mt-auto">
                            <a href="artista_cancion.php?id=<?php echo $cancion['id_cancion']; ?>" class="btn btn-dark w-100">
                                Gestionar Canción
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="alert alert-secondary">
                Este álbum aún no tiene canciones.
            </div>
        </div>
    <?php } ?>
</div>
<?php include("../partials/footer.php"); ?>