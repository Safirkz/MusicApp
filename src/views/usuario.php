<?php
session_start();

// Validar seguridad
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../src/config/conexion.php");
include("../partials/header.php");

// ==========================================
// BUSCADOR Y FILTRO
// ==========================================

$buscar = isset($_GET['buscar'])
    ? trim($_GET['buscar'])
    : '';

$filtro = isset($_GET['filtro'])
    ? $_GET['filtro']
    : 'cancion';

// ==========================================
// FILTRO: CANCIONES
// ==========================================

if ($filtro == "cancion") {

    $sql = "EXEC sp_BuscarCancionesUsuario ?, ?";

    $params = array(
        $buscar,
        $_SESSION['id_usuario']
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

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

    $artistas = array();

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $artistas[] = $row;
    }
}

// ==========================================
// OBTENER PLAYLISTS USUARIO
// ==========================================

$sqlPlaylists = "
SELECT
    id_playlist,
    nombre
FROM Playlist
WHERE id_usuario = ?
ORDER BY es_favoritos DESC, nombre ASC
";

$paramsPlaylists = array($_SESSION['id_usuario']);

$stmtPlaylists = sqlsrv_query(
    $conn,
    $sqlPlaylists,
    $paramsPlaylists
);

$playlistsUsuario = array();

while ($row = sqlsrv_fetch_array(
    $stmtPlaylists,
    SQLSRV_FETCH_ASSOC
)) {

    $playlistsUsuario[] = $row;
}
?>
<!-- Bienvenida -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="display-6">
            Bienvenido, <?php echo $_SESSION['nombre']; ?> 🎧
        </h2>
        <p class="lead text-muted">
            Explora música, artistas y álbumes desde tu biblioteca.
        </p>
    </div>
</div>
<!-- Accesos rápidos -->
<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <a href="usuario_playlist.php"
           class="btn btn-outline-danger w-100">
            ❤ Tus Me Gusta
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="usuario_playlists.php"
           class="btn btn-outline-primary w-100">
            📂 Tus Playlists
        </a>
    </div>
    <div class="col-md-4 mb-2">
        <a href="usuario_historial.php"
           class="btn btn-outline-dark w-100">
            🕓 Historial
        </a>
    </div>
</div>
<!-- Buscador -->
<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-7">
            <input
                type="text"
                name="buscar"
                class="form-control form-control-lg"
                placeholder="Buscar canciones, artistas o álbumes..."
                value="<?php echo htmlspecialchars($buscar); ?>">
        </div>
        <div class="col-md-3">
            <select name="filtro" class="form-select form-select-lg">
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
            <button type="submit" class="btn btn-dark btn-lg w-100">
                Buscar
            </button>
        </div>
    </div>
</form>
<?php if ($filtro == "cancion") { ?>
    <!-- CANCIONES -->
    <div class="row">
        <?php if (count($canciones) > 0) { ?>
            <?php foreach ($canciones as $cancion) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <!-- Imagen -->
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
                            <p class="text-muted mb-3">
                                💿
                                <?php
                                if ($cancion['titulo_album']) {
                                    echo htmlspecialchars($cancion['titulo_album']);
                                } else {
                                    echo "Sencillo";
                                }
                                ?>
                            </p>
                            <!-- Duración -->
                            <div class="mb-3">
                                <span class="badge bg-dark">
                                    <?php
                                    echo gmdate("i:s", $cancion['duracion']);
                                    ?>
                                </span>
                            </div>
                            <!-- Botones -->
                            <div class="mt-auto d-grid gap-2">
                                <!-- Reproducir -->
                                <button
                                    class="btn btn-dark"
                                    onclick="reproducirCancion(
                                        '<?php echo $cancion['id_cancion']; ?>',
                                        '<?php echo htmlspecialchars($cancion['archivo_url'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($cancion['titulo_cancion'], ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($cancion['nombre_artistico'], ENT_QUOTES); ?>'
                                    )">
                                    ▶ Reproducir
                                </button>
                                <!-- Like -->
                                <form
                                    action="../controllers/procesar_like.php"
                                    method="POST">
                                    <input
                                        type="hidden"
                                        name="id_cancion"
                                        value="<?php echo $cancion['id_cancion']; ?>">
                                    <button
                                        type="submit"
                                        class="btn <?php echo $cancion['tiene_like'] ? 'btn-danger' : 'btn-outline-danger'; ?> w-100 mb-2">
                                        <?php if ($cancion['tiene_like']) { ?>
                                            🤍 Quitar me gusta
                                        <?php } else { ?>
                                            ❤️ Me gusta
                                        <?php } ?>
                                    </button>
                                </form>
                                <!-- Agregar a playlist -->
                                <form
                                    action="../controllers/procesar_playlist_cancion.php"
                                    method="POST">
                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="agregar">
                                    <input
                                        type="hidden"
                                        name="id_cancion"
                                        value="<?php echo $cancion['id_cancion']; ?>">
                                    <div class="input-group">
                                        <select
                                            name="id_playlist"
                                            class="form-select"
                                            required>
                                            <option value="">
                                                Seleccionar playlist
                                            </option>
                                            <?php foreach ($playlistsUsuario as $playlist) { ?>
                                                <option
                                                    value="<?php echo $playlist['id_playlist']; ?>">
                                                    <?php echo htmlspecialchars($playlist['nombre']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <button
                                            type="submit"
                                            class="btn btn-success">
                                            ➕
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary">
                    No se encontraron canciones.
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php if ($filtro == "album") { ?>
    <!-- ÁLBUMES -->
    <div class="row">
        <?php if (count($albumes) > 0) { ?>
            <?php foreach ($albumes as $album) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <!-- Imagen -->
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
                            <!-- Canciones -->
                            <div class="mb-3">
                                <span class="badge bg-dark">
                                    Canciones:
                                    <?php echo $album['total_canciones']; ?>
                                </span>
                            </div>
                            <!-- Botón -->
                            <div class="mt-auto">
                                <a
                                    href="usuario_album.php?id=<?php echo $album['id_album']; ?>"
                                    class="btn btn-primary w-100">
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
                    No se encontraron álbumes.
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php if ($filtro == "artista") { ?>
    <!-- ARTISTAS -->
    <div class="row">
        <?php if (count($artistas) > 0) { ?>
            <?php foreach ($artistas as $artista) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <!-- Imagen -->
                        <img
                            src="../../resources/img/artistas/<?php echo $artista['imagen_url']; ?>"
                            class="card-img-top"
                            style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <!-- Nombre -->
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($artista['nombre_artistico']); ?>
                            </h5>
                            <!-- Nacionalidad -->
                            <p class="text-muted mb-2">
                                <?php echo htmlspecialchars($artista['nacionalidad']); ?>
                            </p>
                            <!-- Descripción -->
                            <p class="small">
                                <?php echo htmlspecialchars($artista['descripcion']); ?>
                            </p>
                            <!-- Stats -->
                            <div class="mb-3">
                                <span class="badge bg-primary">
                                    Álbumes:
                                    <?php echo $artista['total_albumes']; ?>
                                </span>
                                <span class="badge bg-dark">
                                    Canciones:
                                    <?php echo $artista['total_canciones']; ?>
                                </span>
                            </div>
                            <!-- Botón -->
                            <div class="mt-auto">
                                <a
                                    href="usuario_artista.php?id=<?php echo $artista['id_artista']; ?>"
                                    class="btn btn-success w-100">
                                    Ver Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary">
                    No se encontraron artistas.
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php include("../partials/footer.php"); ?>