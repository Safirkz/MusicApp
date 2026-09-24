<?php
session_start();

// Validar que solo el Administrador (Rol 1) pueda entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Validar que llegue el ID
if (!isset($_GET['id'])) {
    header("Location: admin_gestion_catalogo.php");
    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

$id_cancion = $_GET['id'];

// Obtener información completa de la canción
$sql = "EXEC sp_ObtenerCancionPorId ?";
$params = array($id_cancion);
$stmt = sqlsrv_query($conn, $sql, $params);

// Validar que exista
if (!$stmt || !($cancion = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
    echo "<div class='alert alert-danger'>La canción no existe.</div>";
    include("../partials/footer.php");
    exit();
}
?>
<div class="row mb-4">
    <div class="col-md-10">
        <h2>Gestión de Canción 🎵</h2>
        <p class="text-muted">
            Administra información y estado de la canción.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="admin_gestion_catalogo.php?filtro=cancion" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<div class="row">
    <!-- Imagen -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <img 
                src="../../resources/img/canciones/<?php echo $cancion['imagen_url']; ?>"
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
                    <?php echo htmlspecialchars($cancion['titulo']); ?>
                </h2>
                <!-- Artista -->
                <p class="mb-2">
                    <strong>Artista:</strong>
                    <?php echo htmlspecialchars($cancion['nombre_artistico']); ?>
                </p>
                <!-- Álbum -->
                <p class="mb-2">
                    <strong>Álbum:</strong>
                    <?php echo $cancion['titulo_album'] ? htmlspecialchars($cancion['titulo_album']) : 'Sencillo'; ?>
                </p>
                <!-- Duración -->
                <p class="mb-2">
                    <strong>Duración:</strong>
                    <?php echo gmdate("i:s", $cancion['duracion']); ?>
                </p>
                <!-- Fecha -->
                <p class="mb-2">
                    <strong>Fecha subida:</strong>
                    <?php echo $cancion['fecha_subida']->format('Y-m-d H:i'); ?>
                </p>
                <!-- Estado -->
                <p class="mb-4">
                    <strong>Estado:</strong>
                    <?php if ($cancion['activo'] == 1) { ?>
                        <span class="badge bg-success">
                            Activa
                        </span>
                    <?php } else { ?>
                        <span class="badge bg-danger">
                            Inactiva
                        </span>
                    <?php } ?>
                </p>
                <!-- Reproductor -->
                <div class="mb-4">
                    <button 
                        class="btn btn-dark"
                        onclick="reproducirCancion(
                            '<?php echo $cancion['id_cancion']; ?>',
                            '<?php echo $cancion['archivo_url']; ?>',
                            '<?php echo htmlspecialchars($cancion['titulo'], ENT_QUOTES); ?>',
                            '<?php echo htmlspecialchars($cancion['nombre_artistico'], ENT_QUOTES); ?>'
                        )">
                        ▶ Reproducir
                    </button>
                </div>
                <!-- Acciones -->
                <div class="mt-auto">
                    <?php if ($cancion['activo'] == 1) { ?>
                        <!-- Inactivar -->
                        <form action="../controllers/procesar_cancion.php" method="POST" class="d-inline">
                            <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
                            <input type="hidden" name="accion" value="inactivar">
                            <button 
                                type="submit"
                                class="btn btn-warning"
                                onclick="return confirm('¿Seguro que deseas inactivar esta canción?');">
                                Inactivar
                            </button>
                        </form>
                    <?php } else { ?>
                        <!-- Activar -->
                        <form action="../controllers/procesar_cancion.php" method="POST" class="d-inline">
                            <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
                            <input type="hidden" name="accion" value="activar">
                            <button 
                                type="submit"
                                class="btn btn-success">
                                Reactivar
                            </button>
                        </form>
                    <?php } ?>
                    <!-- Eliminar -->
                    <form action="../controllers/procesar_cancion.php" method="POST" class="d-inline">
                        <input type="hidden" name="id_cancion" value="<?php echo $cancion['id_cancion']; ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button 
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('PELIGRO: ¿Eliminar definitivamente esta canción?');">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("../partials/footer.php"); ?>