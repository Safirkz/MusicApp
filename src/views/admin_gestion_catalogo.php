<?php
session_start();

// Validar que solo el Administrador (Rol 1) pueda entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// Obtener búsqueda y filtro
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'cancion';

// ==========================================
// FILTRO: CANCIONES
// ==========================================

if ($filtro == "cancion") {

    $sql = "EXEC sp_BuscarCancionesCatalogo ?";
    $params = array($buscar);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Guardar canciones
    $canciones = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $canciones[] = $row;
    }
}

// ==========================================
// FILTRO: ÁLBUMES
// ==========================================

else if ($filtro == "album") {

    $sql = "EXEC sp_BuscarAlbumesCatalogo ?";
    $params = array($buscar);

    $stmt = sqlsrv_query($conn, $sql, $params);

    // Guardar álbumes
    $albumes = array();

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $albumes[] = $row;
    }
}

// ==========================================
// FILTRO: ARTISTAS
// ==========================================

else {
    $sql = "EXEC sp_ObtenerCatalogoArtistas ?";
    $params = array($buscar);
    $stmt = sqlsrv_query($conn, $sql, $params);
    // Guardar artistas
    $artistas = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $artistas[] = $row;
    }
}

?>
<div class="row mb-4">
    <div class="col-md-10">
        <h2>Gestión de Catálogo 🎵</h2>
        <p class="text-muted">
            Explora artistas, álbumes y sencillos de la plataforma.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="admin.php" class="btn btn-secondary">
            Volver al Panel
        </a>
    </div>
</div>
<!-- Buscador y filtros -->
<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-7">
            <input 
                type="text"
                name="buscar"
                class="form-control"
                placeholder="Buscar canciones, artistas o álbumes..."
                value="<?php echo htmlspecialchars($buscar); ?>">
        </div>
        <div class="col-md-3">
            <select name="filtro" class="form-select">
                <option value="cancion" <?php if ($filtro == 'cancion') echo 'selected'; ?>>
                    Canciones
                </option>
                <option value="album" <?php if ($filtro == 'album') echo 'selected'; ?>>
                    Álbumes
                </option>
                <option value="artista" <?php if ($filtro == 'artista') echo 'selected'; ?>>
                    Artistas
                </option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100">
                Buscar
            </button>
        </div>
    </div>
</form>
<?php if ($filtro == "cancion") { ?>
    <!-- Resultados de canciones -->
    <div class="row">
        <?php if (count($canciones) > 0) { ?>
            <?php foreach ($canciones as $cancion) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <!-- Imagen de la canción -->
                        <img 
                            src="../../resources/img/canciones/<?php echo $cancion['imagen_url']; ?>"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <!-- Título -->
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($cancion['titulo_cancion']); ?>
                            </h5>
                            <!-- Artista -->
                            <p class="text-muted mb-1">
                                👤 <?php echo htmlspecialchars($cancion['nombre_artistico']); ?>
                            </p>
                            <!-- Álbum -->
                            <p class="text-muted mb-2">
                                💿 
                                <?php echo $cancion['titulo_album'] ? htmlspecialchars($cancion['titulo_album']) : 'Sencillo'; ?>
                            </p>
                            <!-- Duración -->
                            <div class="mb-3">
                                <span class="badge bg-dark">
                                    Duración:
                                    <?php echo gmdate("i:s", $cancion['duracion']); ?>
                                </span>
                            </div>
                            <!-- Botones --> 
                            <div class="mt-auto d-grid gap-2">
                                <!-- Reproducir -->
                                <button 
                                    class="btn btn-dark"
                                    onclick="reproducirCancion(
                                        '<?php echo $cancion['id_cancion']; ?>',
                                        '<?php echo $cancion['archivo_url']; ?>',
                                        '<?php echo htmlspecialchars($cancion['titulo_cancion'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($cancion['nombre_artistico'], ENT_QUOTES); ?>'
                                    )">
                                    ▶ Reproducir
                                </button>
                                <!-- Gestionar -->
                                <a 
                                    href="admin_cancion.php?id=<?php echo $cancion['id_cancion']; ?>" 
                                    class="btn btn-primary">
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
                    No se encontraron canciones para esta búsqueda.
                </div>
            </div>
        <?php } ?>
    </div>
    <?php } else if ($filtro == "album") { ?>
    <!-- Resultados de álbumes -->
    <div class="row">
        <?php if (count($albumes) > 0) { ?>
            <?php foreach ($albumes as $album) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <!-- Portada -->
                        <img 
                            src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <!-- Título -->
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($album['titulo']); ?>
                            </h5>
                            <!-- Artista -->
                            <p class="text-muted mb-2">

                                👤 <?php echo htmlspecialchars($album['nombre_artistico']); ?>
                            </p>
                            <!-- Fecha -->
                            <p class="small text-muted">
                                Lanzamiento:
                                <?php echo $album['fecha_lanzamiento']->format('Y-m-d'); ?>
                            </p>
                            <!-- Total canciones -->
                            <div class="mb-3">
                                <span class="badge bg-dark">
                                    Canciones:
                                    <?php echo $album['total_canciones']; ?>
                                </span>
                            </div>
                            <!-- Botón -->
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
                    No se encontraron álbumes para esta búsqueda.
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <!-- Resultados de artistas -->
    <div class="row">
        <?php if (count($artistas) > 0) { ?>
            <?php foreach ($artistas as $artista) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <!-- Imagen del artista -->
                        <img 
                            src="../../resources/img/artistas/<?php echo $artista['imagen_url']; ?>"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <!-- Nombre artístico -->
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($artista['nombre_artistico']); ?>
                            </h5>
                            <!-- Nacionalidad -->
                            <p class="text-muted mb-2">
                                <?php echo htmlspecialchars($artista['nacionalidad']); ?>
                            </p>
                            <!-- Descripción -->
                            <p class="card-text small">
                                <?php echo htmlspecialchars($artista['descripcion']); ?>
                            </p>
                            <!-- Estadísticas rápidas -->
                            <div class="mb-3">
                                <span class="badge bg-primary">
                                    Álbumes: <?php echo $artista['total_albumes']; ?>
                                </span>
                                <span class="badge bg-dark">
                                    Sencillos: <?php echo $artista['total_sencillos']; ?>
                                </span>
                                <span class="badge bg-warning">
                                    Canciones: <?php echo $artista['total_canciones']; ?>
                                </span>
                            </div>
                            <!-- Botón -->
                            <div class="mt-auto">
                                <a href="admin_catalogo_artista.php?id=<?php echo $artista['id_artista']; ?>" class="btn btn-success w-100">
                                    Ver Catálogo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary">
                    No se encontraron artistas para esta búsqueda.
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php include("../partials/footer.php"); ?>