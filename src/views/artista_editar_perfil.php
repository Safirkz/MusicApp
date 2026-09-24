<?php
session_start();

// Validar que solo artistas puedan entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {

    header("Location: ../index.php");

    exit();
}

include("../config/conexion.php");
include("../partials/header.php");

// ==========================================
// OBTENER ARTISTA DEL USUARIO LOGUEADO
// ==========================================

$id_usuario = $_SESSION['id_usuario'];
$sql = "EXEC sp_ObtenerArtistaPorUsuario ?";
$params = array($id_usuario);
$stmt = sqlsrv_query($conn, $sql, $params);
$artista = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Validar existencia
if (!$artista) {
    echo "
    <div class='alert alert-danger'>
        No se encontró el perfil del artista.
    </div>
    ";

    include("../partials/footer.php");
    exit();
}

?>
<div class="row mb-4">
    <div class="col-md-10">
        <h2>
            Editar Perfil 🎤
        </h2>
        <p class="text-muted">
            Actualiza tu información artística y tu imagen de perfil.
        </p>
    </div>
    <div class="col-md-2 text-end">
        <a href="artista_gestion_catalogo.php" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="../controllers/procesar_artista.php" method="POST" enctype="multipart/form-data">
            <input 
                type="hidden"
                name="accion"
                value="editar_perfil">
            <input 
                type="hidden"
                name="id_artista"
                value="<?php echo $artista['id_artista']; ?>">
            <div class="row">
                <!-- Imagen actual -->
                <div class="col-md-4 text-center mb-4">
                    <img
                        src="../../resources/img/artistas/<?php echo $artista['imagen_url']; ?>"
                        class="img-fluid rounded-circle shadow-sm mb-3"
                        style="width: 250px; height: 250px; object-fit: cover;">
                    <input
                        type="file"
                        name="imagen"
                        class="form-control">
                    <small class="text-muted">
                        Deja vacío si no deseas cambiar la imagen.
                    </small>
                </div>
                <!-- Información -->
                <div class="col-md-8">
                    <!-- Nombre artístico -->
                    <div class="mb-3">
                        <label class="form-label">
                            Nombre Artístico
                        </label>
                        <input
                            type="text"
                            name="nombre_artistico"
                            class="form-control"
                            required
                            value="<?php echo htmlspecialchars($artista['nombre_artistico']); ?>">
                    </div>
                    <!-- Nacionalidad -->
                    <div class="mb-3">
                        <label class="form-label">
                            Pais de Origen
                        </label>
                        <input
                            type="text"
                            name="nacionalidad"
                            class="form-control"
                            value="<?php echo htmlspecialchars($artista['nacionalidad']); ?>">
                    </div>
                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="form-label">
                            Descripción
                        </label>
                        <textarea
                            name="descripcion"
                            rows="6"
                            class="form-control"><?php echo htmlspecialchars($artista['descripcion']); ?></textarea>
                    </div>
                    <!-- Botón -->
                    <button type="submit" class="btn btn-dark">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include("../partials/footer.php"); ?>