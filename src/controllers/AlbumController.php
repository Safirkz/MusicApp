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

// ==========================================
// CREAR ÁLBUM
// ==========================================
if (isset($_POST['accion']) && $_POST['accion'] === 'crear_album') {
    try {
        $facade->publicarAlbum(
            [
                'titulo'            => trim($_POST['titulo']),
                'fecha_lanzamiento' => $_POST['fecha_lanzamiento'],
                'id_artista'        => $_POST['id_artista'],
                'tipo'              => $_POST['tipo']
            ],
            $_FILES['imagen']
        );

        header("Location: ../views/artista_gestion_catalogo.php?msg=album_creado");
        exit();

    } catch (Exception $e) {
        die("Error al crear álbum: " . $e->getMessage());
    }
}

// ==========================================
// CAMBIAR ESTADO ÁLBUM
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $idAlbum = $_POST['id_album'] ?? null;

    if ($idAlbum && ($accion === 'inactivar' || $accion === 'activar')) {
        // Traducir acción a valor binario BIT (0 para inactivar, 1 para activar)
        $nuevoEstado = ($accion === 'inactivar') ? 0 : 1;

        // Persistir en base de datos vía Facade -> Repository -> SP
        $facade->cambiarEstadoAlbum($idAlbum, $nuevoEstado);

        // Redirigir a la vista del álbum para refrescar la pantalla
        header("Location: ../views/artista_album.php?id=" . $idAlbum);
        exit();
    }
}