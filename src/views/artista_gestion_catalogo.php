<?php
session_start();

// Validar que solo artistas puedan entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {

    header("Location: ../index.php");

    exit();
}

include("../src/config/conexion.php");
include("../partials/header.php");

// ==========================================
// OBTENER ARTISTA DEL USUARIO LOGUEADO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$sqlArtista = "EXEC sp_ObtenerArtistaPorUsuario ?";

$paramsArtista = array($id_usuario);

$stmtArtista = sqlsrv_query($conn, $sqlArtista, $paramsArtista);

$artista = sqlsrv_fetch_array($stmtArtista, SQLSRV_FETCH_ASSOC);

// Validar que exista perfil de artista
if (!$artista) {

    echo "<div class='alert alert-danger'>
            No se encontró el perfil del artista.
          </div>";

    include("../partials/footer.php");

    exit();
}

$id_artista = $artista['id_artista'];

// ==========================================
// OBTENER ÁLBUMES DEL ARTISTA
// ==========================================

$sqlAlbumes = "
SELECT
    id_album,
    titulo,
    fecha_lanzamiento,
    imagen_url,
    activo
FROM Album
WHERE id_artista = ?
AND tipo = 'album'
ORDER BY fecha_lanzamiento DESC
";

$paramsAlbumes = array($id_artista);

$stmtAlbumes = sqlsrv_query($conn, $sqlAlbumes, $paramsAlbumes);

$albumes = array();

while ($row = sqlsrv_fetch_array($stmtAlbumes, SQLSRV_FETCH_ASSOC)) {

    $albumes[] = $row;
}

// ==========================================
// OBTENER SENCILLOS
// ==========================================

$sqlSencillos = "
SELECT
    id_cancion,
    titulo,
    duracion,
    archivo_url,
    imagen_url,
    activo
FROM Cancion
WHERE id_artista = ?
AND id_album IS NULL
ORDER BY fecha_subida DESC
";

$paramsSencillos = array($id_artista);

$stmtSencillos = sqlsrv_query($conn, $sqlSencillos, $paramsSencillos);

$sencillos = array();

while ($row = sqlsrv_fetch_array($stmtSencillos, SQLSRV_FETCH_ASSOC)) {

    $sencillos[] = $row;
}

// ==========================================
// ESTADÍSTICAS
// ==========================================
$total_albumes = count($albumes);
$total_sencillos = count($sencillos);

?>
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            Mi Catálogo 🎵
        </h2>
        <p class="text-muted">
            Administra tu música, álbumes y sencillos.
        </p>
    </div>
    <div class="col-md-4 text-end">
        <a href="artista_crear_album.php" class="btn btn-primary">
            Crear Álbum
        </a>
        <a href="artista_subir_cancion.php" class="btn btn-success">
            Subir Canción
        </a>
    </div>
</div>
<!-- Perfil artista -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2">
                <img
                    src="../../resources/img/artistas/<?php echo $artista['imagen_url']; ?>"
                    class="img-fluid rounded-circle"
                    style="width: 120px; height: 120px; object-fit: cover;">
            </div>
            <div class="col-md-10">
                <h3>
                    <?php echo htmlspecialchars($artista['nombre_artistico']); ?>
                </h3>
                <p class="text-muted mb-2">
                    <?php echo htmlspecialchars($artista['nacionalidad']); ?>
                </p>
                <p>
                    <?php echo htmlspecialchars($artista['descripcion']); ?>
                </p>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="badge bg-primary">
                            Álbumes:
                            <?php echo $total_albumes; ?>
                        </span>
                        <span class="badge bg-dark">
                            Sencillos:
                            <?php echo $total_sencillos; ?>
                        </span>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <a href="artista_editar_perfil.php" class="btn btn-warning btn-sm">
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Álbumes -->
<div class="mb-5">
    <h4 class="mb-3">
        Mis Álbumes 💿
    </h4>
    <div class="row">
        <?php if (count($albumes) > 0) { ?>
            <?php foreach ($albumes as $album) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <img
                            src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($album['titulo']); ?>
                            </h5>
                            <p class="small text-muted">
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
                            </div>
                            <div class="mt-auto">
                                <a href="artista_album.php?id=<?php echo $album['id_album']; ?>" class="btn btn-primary w-100">
                                    Gestionar Álbum
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary">
                    Aún no has creado álbumes.
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!-- Sencillos -->
<div class="mb-5">
    <h4 class="mb-3">
        Mis Sencillos 🎧
    </h4>
    <div class="row">
        <?php if (count($sencillos) > 0) { ?>
            <?php foreach ($sencillos as $cancion) { ?>
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
                            <div class="mt-auto d-grid gap-2">
                                <!-- Reproducir -->
                                <button
                                    class="btn btn-dark"
                                    onclick="reproducirCancion(
                                        '<?php echo $cancion['id_cancion']; ?>',
                                        '<?php echo $cancion['archivo_url']; ?>',
                                        '<?php echo htmlspecialchars($cancion['titulo'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($artista['nombre_artistico'], ENT_QUOTES); ?>'
                                    )">
                                    ▶ Reproducir
                                </button>
                                <!-- Gestionar -->
                                <a
                                    href="artista_cancion.php?id=<?php echo $cancion['id_cancion']; ?>"
                                    class="btn btn-success w-100">
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
                    Aún no has subido sencillos.
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php include("../partials/footer.php"); ?>