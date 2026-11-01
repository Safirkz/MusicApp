<?php
require_once __DIR__ . '/../Repositories/ArtistaRepository.php';
require_once __DIR__ . '/../utils/FileSystemHandler.php';
require_once __DIR__ . '/../factories/LanzamientoFactory.php';

class ArtistaFacade {
    private $artistaRepo;
    private $fileHandler;
    private $factory;

    public function __construct($conn) {
        $this->artistaRepo = new ArtistaRepository($conn);
        $this->fileHandler = new FileSystemHandler();
        $this->factory     = new LanzamientoFactory($this->artistaRepo, $this->fileHandler);
    }

    /**
     * Publica la ficha/contenedor de un álbum
     */
    public function publicarAlbum(array $datosAlbum, array $archivoPortada) {
        $publicador = $this->factory->crearLanzamiento('album');
        return $publicador->publicar($datosAlbum, $archivoPortada);
    }

    /**
     * Publica una canción (sencillo o pista de un álbum)
     */
    public function publicarCancion(array $datosCancion, array $archivoAudio, ?array $archivoPortada = null) {
        $publicador = $this->factory->crearLanzamiento('sencillo');
        return $publicador->publicar($datosCancion, $archivoAudio, $archivoPortada);
    }

    /**
     * Obtiene los álbumes del artista para poblar el <select> del formulario
     */

    public function cambiarEstadoCancion($idCancion, $nuevoEstado) {
        return $this->artistaRepo->cambiarEstadoCancion($idCancion, $nuevoEstado);
    }

    public function eliminarCancion($idCancion) {
        return $this->artistaRepo->eliminarCancion($idCancion);
    }

    public function obtenerArtistaPorUsuario($idUsuario) {
        return $this->artistaRepo->obtenerArtistaPorUsuario($idUsuario);
    }

    public function obtenerAlbumesArtista($idArtista) {
        return $this->artistaRepo->obtenerAlbumesArtista($idArtista);
    }

    public function obtenerSencillosArtista($idArtista) {
        return $this->artistaRepo->obtenerSencillosArtista($idArtista);
    }

    public function obtenerAlbumPorId($idAlbum) {
        return $this->artistaRepo->obtenerAlbumPorId($idAlbum);
    }

    public function obtenerCancionesAlbum($idAlbum) {
        return $this->artistaRepo->obtenerCancionesAlbum($idAlbum);
    }

    public function obtenerCancionPorId($idCancion) {
        return $this->artistaRepo->obtenerCancionPorId($idCancion);
    }

    public function cambiarEstadoAlbum($idAlbum, $nuevoEstado) {
        return $this->artistaRepo->cambiarEstadoAlbum($idAlbum, $nuevoEstado);
    }
}