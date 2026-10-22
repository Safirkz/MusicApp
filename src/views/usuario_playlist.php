<?php
session_start();

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../src/config/conexion.php");
include("../partials/header.php");

// ==========================================
// VALIDAR ID PLAYLIST
// ==========================================

if (!isset($_GET['id'])) {

    header("Location: usuario_playlists.php");
    exit();
}

$id_playlist = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// ==========================================
// OBTENER PLAYLIST
// ==========================================

$sqlPlaylist = "EXEC sp_ObtenerPlaylist ?, ?";

$paramsPlaylist = array(
    $id_playlist,
    $id_usuario
);

$stmtPlaylist = sqlsrv_query(
    $conn,
    $sqlPlaylist,
    $paramsPlaylist
);

$playlist = sqlsrv_fetch_array(
    $stmtPlaylist,
    SQLSRV_FETCH_ASSOC
);

// Playlist no encontrada
if (!$playlist) {

    header("Location: usuario_playlists.php");
    exit();
}

// ==========================================
// OBTENER CANCIONES DE PLAYLIST
// ==========================================

$sqlCanciones = "EXEC sp_ObtenerCancionesPlaylist ?";

$paramsCanciones = array(
    $playlist['id_playlist']
);

$stmtCanciones = sqlsrv_query(
    $conn,
    $sqlCanciones,
    $paramsCanciones
);

$canciones = array();

while ($row = sqlsrv_fetch_array(
    $stmtCanciones,
    SQLSRV_FETCH_ASSOC
)) {

    $canciones[] = $row;
}
?>
<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            <?php echo htmlspecialchars($playlist['nombre']); ?>
            <?php if ($playlist['es_favoritos'] == 1) { ?>
                ❤️
            <?php } ?>
        </h2>
        <p class="text-muted">
            <?php if ($playlist['es_favoritos'] == 1) { ?>
                Todas las canciones que has marcado como favoritas.
            <?php } else { ?>
                Playlist personalizada del usuario.
            <?php } ?>
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
<!-- Información Playlist -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <!-- Imagen -->
                <?php if (!empty($playlist['imagen_url'])) { ?>
                    <img
                        src="../../resources/img/playlists/<?php echo $playlist['imagen_url']; ?>"
                        class="rounded shadow-sm"
                        style="
                            width: 120px;
                            height: 120px;
                            object-fit: cover;
                        ">
                <?php } else { ?>
                    <div
                        class="d-flex align-items-center justify-content-center bg-secondary text-white rounded"
                        style="
                            width: 120px;
                            height: 120px;
                            font-size: 3rem;
                        ">
                        🎵
                    </div>
                <?php } ?>
                <!-- Datos -->
                <div>
                    <h4 class="mb-1">
                        <?php echo htmlspecialchars($playlist['nombre']); ?>
                    </h4>
                    <p class="text-muted mb-2">

                        <?php echo count($canciones); ?>
                        canciones
                    </p>
                    <?php if ($playlist['es_favoritos'] == 1) { ?>
                        <span class="badge bg-danger fs-6">
                            ❤️ Favoritos
                        </span>
                    <?php } else { ?>
                        <span class="badge bg-dark fs-6">
                            🎵 Playlist
                        </span>
                    <?php } ?>
                </div>
            </div>
            <!-- Editar -->
            <?php if ($playlist['es_favoritos'] == 0) { ?>
                <div>
                    <a
                        href="usuario_editar_playlist.php?id=<?php echo $playlist['id_playlist']; ?>"
                        class="btn btn-warning">
                        Editar Playlist
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- Tabla canciones -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if (count($canciones) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Portada</th>
                            <th>Título</th>
                            <th>Artista</th>
                            <th>Duración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($canciones as $cancion) { ?>
                            <tr>
                                <!-- Orden -->
                                <td>
                                    <?php echo $cancion['orden']; ?>
                                </td>
                                <!-- Imagen -->
                                <td>
                                    <img
                                        src="../../resources/img/canciones/<?php echo $cancion['imagen_url']; ?>"
                                        width="70"
                                        class="rounded">
                                </td>
                                <!-- Título -->
                                <td>
                                    <?php echo htmlspecialchars($cancion['titulo']); ?>
                                </td>
                                <!-- Artista -->
                                <td>
                                    <?php echo htmlspecialchars($cancion['nombre_artistico']); ?>
                                </td>
                                <!-- Duración -->
                                <td>
                                    <?php echo gmdate("i:s", $cancion['duracion']); ?>
                                </td>
                                <!-- Acciones -->
                                <td>
                                    <!-- Reproducir -->
                                    <button
                                        class="btn btn-dark btn-sm"
                                        onclick="reproducirCancion(
                                            '<?php echo $cancion['id_cancion']; ?>',
                                            '<?php echo htmlspecialchars($cancion['archivo_url'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($cancion['titulo'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($cancion['nombre_artistico'], ENT_QUOTES); ?>'
                                        )">
                                        ▶
                                    </button>
                                    <!-- Favoritos -->
                                    <?php if ($playlist['es_favoritos'] == 1) { ?>
                                        <form
                                            action="../controllers/procesar_like.php"
                                            method="POST"
                                            class="d-inline">
                                            <input
                                                type="hidden"
                                                name="id_cancion"
                                                value="<?php echo $cancion['id_cancion']; ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm">
                                                🤍 Quitar
                                            </button>
                                        </form>
                                    <?php } else { ?>
                                        <!-- Quitar de playlist -->
                                        <form
                                            action="../controllers/procesar_playlist_cancion.php"
                                            method="POST"
                                            class="d-inline">
                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="quitar">
                                            <input
                                                type="hidden"
                                                name="id_playlist"
                                                value="<?php echo $playlist['id_playlist']; ?>">
                                            <input
                                                type="hidden"
                                                name="id_cancion"
                                                value="<?php echo $cancion['id_cancion']; ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm">
                                                🗑 Quitar
                                            </button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-secondary mb-0">
                <?php if ($playlist['es_favoritos'] == 1) { ?>
                    Aún no tienes canciones favoritas.
                <?php } else { ?>
                    Esta playlist aún no tiene canciones.
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php include("../partials/footer.php"); ?>