<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../facades/ArtistaFacade.php';

class CancionController {
    private $facade;

    public function __construct($conn) {
        $this->facade = new ArtistaFacade($conn);
    }

    public function procesar(string $accion) {
        // Acciones de Gestión: inactivar, activar, eliminar
        if (in_array($accion, ['inactivar', 'activar', 'eliminar', 'eliminar_cancion', 'cambiar_estado_cancion'])) {
            $idCancion = $_POST['id_cancion'] ?? $_GET['id_cancion'] ?? null;

            if (!$idCancion) {
                throw new Exception("ID de canción no especificado.");
            }

            if ($accion === 'inactivar') {
                $this->facade->cambiarEstadoCancion($idCancion, 0);
            } elseif ($accion === 'activar') {
                $this->facade->cambiarEstadoCancion($idCancion, 1);
            } elseif ($accion === 'eliminar' || $accion === 'eliminar_cancion') {
                $this->facade->eliminarCancion($idCancion);
            }

            header("Location: ../views/artista_gestion_catalogo.php");
            exit();
        }

        // Crear Canción
        $idAlbum = !empty($_POST['id_album']) ? $_POST['id_album'] : null;

        $this->facade->publicarCancion(
            [
                'titulo'     => trim($_POST['titulo'] ?? ''),
                'duracion'   => $_POST['duracion'] ?? '',
                'id_album'   => $idAlbum,
                'id_artista' => $_POST['id_artista'] ?? null
            ],
            $_FILES['audio'] ?? [],
            $_FILES['imagen'] ?? null
        );

        if ($idAlbum) {
            header("Location: ../views/artista_album.php?id=" . $idAlbum);
            exit();
        }

        header("Location: ../views/artista_gestion_catalogo.php?msg=cancion_creada");
        exit();
    }
}