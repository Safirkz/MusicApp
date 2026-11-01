<?php
require_once __DIR__ . '/../Templates/PublicarSencillo.php';
require_once __DIR__ . '/../Templates/PublicarAlbum.php';

class LanzamientoFactory {
    private $artistaRepo;
    private $fileHandler;

    public function __construct($artistaRepo, $fileHandler) {
        $this->artistaRepo = $artistaRepo;
        $this->fileHandler = $fileHandler;
    }

    /**
     * Retorna la instancia adecuada según el tipo de publicación
     */
    public function crearLanzamiento(string $tipo) {
        switch (strtolower(trim($tipo))) {
            case 'album':
                return new PublicarAlbum($this->artistaRepo, $this->fileHandler);
            case 'sencillo':
            case 'cancion':
                return new PublicarSencillo($this->artistaRepo, $this->fileHandler);
            default:
                throw new Exception("Tipo de lanzamiento no válido: '{$tipo}'");
        }
    }
}