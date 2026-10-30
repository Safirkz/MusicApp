<?php
require_once __DIR__ . '/PublicacionTemplate.php';

class PublicarAlbum extends PublicacionTemplate {

    public function publicar(array $datosAlbum, array $archivoPortada) {
        // 1. Guardar la portada en resources/img/albums/
        $nombrePortada = $this->fileHandler->guardarPortadaAlbum($archivoPortada);

        // 2. Registrar el álbum en SQL Server
        $this->artistaRepo->crearAlbum(
            $datosAlbum['titulo'],
            $datosAlbum['fecha_lanzamiento'],
            $datosAlbum['id_artista'],
            $datosAlbum['tipo'] ?? 'album',
            $nombrePortada
        );

        return [
            'exito'   => true,
            'portada' => $nombrePortada
        ];
    }
}