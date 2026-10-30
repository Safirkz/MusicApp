<?php
require_once __DIR__ . '/src/utils/FileSystemHandler.php';

$fs = new FileSystemHandler();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    try {
        $tipo = $_POST['tipo_destino'] ?? 'audio';

        switch ($tipo) {
            case 'album':
                $nombre = $fs->guardarPortadaAlbum($_FILES['archivo']);
                $ubicacion = "resources/img/albums/";
                break;
            case 'artista':
                $nombre = $fs->guardarFotoArtista($_FILES['archivo']);
                $ubicacion = "resources/img/artistas/";
                break;
            case 'cancion':
                $nombre = $fs->guardarPortadaCancion($_FILES['archivo']);
                $ubicacion = "resources/img/canciones/";
                break;
            case 'playlist':
                $nombre = $fs->guardarPortadaPlaylist($_FILES['archivo']);
                $ubicacion = "resources/img/playlists/";
                break;
            default:
                $nombre = $fs->guardarAudio($_FILES['archivo']);
                $ubicacion = "resources/audio/";
                break;
        }

        $rutaAbsoluta = realpath(__DIR__ . '/' . $ubicacion . $nombre);

        echo "<p style='color:green;'><b>ÉXITO:</b> Archivo <i>'$nombre'</i> guardado correctamente.</p>";
        echo "<b>Ubicación en el proyecto:</b> $ubicacion<br>";
        echo "<b>Ruta absoluta real:</b> <code style='color:blue;'>$rutaAbsoluta</code>";

    } catch (Exception $e) {
        echo "<p style='color:red;'><b>ERROR:</b> " . $e->getMessage() . "</p>";
    }
}
?>

<h2>Prueba de Subida a Resources</h2>
<form action="test_filesystem.php" method="POST" enctype="multipart/form-data">
    <label>Elige la carpeta destino según tu estructura:</label><br><br>
    <select name="tipo_destino" required>
        <option value="audio">Audio (resources/audio/)</option>
        <option value="album">Portada Álbum (resources/img/albums/)</option>
        <option value="artista">Foto Artista (resources/img/artistas/)</option>
        <option value="cancion">Portada Canción (resources/img/canciones/)</option>
        <option value="playlist">Portada Playlist (resources/img/playlists/)</option>
    </select>
    <br><br>
    <input type="file" name="archivo" required>
    <br><br>
    <button type="submit">Subir Archivo</button>
</form>