<?php
session_start();

// Seguridad extrema: Si no es admin, lo echamos
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../src/config/conexion.php");

// Recibimos los datos del formulario (los inputs hidden)
$id_usuario = $_POST['id_usuario'] ?? null;
$accion = $_POST['accion'] ?? null;

// Si falta algún dato o el admin intenta borrarse a sí mismo, regresamos
if (!$id_usuario || !$accion || $id_usuario == $_SESSION['id_usuario']) {
    header("Location: ../views/admin_gestion_usuarios.php");
    exit();
}

// Dependiendo de la acción, llamamos a un Procedimiento Almacenado u otro
if ($accion == 'inactivar') {
    // Llamar al SP para suspender
    $sql = "{CALL sp_InactivarUsuario(?)}";
    $params = [$id_usuario];
    $stmt = sqlsrv_query($conn, $sql, $params);
    $msg = "inactivado";

} elseif ($accion == 'activar') {
    // Llamar al SP para reactivar
    $sql = "{CALL sp_ActivarUsuario(?)}";
    $params = [$id_usuario];
    $stmt = sqlsrv_query($conn, $sql, $params);
    $msg = "activado";

} elseif ($accion == 'eliminar') {
    // Llamar al SP para borrar físicamente
    $sql = "{CALL sp_EliminarUsuario(?)}";
    $params = [$id_usuario];
    $stmt = sqlsrv_query($conn, $sql, $params);
    $msg = "eliminado";

} else if ($accion == "hacer_artista") {
    $sql = "EXEC sp_ConvertirEnArtista ?";
    $params = array($id_usuario);
    sqlsrv_query($conn, $sql, $params);
    header("Location: ../views/admin_gestion_usuarios.php?msg=artista");
    exit();
}

// Si la ejecución falló a nivel de base de datos
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Si todo salió bien, devolvemos al admin a la lista con un mensaje
header("Location: ../views/admin_gestion_usuarios.php?msg=" . $msg);
exit();
?>