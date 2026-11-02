<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../facades/ArtistaFacade.php';

// 1. Validar sesión de usuario
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$facade = new ArtistaFacade($conn);

// 2. Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar_perfil') {
    try {
        $idArtista       = $_POST['id_artista'] ?? null;
        $nombreArtistico = trim($_POST['nombre_artistico'] ?? '');
        $nacionalidad    = trim($_POST['nacionalidad'] ?? '');
        $descripcion     = trim($_POST['descripcion'] ?? '');
        $archivoImagen   = $_FILES['imagen'] ?? null;

        if (!$idArtista) {
            throw new Exception("El ID del artista es obligatorio.");
        }

        // Delegar todo el flujo de trabajo a la fachada
        $facade->actualizarPerfilArtista(
            $idArtista,
            $nombreArtistico,
            $nacionalidad,
            $descripcion,
            $archivoImagen
        );

        // Redirección idéntica a la vista original
        header("Location: ../views/artista_gestion_catalogo.php?msg=perfil_actualizado");
        exit();

    } catch (Exception $e) {
        die("Error al procesar la solicitud: " . $e->getMessage());
    }
}