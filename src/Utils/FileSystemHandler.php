<?php

class FileSystemHandler {
    private $baseDir;

    public function __construct() {
        // Ruta relativa hacia la carpeta de recursos desde src/Utils/
        $this->baseDir = __DIR__ . '/../../resources/';
    }

    /**
     * Subir imagen de álbum a la carpeta /resources/img/albums/
     */
    public function guardarImagenAlbum($fileTmp, $nombreArchivo) {
        if (empty($nombreArchivo) || empty($fileTmp)) {
            return null;
        }
        $destino = $this->baseDir . "img/albums/" . $nombreArchivo;
        return move_uploaded_file($fileTmp, $destino) ? $nombreArchivo : null;
    }

    /**
     * Subir imagen de perfil a la carpeta /resources/img/artistas/
     */
    public function guardarImagenArtista($fileTmp, $nombreArchivo) {
        if (empty($nombreArchivo) || empty($fileTmp)) {
            return null;
        }
        $destino = $this->baseDir . "img/artistas/" . $nombreArchivo;
        return move_uploaded_file($fileTmp, $destino) ? $nombreArchivo : null;
    }

    /**
     * Subir portada de canción a la carpeta /resources/img/canciones/
     */
    public function guardarImagenCancion($fileTmp, $nombreArchivo) {
        if (empty($nombreArchivo) || empty($fileTmp)) {
            return null;
        }
        $destino = $this->baseDir . "img/canciones/" . $nombreArchivo;
        return move_uploaded_file($fileTmp, $destino) ? $nombreArchivo : null;
    }

    /**
     * Subir pista de audio a la carpeta /resources/audio/
     */
    public function guardarAudioCancion($fileTmp, $nombreArchivo) {
        if (empty($nombreArchivo) || empty($fileTmp)) {
            return null;
        }
        $destino = $this->baseDir . "audio/" . $nombreArchivo;
        return move_uploaded_file($fileTmp, $destino) ? $nombreArchivo : null;
    }
}
