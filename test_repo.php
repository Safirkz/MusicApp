<?php
// Ruta corregida según tu estructura
require_once __DIR__ . '/src/config/conexion.php'; 
require_once __DIR__ . '/src/repositories/ArtistaRepository.php';

try {
    if (!isset($conn) || !$conn) {
        throw new Exception("La variable \$conn no está definida o la conexión falló.");
    }

    $repo = new ArtistaRepository($conn);

    echo "<h3>=== PRUEBA 1: Obtener Artista por Usuario ===</h3>";
    $idUsuarioPrueba = 6; // ID de un usuario con perfil de artista en tu BD
    $idArtista = $repo->obtenerArtistaPorUsuario($idUsuarioPrueba);
    echo "ID Usuario enviado: <b>$idUsuarioPrueba</b><br>";
    echo "ID Artista retornado: <b>" . var_export($idArtista, true) . "</b><br><br>";

    if ($idArtista) {
        echo "<h3>=== PRUEBA 2: Crear Canción (Sencillo) ===</h3>";
        $exito = $repo->crearCancion(
            "Canción Test Repositorio",
            210,
            $idArtista,
            "test_audio.mp3",
            "test_portada.jpg",
            null
        );
        echo "Inserción de canción: " . ($exito ? "<b style='color:green;'>ÉXITO</b>" : "<b style='color:red;'>FALLO</b>") . "<br>";
    } else {
        echo "<p style='color:orange;'>Cambia \$idUsuarioPrueba en el script por un ID de usuario que exista en la base de datos.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'><b>Error atrapado:</b> " . $e->getMessage() . "</p>";
}