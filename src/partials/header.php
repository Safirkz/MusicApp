<?php
// /partials/header.php
// Este archivo irá al principio de TODAS tus vistas (admin, usuario, artista)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeatUp</title>
    <!-- Conectamos Bootstrap 5 directamente desde internet (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Barra de navegación (Navbar) oscura -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🎵 MusicApp</a>
            
            <!-- Botón para cerrar sesión a la derecha -->
            <div class="d-flex">
                <!-- Usamos isset() para mostrar el botón SOLO si el usuario está logueado -->
                <?php if(isset($_SESSION['id_usuario'])): ?>
                    <span class="navbar-text me-3 text-white">
                        Hola, <?php echo $_SESSION['nombre']; ?>
                    </span>
                    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal donde irá el contenido de cada página -->
    <div class="container mt-4">