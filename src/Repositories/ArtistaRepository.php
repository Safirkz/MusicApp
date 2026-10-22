<?php

class ArtistaRepository {
    private $conn; // Recurso de conexión de sqlsrv

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Crear un álbum invocando el procedimiento almacenado sp_CrearAlbum
     */
    public function crearAlbum($titulo, $fechaLanzamiento, $idArtista, $tipo, $nombreImagen) {
        $sql = "EXEC sp_CrearAlbum ?, ?, ?, ?, ?";
        $params = array($titulo, $fechaLanzamiento, $idArtista, $tipo, $nombreImagen);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if (!$stmt) {
            throw new Exception("Error al crear álbum: " . print_r(sqlsrv_errors(), true));
        }
        return true;
    }

    /**
     * Consultar la imagen actual del perfil de un artista
     */
    public function obtenerImagenArtista($idArtista) {
        $sql = "SELECT imagen_url FROM Artista WHERE id_artista = ?";
        $params = array($idArtista);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return $row['imagen_url'];
        }
        return null;
    }

    /**
     * Actualizar perfil de artista invocando sp_ActualizarPerfilArtista
     */
    public function actualizarPerfilArtista($idArtista, $nombreArtistico, $nacionalidad, $descripcion, $nombreImagen) {
        $sql = "EXEC sp_ActualizarPerfilArtista ?, ?, ?, ?, ?";
        $params = array($idArtista, $nombreArtistico, $nacionalidad, $descripcion, $nombreImagen);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if (!$stmt) {
            throw new Exception("Error al actualizar perfil: " . print_r(sqlsrv_errors(), true));
        }
        return true;
    }

    /**
     * Activar o inactivar una canción
     */
    public function cambiarEstadoCancion($idCancion, $activo) {
        $sql = "UPDATE Cancion SET activo = ? WHERE id_cancion = ?";
        $params = array($activo, $idCancion);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if (!$stmt) {
            throw new Exception("Error al cambiar estado de la canción: " . print_r(sqlsrv_errors(), true));
        }
        return true;
    }

    /**
     * Eliminar físicamente una canción de la BD
     */
    public function eliminarCancion($idCancion) {
        $sql = "DELETE FROM Cancion WHERE id_cancion = ?";
        $params = array($idCancion);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if (!$stmt) {
            throw new Exception("Error al eliminar la canción: " . print_r(sqlsrv_errors(), true));
        }
        return true;
    }

    /**
     * Crear una canción o sencillo invocando sp_CrearCancion
     */
    public function crearCancion($titulo, $duracion, $idAlbum, $idArtista, $nombreAudio, $nombreImagen) {
        $sql = "EXEC sp_CrearCancion ?, ?, ?, ?, ?, ?";
        $params = array($titulo, $duracion, $idAlbum, $idArtista, $nombreAudio, $nombreImagen);
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if (!$stmt) {
            throw new Exception("Error al crear la canción: " . print_r(sqlsrv_errors(), true));
        }
        return true;
    }
}

