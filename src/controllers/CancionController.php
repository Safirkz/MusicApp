<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../facades/ArtistaFacade.php';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$facade = new ArtistaFacade($conn);
$accion = $_POST['accion'] ?? null;

// ==========================================
// ACCIONES DE GESTIÓN (Inactivar, Activar, Eliminar)
// ==========================================
if (in_array($accion, ['inactivar', 'activar', 'eliminar'])) {
    try {
        $idCancion = $_POST['id_cancion'];

        if ($accion === 'inactivar') {
            $facade->inactivarCancion($idCancion);
        } elseif ($accion === 'activar') {
            $facade->activarCancion($idCancion);
        } elseif ($accion === 'eliminar') {
            $facade->eliminarCancion($idCancion);
        }

        header("Location: ../views/artista_gestion_catalogo.php");
        exit();

    } catch (Exception $e) {
        die("Error en gestión de canción: " . $e->getMessage());
    }
}

// ==========================================
// CREAR CANCIÓN
// ==========================================
try {
    $idAlbum = !empty($_POST['id_album']) ? $_POST['id_album'] : null;

    $facade->publicarCancion(
        [
            'titulo'     => trim($_POST['titulo']),
            'duracion'   => $_POST['duracion'],
            'id_album'   => $idAlbum,
            'id_artista' => $_POST['id_artista']
        ],
        $_FILES['audio'],
        $_FILES['imagen'] ?? null
    );

    // Redirecciones idénticas al flujo original
    if ($idAlbum) {
        header("Location: ../views/artista_album.php?id=" . $idAlbum);
        exit();
    }

    header("Location: ../views/artista_gestion_catalogo.php?msg=cancion_creada");
    exit();

} catch (Exception $e) {
    die("Error al crear canción: " . $e->getMessage());
}