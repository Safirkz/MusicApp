<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/src/config/conexion.php';
require_once __DIR__ . '/src/Repositories/ArtistaRepository.php';
require_once __DIR__ . '/src/Templates/PublicarSencillo.php';
require_once __DIR__ . '/src/Templates/PublicarAlbum.php';

$artistaRepo = new ArtistaRepository($conn);
$idArtista = 1; // ID de prueba para la sesión de artista

// Cargar lista de álbumes del artista para el <select>
$albums = $artistaRepo->obtenerAlbumsPorArtista($idArtista);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $accion = $_POST['accion'] ?? '';

        // 1. PUBLICAR ÁLBUM
        if ($accion === 'publicar_album') {
            $publicador = new PublicarAlbum();

            $resultado = $publicador->publicar(
                [
                    'titulo'            => $_POST['titulo_album'],
                    'fecha_lanzamiento' => $_POST['fecha_lanzamiento'],
                    'id_artista'        => $_POST['id_artista'],
                    'tipo'              => $_POST['tipo']
                ],
                $_FILES['portada_album']
            );

            echo "<p style='color:green;'><b>¡Álbum creado exitosamente!</b> Portada: " . htmlspecialchars($resultado['portada']) . "</p>";

            // Actualizar desplegable tras crear
            $albums = $artistaRepo->obtenerAlbumsPorArtista($idArtista);
        }

        // 2. PUBLICAR CANCIÓN
        elseif ($accion === 'publicar_cancion') {
            $publicador = new PublicarSencillo();

            $resultado = $publicador->publicar(
                [
                    'titulo'     => $_POST['titulo_cancion'],
                    'duracion'   => $_POST['duracion'],
                    'id_album'   => $_POST['id_album'] ?: null,
                    'id_artista' => $_POST['id_artista']
                ],
                $_FILES['audio'],
                $_FILES['portada_cancion'] ?? null
            );

            echo "<p style='color:green;'><b>¡Canción publicada exitosamente!</b> Audio: " . htmlspecialchars($resultado['archivo_audio']) . "</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color:red;'><b>ERROR:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h2>Panel de Pruebas - Módulo Artista</h2>
<hr>

<!-- FORMULARIO 1: CREAR ÁLBUM -->
<div style="width: 48%; float: left; padding: 10px; border: 1px solid #ccc; box-sizing: border-box;">
    <h3>1. Crear Álbum</h3>
    <form action="test_publicacion.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="publicar_album">

        <label>Título del Álbum:</label><br>
        <input type="text" name="titulo_album" required><br><br>

        <label>ID Artista:</label><br>
        <input type="number" name="id_artista" value="<?php echo $idArtista; ?>" required><br><br>

        <label>Fecha Lanzamiento:</label><br>
        <input type="date" name="fecha_lanzamiento" value="<?php echo date('Y-m-d'); ?>" required><br><br>

        <label>Tipo:</label><br>
        <select name="tipo">
            <option value="album">album</option>
            <option value="sencillo">sencillo</option>
        </select><br><br>

        <label>Portada Álbum (.jpg, .png):</label><br>
        <input type="file" name="portada_album" accept="image/*" required><br><br>

        <button type="submit">Crear Álbum</button>
    </form>
</div>

<!-- FORMULARIO 2: PUBLICAR CANCIÓN -->
<div style="width: 48%; float: right; padding: 10px; border: 1px solid #ccc; box-sizing: border-box;">
    <h3>2. Publicar Canción</h3>
    <form action="test_publicacion.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="publicar_cancion">

        <label>Título de la Canción:</label><br>
        <input type="text" name="titulo_cancion" required><br><br>

        <label>ID Artista:</label><br>
        <input type="number" name="id_artista" value="<?php echo $idArtista; ?>" required><br><br>

        <label>Álbum (Opcional):</label><br>
        <select name="id_album">
            <option value="">-- Ninguno (Sencillo) --</option>
            <?php foreach ($albums as $album): ?>
                <option value="<?php echo $album['id_album']; ?>">
                    <?php echo htmlspecialchars($album['titulo']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Duración (ej. 00:03:30):</label><br>
        <input type="text" name="duracion" value="00:03:30" required><br><br>

        <label>Archivo Audio (.mp3):</label><br>
        <input type="file" name="audio" accept=".mp3" required><br><br>

        <label>Portada Canción (Opcional):</label><br>
        <input type="file" name="portada_cancion" accept="image/*"><br><br>

        <button type="submit">Publicar Canción</button>
    </form>
</div>

<div style="clear: both;"></div>