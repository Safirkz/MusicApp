<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {

    exit();
}

include("../src/config/conexion.php");

$id_usuario = $_SESSION['id_usuario'];

$id_cancion = $_POST['id_cancion'] ?? 0;

if ($id_cancion > 0) {

    $sql = "
    INSERT INTO Historial_Reproduccion (
        id_usuario,
        id_cancion
    )
    VALUES (?, ?)
    ";

    $params = array(
        $id_usuario,
        $id_cancion
    );

    sqlsrv_query($conn, $sql, $params);
}
?>