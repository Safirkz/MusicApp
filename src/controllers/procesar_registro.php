<?php
// Incluimos la conexión
include("../src/config/conexion.php");

// Recibimos los datos del formulario (registro.php)
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$contrasena = $_POST['contrasena'];
$id_rol = 2; // Le asignamos el rol 2 (Usuario/Oyente) por defecto

// --- NUEVO: Encriptamos la contraseña antes de guardarla ---
$hash_seguro = password_hash($contrasena, PASSWORD_DEFAULT);

// 1. VALIDACIÓN: Revisar si el correo ya existe en la base de datos
// (Esto evita un error de SQL porque en tu tabla pusiste UNIQUE en el email)
$sql_check = "SELECT id_usuario FROM Usuario WHERE email = ?";
$stmt_check = sqlsrv_query($conn, $sql_check, [$email]);

// Si sqlsrv_fetch_array encuentra algo, significa que el correo ya está en uso
if (sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC)) {
    echo "El correo ya está registrado. <a href='registro.php'>Volver a intentar</a>";
    exit();
}

// 2. INSERCIÓN: Si el correo es nuevo, guardamos al usuario
// --- MODIFICADO: Pasamos la variable $hash_seguro en lugar de $contrasena ---
$sql_insert = "INSERT INTO Usuario (nombre, email, contrasena, id_rol, activo) 
               VALUES (?, ?, ?, ?, 1)";

$params = [$nombre, $email, $hash_seguro, $id_rol];
$stmt_insert = sqlsrv_query($conn, $sql_insert, $params);

// 3. REDIRECCIÓN: Si todo salió bien, lo mandamos al login con un mensaje de éxito
if ($stmt_insert) {
    // Le pasamos una variable por la URL (?mensaje=exito) para que el login sepa qué mostrar
    header("Location: ../auth/login.php?mensaje=exito");
    exit();
} else {
    // Si algo falla a nivel de base de datos, mostramos el error
    die(print_r(sqlsrv_errors(), true));
}
?>