<?php
session_start();

// Validar seguridad ESTRICTA: Si no está logueado O su rol NO es 1 (Admin)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    // Lo expulsamos hacia el index para que el sistema lo redirija a donde sí pertenece
    header("Location: ../index.php");
    exit();
}

// Incluimos la cabecera
include("../partials/header.php");
?>

<!-- Contenido específico del Administrador -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="display-6">Panel de Administración ⚙️</h2>
        <p class="lead">Bienvenido, <?php echo $_SESSION['nombre']; ?>. Desde aquí puedes gestionar toda la plataforma.</p>
    </div>
</div>

<!-- Opciones de administración -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-danger h-100 shadow-sm">
            <div class="card-header bg-danger text-white">Gestión de Usuarios</div>
            <div class="card-body">
                <h5 class="card-title">Cuentas</h5>
                <p class="card-text">Administra roles de usuario, bloqueos y datos de perfil.</p>
                <a href="admin_gestion_usuarios.php" class="btn btn-danger w-100">Ir a Usuarios</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-dark h-100 shadow-sm">
            <div class="card-header bg-dark text-white">Catálogo Musical</div>
            <div class="card-body">
                <h5 class="card-title">Música y Categorías</h5>
                <p class="card-text">Administra los géneros musicales y el catálogo de canciones/álbumes.</p>
                
                <div class="d-grid gap-2">
                    <a href="admin_gestion_generos.php" class="btn btn-outline-dark">Gestionar Géneros</a>
                    <a href="admin_gestion_catalogo.php" class="btn btn-dark">Gestionar Canciones/Álbumes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página
include("../partials/footer.php");
?>