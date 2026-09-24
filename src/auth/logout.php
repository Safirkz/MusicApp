<?php
/*
session_start();

session_destroy();

header("Location: login.php");
exit();
*/
?>

<?php
// Reanudamos la sesión actual para saber a qué usuario estamos afectando
session_start();

// Destruimos completamente la sesión en el servidor
session_destroy();

// Redirigimos al usuario de vuelta a la pantalla de login
header("Location: login.php");
exit();
?>