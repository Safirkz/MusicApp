<?php
session_start();

// Validar que solo el Administrador pueda ejecutar acciones
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {

    header("Location: ../index.php");

    exit();
}

include("../src/config/conexion.php");

// Validar datos
if (
    !isset($_POST['id_cancion']) ||
    !isset($_POST['accion'])
) {

    header("Location: ../admin/admin_gestion_catalogo.php");

    exit();
}

$id_cancion = $_POST['id_cancion'];
$accion = $_POST['accion'];

// ==========================================
// INACTIVAR
// ==========================================

if ($accion == "inactivar") {

    $sql = "
    UPDATE Cancion
    SET activo = 0
    WHERE id_cancion = ?
    ";

    $params = array($id_cancion);

    sqlsrv_query($conn, $sql, $params);

    header("Location: ../admin/admin_cancion.php?id=$id_cancion&msg=inactivada");

    exit();
}

// ==========================================
// ACTIVAR
// ==========================================

if ($accion == "activar") {

    $sql = "
    UPDATE Cancion
    SET activo = 1
    WHERE id_cancion = ?
    ";

    $params = array($id_cancion);

    sqlsrv_query($conn, $sql, $params);

    header("Location: ../admin/admin_cancion.php?id=$id_cancion&msg=activada");

    exit();
}

// ==========================================
// ELIMINAR
// ==========================================

if ($accion == "eliminar") {

    $sql = "
    DELETE FROM Cancion
    WHERE id_cancion = ?
    ";
    $params = array($id_cancion);
    sqlsrv_query($conn, $sql, $params);
    header("Location: ../admin/admin_gestion_catalogo.php?filtro=cancion&msg=eliminada");
    exit();
}
?>