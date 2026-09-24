<?php
session_start();

include("../config/conexion.php");

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../index.php");

    exit();
}

// ==========================================
// CREAR ÁLBUM
// ==========================================

if ($_POST['accion'] == "crear_album") {

    $titulo = trim($_POST['titulo']);

    $fecha_lanzamiento = $_POST['fecha_lanzamiento'];

    $id_artista = $_POST['id_artista'];

    $tipo = $_POST['tipo'];

    // ==========================================
    // SUBIR IMAGEN
    // ==========================================

    $nombreImagen = $_FILES['imagen']['name'];

    $rutaTemporal = $_FILES['imagen']['tmp_name'];

    $rutaDestino = "../../resources/img/albums/" . $nombreImagen;

    move_uploaded_file($rutaTemporal, $rutaDestino);

    // ==========================================
    // CREAR ÁLBUM
    // ==========================================

    $sql = "EXEC sp_CrearAlbum ?, ?, ?, ?, ?";

    $params = array(
        $titulo,
        $fecha_lanzamiento,
        $id_artista,
        $tipo,
        $nombreImagen
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    // ==========================================
    // REDIRECCIÓN
    // ==========================================

    if ($stmt) {

        header("Location: ../views/artista_gestion_catalogo.php?msg=album_creado");

        exit();

    } else {

        die(print_r(sqlsrv_errors(), true));
    }
}
?>