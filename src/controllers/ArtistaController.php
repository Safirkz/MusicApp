<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../facades/ArtistaFacade.php';

class ArtistaController {
    private $facade;

    public function __construct($conn) {
        $this->facade = new ArtistaFacade($conn);
    }

    public function editarPerfil() {
        $idArtista       = $_POST['id_artista'] ?? null;
        $nombreArtistico = trim($_POST['nombre_artistico'] ?? '');
        $nacionalidad    = trim($_POST['nacionalidad'] ?? '');
        $descripcion     = trim($_POST['descripcion'] ?? '');
        $archivoImagen   = $_FILES['imagen'] ?? null;

        if (!$idArtista) {
            throw new Exception("El ID del artista es obligatorio.");
        }

        $this->facade->actualizarPerfilArtista(
            $idArtista,
            $nombreArtistico,
            $nacionalidad,
            $descripcion,
            $archivoImagen
        );

        header("Location: ../views/artista_gestion_catalogo.php?msg=perfil_actualizado");
        exit();
    }
}