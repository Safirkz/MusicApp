
<?php
/*
$serverName = "localhost\\SQLEXPRESS";

$connectionOptions = [
    "Database" => "MusicApp",
    "CharacterSet" => "UTF-8",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
*/
?>


<?php
// 1. Definimos el servidor al que nos vamos a conectar. 
// "localhost" significa tu propia computadora. "\\SQLEXPRESS" es la instancia de tu base de datos.
// (Se usa doble barra \\ porque en programación una sola barra \ a veces significa un "escape" de texto).
$serverName = "localhost\\SQLEXPRESS";

// 2. Creamos un "Arreglo" (Array) con las opciones de conexión. 
// El símbolo "=>" se lee como "apunta a" o "su valor es". 
// Ejemplo: La llave "Database" tiene el valor "MusicApp".
$connectionOptions = [
    "Database" => "MusicApp",    // El nombre de tu base de datos
    "CharacterSet" => "UTF-8",   // Para que acepte acentos y la letra ñ sin errores
    "Uid" => "",                 // User ID (Usuario de SQL Server, vacío si usas autenticación de Windows)
    "PWD" => ""                  // Password (Contraseña de SQL Server)
];

// 3. sqlsrv_connect() es una FUNCION NATIVA de PHP. 
// Intenta hacer la llamada a SQL Server enviándole el servidor y las opciones. 
// El resultado de esa llamada (la conexión) se guarda en la variable $conn.
$conn = sqlsrv_connect($serverName, $connectionOptions);

// 4. Verificamos si hubo un error. 
// "===" significa "estrictamente igual a". Si la conexión dio "false" (falso/falló)...
if ($conn === false) {
    // 5. die() es una función de PHP que "mata" o detiene todo el proceso.
    // print_r() imprime información de manera legible.
    // sqlsrv_errors() es una función nativa que nos dice exactamente POR QUÉ falló SQL Server.
    die(print_r(sqlsrv_errors(), true));
}
?>