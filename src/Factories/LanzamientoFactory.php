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
                // CORREGIDO: Se invierten los argumentos para coincidir con PublicacionTemplate
                return new PublicarAlbum($this->fileHandler, $this->artistaRepo);
            case 'sencillo':
            case 'cancion':
                // CORREGIDO: Se invierten los argumentos para coincidir con PublicacionTemplate
                return new PublicarSencillo($this->fileHandler, $this->artistaRepo);
            default:
                throw new Exception("Tipo de lanzamiento no válido: '{$tipo}'");
        }
    }
}