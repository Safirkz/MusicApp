<?php
require_once __DIR__ . '/../Utils/FileSystemHandler.php';
require_once __DIR__ . '/../Repositories/ArtistaRepository.php';
require_once __DIR__ . '/../config/conexion.php';

abstract class PublicacionTemplate {
    protected $fileHandler;
    protected $artistaRepo;

    public function __construct(FileSystemHandler $fileHandler = null, ArtistaRepository $artistaRepo = null) {
        global $conn;
        
        $this->fileHandler = $fileHandler ?? new FileSystemHandler();
        $this->artistaRepo = $artistaRepo ?? new ArtistaRepository($conn);
    }

    /**
     * Convierte la cadena "HH:MM:SS" o "MM:SS" a segundos enteros
     */
    protected function convertirAEnteroSegundos($duracion) {
        if (is_numeric($duracion)) {
            return (int)$duracion;
        }

        $partes = array_reverse(explode(':', $duracion));
        $segundos = 0;
        $multiplicadores = [1, 60, 3600];

        foreach ($partes as $i => $val) {
            if (isset($multiplicadores[$i])) {
                $segundos += (int)$val * $multiplicadores[$i];
            }
        }

        return $segundos;
    }
}