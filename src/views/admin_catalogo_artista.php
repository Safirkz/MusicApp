<?php
session_start();

// Validar que solo el Administrador (Rol 1) pueda entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Validar que llegue un ID de artista
if (!isset($_GET['id'])) {
    header("Location: admin_gestion_catalogo.php");
    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

$id_artista = $_GET['id'];

// Obtener información del artista
$sqlArtista = "EXEC sp_ObtenerDetalleArtista ?";
$paramsArtista = array($id_artista);

$stmtArtista = sqlsrv_query($conn, $sqlArtista, $paramsArtista);
$artista = sqlsrv_fetch_array($stmtArtista, SQLSRV_FETCH_ASSOC);

// Validar que el artista exista
if (!$artista) {
    header("Location: admin_gestion_catalogo.php");
    exit();
}

// Obtener álbumes del artista
$sqlAlbumes = "EXEC sp_ObtenerAlbumesArtista ?";
$paramsAlbumes = array($id_artista);

$stmtAlbumes = sqlsrv_query($conn, $sqlAlbumes, $paramsAlbumes);

// Guardar álbumes
$albumes = array();

while ($rowAlbum = sqlsrv_fetch_array($stmtAlbumes, SQLSRV_FETCH_ASSOC)) {
    $albumes[] = $rowAlbum;
}

// Obtener sencillos del artista
$sqlSencillos = "EXEC sp_ObtenerSencillosArtista ?";
$paramsSencillos = array($id_artista);

$stmtSencillos = sqlsrv_query($conn, $sqlSencillos, $paramsSencillos);

// Guardar sencillos
$sencillos = array();

while ($rowSencillo = sqlsrv_fetch_array($stmtSencillos, SQLSRV_FETCH_ASSOC)) {
    $sencillos[] = $rowSencillo;
}
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            <?php echo htmlspecialchars($artista['nombre_artistico']); ?> 🎤
        </h2>
        <p class="text-muted">
            Explora los álbumes y sencillos del artista.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="admin_gestion_catalogo.php" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<!-- Información del artista -->
<div class="card shadow-sm mb-5">
    <div class="row g-0">
        <div class="col-md-4">
            <img 
                src="../../resources/img/artistas/<?php echo $artista['imagen_url']; ?>"
                class="img-fluid rounded-start w-100"
                style="height: 100%; object-fit: cover;">
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h3 class="card-title">
                    <?php echo htmlspecialchars($artista['nombre_artistico']); ?>
                </h3>
                <p class="text-muted">
                    <?php echo htmlspecialchars($artista['nacionalidad']); ?>
                </p>
                <p class="card-text">
                    <?php echo htmlspecialchars($artista['descripcion']); ?>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- Sección de álbumes -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Álbumes 💿</h3>
    <span class="badge bg-dark">
        Total: <?php echo count($albumes); ?>
    </span>
</div>
<div class="row mb-5">
    <?php if (count($albumes) > 0) { ?>
        <?php foreach ($albumes as $album) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <!-- Portada del álbum -->
                    <img 
                        src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                        class="card-img-top"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($album['titulo']); ?>
                        </h5>
                        <p class="text-muted small">
                            Lanzamiento:
                            <?php echo $album['fecha_lanzamiento']->format('Y-m-d'); ?>
                        </p>
                        <div class="mt-auto">
                            <a href="admin_album.php?id=<?php echo $album['id_album']; ?>" class="btn btn-primary w-100">
                                Ver Álbum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="alert alert-secondary">
                Este artista no tiene álbumes registrados.
            </div>
        </div>
    <?php } ?>
</div>
<!-- Sección de sencillos -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Sencillos 🎶</h3>
    <span class="badge bg-dark">
        Total: <?php echo count($sencillos); ?>
    </span>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Portada</th>
                        <th>Título</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sencillos) > 0) { ?>
                        <?php foreach ($sencillos as $sencillo) { ?>
                            <tr>
                                <td>
                                    <img 
                                        src="../../resources/img/canciones/<?php echo $sencillo['imagen_url']; ?>"
                                        width="80"
                                        class="rounded">
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($sencillo['titulo']); ?>
                                </td>
                                <td>
                                    <?php echo gmdate("i:s", $sencillo['duracion']); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Este artista no tiene sencillos registrados.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../partials/footer.php"); ?>