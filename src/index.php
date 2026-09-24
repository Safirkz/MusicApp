<?php
/*
session_start();

// Si ya inició sesión
if (isset($_SESSION['id_usuario'])) {

    // Redirigir según rol
    if ($_SESSION['id_rol'] == 1) {
        header("Location: views/admin.php");
    } elseif ($_SESSION['id_rol'] == 3) {
        header("Location: views/artista.php");
    } else {
        header("Location: views/usuario.php");
    }

    exit();
}

// Si NO ha iniciado sesión → ir al login
header("Location: auth/login.php");
exit();
*/
?>

<?php
// 1. session_start() es una FUNCION NATIVA muy importante. 
// Le dice a PHP: "Abre la memoria temporal del servidor para recordar a este usuario".
session_start();

// 2. isset() es otra FUNCION NATIVA que pregunta: "¿Existe esto?".
// $_SESSION es una variable "superglobal" de PHP (un arreglo) que guarda datos temporales.
// Aquí preguntamos: ¿Existe un 'id_usuario' guardado en la memoria de la sesión?
if (isset($_SESSION['id_usuario'])) {

    // 3. Si la respuesta es SÍ, verificamos qué rol tiene. (1 = Admin, 3 = Artista, etc.)
    // "==" significa "igual a".
    if ($_SESSION['id_rol'] == 1) {
        // 4. header() es una FUNCION NATIVA que envía órdenes al navegador.
        // "Location: ..." le dice al navegador que se teletransporte a esa URL.
        header("Location: views/admin.php");
    } elseif ($_SESSION['id_rol'] == 3) {
        header("Location: views/artista.php");
    } else {
        header("Location: views/usuario.php"); // Si no es 1 ni 3, por descarte es un usuario normal (2)
    }

    // 5. exit() detiene la ejecución. Es una buena práctica después de un header() 
    // para asegurar que el código de abajo no se ejecute por accidente.
    exit();
}

// 6. Si el "if" de arriba fue FALSO (no hay sesión), el código sigue hasta aquí.
// Redirigimos al usuario a la pantalla para que inicie sesión.
header("Location: auth/login.php");
exit();
?>
