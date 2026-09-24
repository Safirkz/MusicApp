<?php
session_start();

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// Validar ID
if (!isset($_GET['id'])) {

    header("Location: usuario_playlists.php");
    exit();
}

$id_playlist = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// Obtener playlist
$sql = "
SELECT *
FROM Playlist
WHERE id_playlist = ?
AND id_usuario = ?
";

$params = array($id_playlist, $id_usuario);

$stmt = sqlsrv_query($conn, $sql, $params);

$playlist = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$playlist) {

    header("Location: usuario_playlists.php");
    exit();
}
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Editar Playlist ✏️
        </h2>
        <p class="text-muted">
            Actualiza la información de tu playlist.
        </p>
    </div>

    <div class="col-md-2 text-end">
        <a
            href="usuario_playlist.php?id=<?php echo $id_playlist; ?>"
            class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form
                    action="../controllers/procesar_playlist.php"
                    method="POST"
                    enctype="multipart/form-data">

                    <!-- Acción -->
                    <input
                        type="hidden"
                        name="accion"
                        value="editar">

                    <!-- ID -->
                    <input
                        type="hidden"
                        name="id_playlist"
                        value="<?php echo $playlist['id_playlist']; ?>">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">
                            Nombre de la Playlist
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($playlist['nombre']); ?>"
                            required>
                    </div>

                    <!-- Imagen actual -->
                    <?php if ($playlist['imagen_url']) { ?>
                        <div class="mb-3 text-center">
                            <img
                                src="../../resources/img/playlists/<?php echo $playlist['imagen_url']; ?>"
                                class="img-fluid rounded shadow-sm"
                                style="max-height: 250px;">
                        </div>
                    <?php } ?>

                    <!-- Nueva imagen -->
                    <div class="mb-4">
                        <label class="form-label">
                            Nueva Imagen
                        </label>

                        <input
                            type="file"
                            name="imagen"
                            class="form-control"
                            accept="image/*">
                    </div>

                    <!-- Botón -->
                    <button
                        type="submit"
                        class="btn btn-primary w-100">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../partials/footer.php"); ?>
```
