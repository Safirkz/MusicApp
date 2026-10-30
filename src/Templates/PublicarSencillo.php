<?php
require_once __DIR__ . '/PublicacionTemplate.php';

class PublicarSencillo extends PublicacionTemplate {

    public function publicar(array $datosSencillo, array $archivoAudio, ?array $archivoPortada = null) {
        $nombreAudio   = $this->fileHandler->guardarAudio($archivoAudio);
        $nombrePortada = $this->fileHandler->guardarPortadaCancion($archivoPortada);
        $duracion      = $this->convertirAEnteroSegundos($datosSencillo['duracion']);

        $this->artistaRepo->crearCancion(
            $datosSencillo['titulo'],
            $duracion,
            $datosSencillo['id_album'],
            $datosSencillo['id_artista'],
            $nombreAudio,
            $nombrePortada
        );

        return [
            'archivo_audio'   => $nombreAudio,
            'archivo_portada' => $nombrePortada
        ];
    }
}