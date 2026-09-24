<?php
session_start();

include("../config/conexion.php");

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../index.php");

    exit();
}

// ==========================================
// ACCIONES DE GESTIÓN
// ==========================================

if (isset($_POST['accion'])) {

    $accion = $_POST['accion'];

    $id_cancion = $_POST['id_cancion'];

    // ======================================
    // INACTIVAR
    // ======================================

    if ($accion == "inactivar") {

        $sql = "
        UPDATE Cancion
        SET activo = 0
        WHERE id_cancion = ?
        ";

        $params = array($id_cancion);

        sqlsrv_query($conn, $sql, $params);
    }

    // ======================================
    // ACTIVAR
    // ======================================

    else if ($accion == "activar") {

        $sql = "
        UPDATE Cancion
        SET activo = 1
        WHERE id_cancion = ?
        ";

        $params = array($id_cancion);

        sqlsrv_query($conn, $sql, $params);
    }

    // ======================================
    // ELIMINAR
    // ======================================

    else if ($accion == "eliminar") {

        $sql = "
        DELETE FROM Cancion
        WHERE id_cancion = ?
        ";

        $params = array($id_cancion);

        sqlsrv_query($conn, $sql, $params);
    }

    // Redirección
    header("Location: ../views/artista_gestion_catalogo.php");

    exit();
}

// ==========================================
// RECIBIR DATOS
// ==========================================

$titulo = trim($_POST['titulo']);

$duracion = $_POST['duracion'];

$id_album = !empty($_POST['id_album'])
    ? $_POST['id_album']
    : null;

$id_artista = $_POST['id_artista'];

// ==========================================
// SUBIR IMAGEN
// ==========================================

$nombreImagen = $_FILES['imagen']['name'];

$rutaTemporalImagen = $_FILES['imagen']['tmp_name'];

$rutaDestinoImagen = "../../resources/img/canciones/" . $nombreImagen;

move_uploaded_file($rutaTemporalImagen, $rutaDestinoImagen);

// ==========================================
// SUBIR AUDIO
// ==========================================

$nombreAudio = $_FILES['audio']['name'];

$rutaTemporalAudio = $_FILES['audio']['tmp_name'];

$rutaDestinoAudio = "../../resources/audio/" . $nombreAudio;

move_uploaded_file($rutaTemporalAudio, $rutaDestinoAudio);

// ==========================================
// CREAR CANCIÓN
// ==========================================

$sql = "EXEC sp_CrearCancion ?, ?, ?, ?, ?, ?";

$params = array(

    $titulo,
    $duracion,
    $id_album,
    $id_artista,
    $nombreAudio,
    $nombreImagen

);

$stmt = sqlsrv_query($conn, $sql, $params);

// ==========================================
// REDIRECCIÓN
// ==========================================

if ($stmt) {

    // Si viene desde un álbum
    if ($id_album) {

        header("Location: ../views/artista_album.php?id=" . $id_album);
        exit();
    }

    // Si es sencillo
    header("Location: ../views/artista_gestion_catalogo.php?msg=cancion_creada");
    exit();

} else {
    die(print_r(sqlsrv_errors(), true));
}
?>