<?php
session_start();

// Validar seguridad ESTRICTA: Si no está logueado O su rol NO es 3 (Artista)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 3) {
    // Lo expulsamos hacia el index
    header("Location: ../index.php");
    exit();
}

// Incluimos la cabecera
include("../partials/header.php");
?>

<!-- Contenido específico del Artista -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="display-6">Panel de Artista 🎸</h2>
        <p class="lead">Hola, <?php echo $_SESSION['nombre']; ?>. Sube tu música y conéctate con tus fans.</p>
    </div>
</div>

<!-- Opciones de administración del artista -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-warning h-100 shadow-sm">
            <div class="card-header bg-warning text-dark">Mi Música</div>
            <div class="card-body">
                <h5 class="card-title">Canciones y Álbumes</h5>
                <p class="card-text">Sube nuevas canciones, crea álbumes o edita la información de tus pistas existentes.</p>
                <a href="artista_gestion_catalogo.php" class="btn btn-warning text-dark">Gestionar Música</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-secondary h-100 shadow-sm">
            <div class="card-header bg-secondary text-white">Estadísticas</div>
            <div class="card-body">
                <h5 class="card-title">Rendimiento</h5>
                <p class="card-text">Revisa cuántas reproducciones y likes han obtenido tus canciones este mes.</p>
                <a href="#" class="btn btn-secondary text-white">Ver Estadísticas</a>
            </div>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página
include("../partials/footer.php");
?>
