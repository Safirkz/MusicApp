<?php
session_start();

// ==========================================
// VALIDAR SESIÓN
// ==========================================

if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

include("../src/config/conexion.php");

// ==========================================
// DATOS
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$accion = $_POST['accion'] ?? "";

$id_playlist = $_POST['id_playlist'] ?? 0;

$id_cancion = $_POST['id_cancion'] ?? 0;

// ==========================================
// AGREGAR CANCIÓN A PLAYLIST
// ==========================================

if ($accion == "agregar") {

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

        header("Location: ../views/usuario.php");
        exit();
    }

    // ==========================================
    // AGREGAR CANCIÓN
    // ==========================================

    $sql = "EXEC sp_AgregarCancionPlaylist ?, ?";

    $params = array(
        $id_playlist,
        $id_cancion
    );

    sqlsrv_query(
        $conn,
        $sql,
        $params
    );

    // ==========================================
    // REDIRECCIONAR
    // ==========================================

    header("Location: ../views/usuario_playlist.php?id=$id_playlist&msg=agregada");
    exit();
}

// ==========================================
// QUITAR CANCIÓN DE PLAYLIST
// ==========================================

if ($accion == "quitar") {

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

        header("Location: ../views/usuario.php");
        exit();
    }

    // ==========================================
    // QUITAR CANCIÓN
    // ==========================================

    $sql = "EXEC sp_QuitarCancionPlaylist ?, ?";

    $params = array(
        $id_playlist,
        $id_cancion
    );

    sqlsrv_query(
        $conn,
        $sql,
        $params
    );

    // ==========================================
    // REDIRECCIONAR
    // ==========================================

    header("Location: ../views/usuario_playlist.php?id=$id_playlist&msg=eliminada");
    exit();
}
?>