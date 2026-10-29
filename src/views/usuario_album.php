<?php
session_start();

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// ==========================================
// VALIDAR ID ÁLBUM
// ==========================================

if (!isset($_GET['id'])) {

    header("Location: usuario.php?filtro=album");
    exit();
}

$id_album = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// ==========================================
// OBTENER ÁLBUM
// ==========================================

$sqlAlbum = "EXEC sp_ObtenerAlbumUsuario ?";

$paramsAlbum = array(
    $id_album
);

$stmtAlbum = sqlsrv_query(
    $conn,
    $sqlAlbum,
    $paramsAlbum
);

$album = sqlsrv_fetch_array(
    $stmtAlbum,
    SQLSRV_FETCH_ASSOC
);

// Álbum no encontrado
if (!$album) {

    header("Location: usuario.php?filtro=album");
    exit();
}

// ==========================================
// OBTENER CANCIONES DEL ÁLBUM
// ==========================================

$sqlCanciones = "
EXEC sp_ObtenerCancionesAlbumUsuario ?, ?
";

$paramsCanciones = array(
    $id_album,
    $id_usuario
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

// ==========================================
// OBTENER PLAYLISTS DEL USUARIO
// ==========================================

$sqlPlaylists = "
EXEC sp_ObtenerPlaylistsUsuario ?
";

$paramsPlaylists = array(
    $id_usuario
);

$stmtPlaylists = sqlsrv_query(
    $conn,
    $sqlPlaylists,
    $paramsPlaylists
);

$playlists = array();

while ($row = sqlsrv_fetch_array(
    $stmtPlaylists,
    SQLSRV_FETCH_ASSOC
)) {

    $playlists[] = $row;
}
?>

<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-10">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <!-- Imagen -->
            <img
                src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                class="rounded shadow"
                style="
                    width: 220px;
                    height: 220px;
                    object-fit: cover;
                ">

            <!-- Información -->
            <div>
                <p class="text-muted mb-1">
                    ÁLBUM
                </p>
                <h1 class="display-5 fw-bold">
                    <?php echo htmlspecialchars($album['titulo']); ?>
                </h1>
                <p class="lead mb-2">
                    👤
                    <?php echo htmlspecialchars($album['nombre_artistico']); ?>
                </p>
                <p class="text-muted">
                    Lanzamiento:
                    <?php
                    echo $album['fecha_lanzamiento']->format('Y-m-d');
                    ?>
                </p>
                <span class="badge bg-dark fs-6">
                    <?php echo count($canciones); ?>
                    canciones
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-2 text-end">
        <a
            href="usuario.php?filtro=album"
            class="btn btn-secondary">
            Volver
        </a>
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
                        <?php foreach ($canciones as $index => $cancion) { ?>
                            <tr>
                                <!-- Número -->
                                <td>
                                    <?php echo $index + 1; ?>
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
                                    <?php
                                    echo htmlspecialchars(
                                        $cancion['titulo']
                                    );
                                    ?>
                                </td>
                                <!-- Artista -->
                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $cancion['nombre_artistico']
                                    );
                                    ?>
                                </td>
                                <!-- Duración -->
                                <td>
                                    <?php
                                    echo gmdate(
                                        "i:s",
                                        $cancion['duracion']
                                    );
                                    ?>
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
                                    <!-- Like -->
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
                                            class="btn btn-sm <?php echo $cancion['tiene_like'] ? 'btn-danger' : 'btn-outline-danger'; ?>">
                                            <?php if ($cancion['tiene_like']) { ?>
                                                🤍
                                            <?php } else { ?>
                                                ❤️
                                            <?php } ?>
                                        </button>
                                    </form>
                                    <!-- Agregar playlist -->
                                    <div class="dropdown d-inline">
                                        <button
                                            class="btn btn-outline-primary btn-sm dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown">
                                            ➕
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($playlists as $playlist) { ?>
                                                    <li>
                                                        <form
                                                            action="../controllers/procesar_playlist_cancion.php"
                                                            method="POST">
                                                            <input
                                                                type="hidden"
                                                                name="accion"
                                                                value="agregar">
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
                                                                class="dropdown-item">
                                                                <?php
                                                                echo htmlspecialchars(
                                                                    $playlist['nombre']
                                                                );
                                                                ?>
                                                            </button>
                                                        </form>
                                                    </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-secondary mb-0">
                Este álbum aún no tiene canciones.
            </div>
        <?php } ?>
    </div>
</div>
<?php include("../partials/footer.php"); ?>