<?php

// Sesión
session_start();

// Conexión BD
include("../config/conexion.php");

// Datos formulario
$email = $_POST['email'];
$contrasena = $_POST['contrasena'];

// Consulta usuario
$sql = "SELECT id_usuario, contrasena, nombre, id_rol, activo
        FROM Usuario 
        WHERE email = ?";

// Parámetros
$params = [$email];

// Ejecutar consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Error SQL
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Verificar usuario
if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    // Usuario suspendido
    if ($row['activo'] == 0) {

        echo "Tu cuenta ha sido suspendida";

    } 
    // Verificar contraseña
    elseif (password_verify($contrasena, $row['contrasena'])) {
        // Variables de sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['id_rol'] = $row['id_rol'];

        // Redirección
        header("Location: ../index.php");
        exit();
    } else {
        // Contraseña incorrecta
        echo "Correo o contraseña incorrectos";
    }

} else {
    
    // Usuario inexistente
    echo "Usuario no encontrado";
}
?>
