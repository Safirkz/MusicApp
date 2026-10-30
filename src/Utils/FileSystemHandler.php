<?php

class FileSystemHandler {
    private $baseDir;

    public function __construct($baseDir = null) {
        // Apunta por defecto a la carpeta resources en la raíz de la aplicación
        $this->baseDir = $baseDir ?? __DIR__ . '/../../resources/';
    }

    /**
     * Guarda archivos .mp3 en resources/audio/
     */
    public function guardarAudio(array $file) {
        return $this->moverArchivo($file, $this->baseDir . 'audio/');
    }

    /**
     * Guarda portadas en resources/img/albums/
     */
    public function guardarPortadaAlbum(array $file) {
        return $this->moverArchivo($file, $this->baseDir . 'img/albums/');
    }

    /**
     * Guarda fotos en resources/img/artistas/
     */
    public function guardarFotoArtista(array $file) {
        return $this->moverArchivo($file, $this->baseDir . 'img/artistas/');
    }

    /**
     * Guarda portadas en resources/img/canciones/
     */
    public function guardarPortadaCancion(array $file) {
        return $this->moverArchivo($file, $this->baseDir . 'img/canciones/');
    }

    /**
     * Guarda portadas en resources/img/playlists/
     */
    public function guardarPortadaPlaylist(array $file) {
        return $this->moverArchivo($file, $this->baseDir . 'img/playlists/');
    }

    /**
     * Mueve el archivo a la ubicación final conservando el nombre original
     */
    private function moverArchivo(array $file, string $destinoDir) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al cargar el archivo en PHP (Código: " . ($file['error'] ?? 'desconocido') . ")");
        }

        $nombreOriginal = basename($file['name']);
        $rutaTemporal   = $file['tmp_name'];

        if (!is_dir($destinoDir)) {
            if (!mkdir($destinoDir, 0777, true)) {
                throw new Exception("No se pudo crear la carpeta destino: $destinoDir");
            }
        }

        $rutaDestino = $destinoDir . $nombreOriginal;

        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            return $nombreOriginal;
        }

        throw new Exception("No se pudo mover el archivo desde la carpeta temporal a $rutaDestino");
    }
}