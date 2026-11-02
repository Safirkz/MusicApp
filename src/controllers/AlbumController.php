<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../facades/ArtistaFacade.php';

class AlbumController {
    private $facade;

    public function __construct($conn) {
        $this->facade = new ArtistaFacade($conn);
    }

    public function procesar(string $accion) {
        if ($accion === 'crear_album') {
            $this->facade->publicarAlbum(
                [
                    'titulo'            => trim($_POST['titulo'] ?? ''),
                    'fecha_lanzamiento' => $_POST['fecha_lanzamiento'] ?? '',
                    'id_artista'        => $_POST['id_artista'] ?? null,
                    'tipo'              => $_POST['tipo'] ?? ''
                ],
                $_FILES['imagen'] ?? []
            );

            header("Location: ../views/artista_gestion_catalogo.php?msg=album_creado");
            exit();
        }

        if ($accion === 'inactivar' || $accion === 'activar' || $accion === 'cambiar_estado_album') {
            $idAlbum     = $_POST['id_album'] ?? $_GET['id_album'] ?? null;
            $nuevoEstado = ($accion === 'inactivar') ? 0 : 1;

            if ($idAlbum) {
                $this->facade->cambiarEstadoAlbum($idAlbum, $nuevoEstado);
                header("Location: ../views/artista_album.php?id=" . $idAlbum);
                exit();
            }
        }

        throw new Exception("Acción no válida en AlbumController.");
    }
}