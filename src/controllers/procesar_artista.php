<?php
session_start();

include("../config/conexion.php");

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../index.php");

    exit();
}

// ==========================================
// EDITAR PERFIL ARTISTA
// ==========================================

if ($_POST['accion'] == "editar_perfil") {

    $id_artista = $_POST['id_artista'];

    $nombre_artistico = trim($_POST['nombre_artistico']);

    $nacionalidad = trim($_POST['nacionalidad']);

    $descripcion = trim($_POST['descripcion']);

    // ==========================================
    // OBTENER IMAGEN ACTUAL
    // ==========================================

    $sqlActual = "
    SELECT imagen_url
    FROM Artista
    WHERE id_artista = ?
    ";

    $paramsActual = array($id_artista);

    $stmtActual = sqlsrv_query($conn, $sqlActual, $paramsActual);

    $artistaActual = sqlsrv_fetch_array($stmtActual, SQLSRV_FETCH_ASSOC);

    // Mantener imagen actual por defecto
    $nombreImagen = $artistaActual['imagen_url'];

    // ==========================================
    // SUBIR NUEVA IMAGEN SI EXISTE
    // ==========================================

    if (!empty($_FILES['imagen']['name'])) {

        // Nombre del archivo
        $nombreImagen = $_FILES['imagen']['name'];

        // Ruta temporal
        $rutaTemporal = $_FILES['imagen']['tmp_name'];

        // Ruta final
        $rutaDestino = "../../resources/img/artistas/" . $nombreImagen;

        // Mover archivo
        move_uploaded_file($rutaTemporal, $rutaDestino);
    }

    // ==========================================
    // ACTUALIZAR PERFIL
    // ==========================================

    $sql = "EXEC sp_ActualizarPerfilArtista ?, ?, ?, ?, ?";

    $params = array(
        $id_artista,
        $nombre_artistico,
        $nacionalidad,
        $descripcion,
        $nombreImagen
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    // ==========================================
    // REDIRECCIÓN
    // ==========================================
    if ($stmt) {
        header("Location: ../views/artista_gestion_catalogo.php?msg=perfil_actualizado");
        exit();

    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}
?>