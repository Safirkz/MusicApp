<?php
// Cargar la conexión y las dos nuevas clases
require_once "src/config/conexion.php";
require_once "src/Repositories/ArtistaRepository.php";
require_once "src/Utils/FileSystemHandler.php";

echo "<h2>🧪 Prueba de Persistencia (BD + Archivos)</h2>";

try {
    // 1. Probar que el Repositorio se conecta a SQL Server
    $repo = new ArtistaRepository($conn);
    echo "<p style='color:green;'>✅ ArtistaRepository instanciado correctamente.</p>";

    // Cambia este '1' por el ID de un artista real que exista en tu BD
    $idPrueba = 1; 
    $imagen = $repo->obtenerImagenArtista($idPrueba);
    
    echo "<p style='color:green;'>✅ Consulta SQL ejecutada con éxito. Imagen obtenida: <strong>" . ($imagen ?? 'No tiene o ID no existe') . "</strong></p>";

    // 2. Probar que FileSystemHandler localiza las rutas de carpetas
    $fileHandler = new FileSystemHandler();
    echo "<p style='color:green;'>✅ FileSystemHandler instanciado correctamente.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error durante la prueba: " . $e->getMessage() . "</p>";
}