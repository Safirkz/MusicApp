<?php
session_start();

// Validar que solo el Administrador (Rol 1) pueda entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Validar que llegue el ID
if (!isset($_GET['id'])) {
    header("Location: admin_gestion_catalogo.php?filtro=album");
    exit();
}

include("../src/config/conexion.php");
include("../partials/header.php");

$id_album = $_GET['id'];

// ==========================================
// OBTENER INFORMACIÓN DEL ÁLBUM
// ==========================================

$sql = "EXEC sp_ObtenerAlbumPorId ?";

$params = array($id_album);

$stmt = sqlsrv_query($conn, $sql, $params);

// Validar que exista
if (!$stmt || !($album = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {

    echo "
    <div class='alert alert-danger'>
        El álbum no existe.
    </div>
    ";

    include("../partials/footer.php");

    exit();
}

// ==========================================
// OBTENER CANCIONES DEL ÁLBUM
// ==========================================

$sqlCanciones = "EXEC sp_ObtenerCancionesAlbum ?";

$paramsCanciones = array($id_album);

$stmtCanciones = sqlsrv_query($conn, $sqlCanciones, $paramsCanciones);

// Guardar canciones
$canciones = array();

while ($row = sqlsrv_fetch_array($stmtCanciones, SQLSRV_FETCH_ASSOC)) {
    $canciones[] = $row;
}
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>Gestión de Álbum 💿</h2>
        <p class="text-muted">
            Administra información y canciones del álbum.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="admin_gestion_catalogo.php?filtro=album" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<div class="row mb-4">
    <!-- Portada -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <img 
                src="../../resources/img/albums/<?php echo $album['imagen_url']; ?>"
                class="card-img-top"
                style="height: 400px; object-fit: cover;">
        </div>
    </div>
    <!-- Información -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <!-- Título -->
                <h2 class="mb-3">
                    <?php echo htmlspecialchars($album['titulo']); ?>
                </h2>
                <!-- Artista -->
                <p class="mb-2">
                    <strong>Artista:</strong>
                    <?php echo htmlspecialchars($album['nombre_artistico']); ?>
                </p>
                <!-- Fecha -->
                <p class="mb-2">
                    <strong>Fecha lanzamiento:</strong>
                    <?php echo $album['fecha_lanzamiento']->format('Y-m-d'); ?>
                </p>
                <!-- Canciones -->
                <p class="mb-2">
                    <strong>Total canciones:</strong>
                    <?php echo $album['total_canciones']; ?>
                </p>
                <!-- Estado -->
                <p class="mb-4">
                    <strong>Estado:</strong>
                    <?php if ($album['activo'] == 1) { ?>
                        <span class="badge bg-success">
                            Activo
                        </span>
                    <?php } else { ?>
                        <span class="badge bg-danger">
                            Inactivo
                        </span>
                    <?php } ?>
                </p>
                <!-- Acciones -->
                <div class="mt-auto">
                    <?php if ($album['activo'] == 1) { ?>
                        <!-- Inactivar -->
                        <form action="../controllers/procesar_album.php" method="POST" class="d-inline">
                            <input type="hidden" name="id_album" value="<?php echo $album['id_album']; ?>">
                            <input type="hidden" name="accion" value="inactivar">
                            <button 
                                type="submit"
                                class="btn btn-warning"
                                onclick="return confirm('¿Seguro que deseas inactivar este álbum?');">
                                Inactivar
                            </button>
                        </form>
                    <?php } else { ?>
                        <!-- Activar -->
                        <form action="../controllers/procesar_album.php" method="POST" class="d-inline">
                            <input type="hidden" name="id_album" value="<?php echo $album['id_album']; ?>">
                            <input type="hidden" name="accion" value="activar">
                            <button 
                                type="submit"
                                class="btn btn-success">
                                Reactivar
                            </button>
                        </form>
                    <?php } ?>
                    <!-- Eliminar -->
                    <form action="../controllers/procesar_album.php" method="POST" class="d-inline">
                        <input type="hidden" name="id_album" value="<?php echo $album['id_album']; ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button 
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('PELIGRO: ¿Eliminar definitivamente este álbum?');">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Lista de canciones -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        Canciones del Álbum
    </div>
    <div class="card-body">
        <?php if (count($canciones) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Portada</th>
                            <th>Título</th>
                            <th>Duración</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($canciones as $cancion) { ?>
                            <tr>
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
                                <!-- Duración -->
                                <td>
                                    <?php echo gmdate("i:s", $cancion['duracion']); ?>
                                </td>
                                <!-- Estado -->
                                <td>
                                    <?php if ($cancion['activo'] == 1) { ?>
                                        <span class="badge bg-success">
                                            Activa
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">
                                            Inactiva
                                        </span>
                                    <?php } ?>
                                </td>
                                <!-- Acciones -->
                                <td>
                                    <!-- Reproducir -->
                                    <button 
                                        class="btn btn-dark btn-sm"
                                        onclick="reproducirCancion(
                                            '<?php echo $cancion['id_cancion']; ?>',
                                            '<?php echo $cancion['archivo_url']; ?>',
                                            '<?php echo htmlspecialchars($cancion['titulo'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($album['nombre_artistico'], ENT_QUOTES); ?>'
                                        )">
                                        ▶
                                    </button>
                                    <!-- Gestionar -->
                                    <a 
                                        href="admin_cancion.php?id=<?php echo $cancion['id_cancion']; ?>"
                                        class="btn btn-primary btn-sm">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-secondary mb-0">
                Este álbum no tiene canciones registradas.
            </div>
        <?php } ?>
    </div>
</div>
<?php include("../partials/footer.php"); ?>