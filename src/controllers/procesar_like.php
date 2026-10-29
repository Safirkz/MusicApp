<?php
session_start();

include("../config/conexion.php");

// ==========================================
// VALIDAR SESIÓN
// ==========================================

if (!isset($_SESSION['id_usuario'])) {

    header("Location: ../auth/login.php");
    exit();
}

// ==========================================
// RECIBIR DATOS
// ==========================================

$id_usuario = $_SESSION['id_usuario'];

$id_cancion = $_POST['id_cancion'];

// ==========================================
// EJECUTAR SP
// ==========================================

$sql = "EXEC sp_ToggleLikeCancion ?, ?";

$params = array(
    $id_usuario,
    $id_cancion
);

$stmt = sqlsrv_query($conn, $sql, $params);

// ==========================================
// REDIRECCIÓN
// ==========================================

if ($stmt) {

    // Volver a la página anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

} else {

    die(print_r(sqlsrv_errors(), true));
}
?>