<?php
session_start();

// Seguridad extrema: Solo el admin puede manipular géneros
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../src/config/conexion.php");

// Recibimos el nombre del género desde el formulario
$nombre = $_POST['nombre_genero'] ?? null;

// Si el campo está vacío, regresamos a la vista
if (!$nombre || trim($nombre) == "") {
    header("Location: ../views/admin_gestion_generos.php?msg=vacio");
    exit();
}

$sql = "{CALL sp_InsertarGenero(?)}";
$params = array($nombre);
$stmt = sqlsrv_query($conn, $sql, $params);

// Si la ejecución falla a nivel de base de datos
if ($stmt === false) {
    // Puedes cambiar este die por un redireccionamiento a error si prefieres
    die(print_r(sqlsrv_errors(), true));
}

// Si todo salió bien, regresamos con mensaje de éxito
header("Location: ../views/admin_gestion_generos.php?msg=creado");
exit();
?>