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
// OBTENER PLAYLISTS DEL USUARIO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$sql = "EXEC sp_ObtenerPlaylistsUsuario ?";

$params = array($id_usuario);

$stmt = sqlsrv_query($conn, $sql, $params);

// Guardar playlists
$playlists = array();

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    $playlists[] = $row;
}
?>

<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            Mis Playlists 🎵
        </h2>
        <p class="text-muted">
            Organiza tu música favorita en playlists personalizadas.
        </p>
    </div>
    <div class="col-md-4 text-end">
        <a href="usuario.php" class="btn btn-secondary">
            Volver
        </a>
        <a href="usuario_crear_playlist.php" class="btn btn-success">
            + Crear Playlist
        </a>
    </div>
</div>
<!-- Playlists -->
<div class="row">
    <?php if (count($playlists) > 0) { ?>
        <?php foreach ($playlists as $playlist) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <!-- Imagen -->
                    <?php if (!empty($playlist['imagen_url'])) { ?>
                        <img
                            src="../../resources/img/playlists/<?php echo $playlist['imagen_url']; ?>"
                            class="card-img-top"
                            style="
                                height: 220px;
                                object-fit: cover;
                            ">
                    <?php } else { ?>
                        <div
                            class="d-flex align-items-center justify-content-center bg-secondary text-white"
                            style="
                                height: 220px;
                                font-size: 4rem;
                            ">
                            🎵
                        </div>
                    <?php } ?>
                    <!-- Cabecera -->
                    <div class="
                        card-header
                        text-white
                        <?php
                        if ($playlist['es_favoritos'] == 1) {
                            echo 'bg-danger';
                        } else {
                            echo 'bg-dark';
                        }
                        ?>
                    ">
                        <h5 class="mb-0">

                            <?php
                            if ($playlist['es_favoritos'] == 1) {
                                echo "❤️ ";
                            } else {
                                echo "🎵 ";
                            }
                            ?>
                            <?php echo htmlspecialchars($playlist['nombre']); ?>
                        </h5>
                    </div>
                    <!-- Contenido -->
                    <div class="card-body d-flex flex-column">
                        <!-- Total canciones -->
                        <p class="text-muted mb-2">
                            Canciones:
                            <strong>
                                <?php echo $playlist['total_canciones']; ?>
                            </strong>
                        </p>
                        <!-- Fecha -->
                        <p class="small text-muted mb-4">
                            Creada:
                            <?php echo $playlist['fecha_creacion']->format('Y-m-d'); ?>
                        </p>
                        <!-- Botones -->
                        <div class="mt-auto d-grid gap-2">
                            <!-- Ver playlist -->
                            <a
                                href="usuario_playlist.php?id=<?php echo $playlist['id_playlist']; ?>"
                                class="btn btn-primary">
                                Ver Playlist
                            </a>
                            <!-- Editar -->
                            <?php if ($playlist['es_favoritos'] == 0) { ?>
                                <a
                                    href="usuario_editar_playlist.php?id=<?php echo $playlist['id_playlist']; ?>"
                                    class="btn btn-warning">
                                    Editar
                                </a>
                            <?php } ?>
                            <!-- Eliminar -->
                            <?php if ($playlist['es_favoritos'] == 0) { ?>
                                <form
                                    action="../controllers/procesar_playlist.php"
                                    method="POST">
                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar">
                                    <input
                                        type="hidden"
                                        name="id_playlist"
                                        value="<?php echo $playlist['id_playlist']; ?>">
                                    <button
                                        type="submit"
                                        class="btn btn-danger w-100"
                                        onclick="return confirm('¿Eliminar esta playlist?');">
                                        Eliminar
                                    </button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="alert alert-secondary">
                Aún no tienes playlists creadas.
            </div>
        </div>
    <?php } ?>
</div>
<?php include("../partials/footer.php"); ?>
