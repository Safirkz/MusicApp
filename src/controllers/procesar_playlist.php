<?php
session_start();

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../src/config/conexion.php");

$id_usuario = $_SESSION['id_usuario'];

$accion = $_POST['accion'] ?? "";

// ==========================================
// CREAR PLAYLIST
// ==========================================

if ($accion == "crear") {

    $nombre = trim($_POST['nombre']);

    // Validar nombre
    if (empty($nombre)) {

        header("Location: ../views/usuario_crear_playlist.php?error=nombre");
        exit();
    }

    // ==========================================
    // SUBIR IMAGEN
    // ==========================================

    $imagen_url = null;

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] == 0
    ) {

        $directorio = "../../resources/img/playlists/";

        // Nombre único
        $nombreImagen = basename($_FILES['imagen']['name']);

        // Ruta física
        $rutaCompleta = $directorio . $nombreImagen;

        // Mover imagen
        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $rutaCompleta
        );

        // Guardar SOLO nombre archivo
        $imagen_url = $nombreImagen;
    }

    // ==========================================
    // INSERTAR PLAYLIST
    // ==========================================

    $sql = "
    INSERT INTO Playlist (
        nombre,
        id_usuario,
        imagen_url
    )
    VALUES (?, ?, ?)
    ";

    $params = array(
        $nombre,
        $id_usuario,
        $imagen_url
    );

    sqlsrv_query($conn, $sql, $params);

    header("Location: ../views/usuario_playlists.php?msg=creada");
    exit();
}

// ==========================================
// EDITAR PLAYLIST
// ==========================================

if ($accion == "editar") {

    $id_playlist = $_POST['id_playlist'];

    $nombre = trim($_POST['nombre']);

    // ==========================================
    // VALIDAR PLAYLIST DEL USUARIO
    // ==========================================

    $sqlValidar = "
    SELECT 1
    FROM Playlist
    WHERE id_playlist = ?
    AND id_usuario = ?
    ";

    $paramsValidar = array(
        $id_playlist,
        $id_usuario
    );

    $stmtValidar = sqlsrv_query(
        $conn,
        $sqlValidar,
        $paramsValidar
    );

    if (!sqlsrv_fetch_array($stmtValidar, SQLSRV_FETCH_ASSOC)) {

        header("Location: ../views/usuario_playlists.php");
        exit();
    }

    // ==========================================
    // OBTENER IMAGEN ACTUAL
    // ==========================================

    $sqlActual = "
    SELECT imagen_url
    FROM Playlist
    WHERE id_playlist = ?
    AND id_usuario = ?
    ";

    $paramsActual = array(
        $id_playlist,
        $id_usuario
    );

    $stmtActual = sqlsrv_query(
        $conn,
        $sqlActual,
        $paramsActual
    );

    $playlistActual = sqlsrv_fetch_array(
        $stmtActual,
        SQLSRV_FETCH_ASSOC
    );

    $imagen_url = $playlistActual['imagen_url'];

    // ==========================================
    // CAMBIAR IMAGEN
    // ==========================================

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] == 0
    ) {

        $directorio = "../../resources/img/playlists/";

        // Nombre único
        $nombreImagen = basename($_FILES['imagen']['name']);

        // Ruta física
        $rutaCompleta = $directorio . $nombreImagen;

        // Mover imagen
        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $rutaCompleta
        );

        // Guardar SOLO nombre archivo
        $imagen_url = $nombreImagen;
    }

    // ==========================================
    // ACTUALIZAR PLAYLIST
    // ==========================================

    $sql = "
    UPDATE Playlist
    SET nombre = ?,
        imagen_url = ?
    WHERE id_playlist = ?
    ";

    $params = array(
        $nombre,
        $imagen_url,
        $id_playlist
    );

    sqlsrv_query($conn, $sql, $params);

    header("Location: ../views/usuario_playlist.php?id=$id_playlist&msg=editada");
    exit();
}

// ==========================================
// ELIMINAR PLAYLIST
// ==========================================

if ($accion == "eliminar") {

    $id_playlist = $_POST['id_playlist'];

    // ==========================================
    // VALIDAR PLAYLIST DEL USUARIO
    // ==========================================

    $sqlValidar = "
    SELECT 1
    FROM Playlist
    WHERE id_playlist = ?
    AND id_usuario = ?
    ";

    $paramsValidar = array(
        $id_playlist,
        $id_usuario
    );

    $stmtValidar = sqlsrv_query(
        $conn,
        $sqlValidar,
        $paramsValidar
    );

    if (!sqlsrv_fetch_array($stmtValidar, SQLSRV_FETCH_ASSOC)) {

        header("Location: ../views/usuario_playlists.php");
        exit();
    }

    // ==========================================
    // ELIMINAR PLAYLIST
    // ==========================================

    $sql = "
    DELETE FROM Playlist
    WHERE id_playlist = ?
    ";

    $params = array($id_playlist);

    sqlsrv_query($conn, $sql, $params);

    header("Location: ../views/usuario_playlists.php?msg=eliminada");
    exit();
}
?>
