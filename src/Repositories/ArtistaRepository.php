<?php

class ArtistaRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Registra un álbum en SQL Server.
     * SP: 1. @titulo, 2. @fecha_lanzamiento, 3. @id_artista, 4. @tipo, 5. @imagen_url
     */
    public function crearAlbum($titulo, $fechaLanzamiento, $idArtista, $tipo, $imagenUrl) {
        $sql = "EXEC sp_CrearAlbum ?, ?, ?, ?, ?";
        
        $tipoNormalizado = strtolower(trim($tipo ?? ''));
        if (!in_array($tipoNormalizado, ['album', 'sencillo'])) {
            $tipoNormalizado = 'album';
        }

        $params = array(
            $titulo,
            $fechaLanzamiento,
            (int)$idArtista,
            $tipoNormalizado,
            $imagenUrl
        );

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception("Error al crear álbum: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);
        return true;
    }

    /**
     * Modifica el estado activo/inactivo de un album.
     */
    public function cambiarEstadoAlbum($idAlbum, $nuevoEstado) {
        $sql = "{CALL sp_CambiarEstadoAlbum(?, ?)}";
        $params = array(
            array($idAlbum, SQLSRV_PARAM_IN),
            array($nuevoEstado, SQLSRV_PARAM_IN)
        );
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        if ($stmt === false) {
            throw new Exception("Error al actualizar el estado del álbum: " . print_r(sqlsrv_errors(), true));
        }
        
        return true;
    }

    /**
     * Registra una canción en SQL Server.
     * SP: 1. @titulo, 2. @duracion, 3. @id_album, 4. @id_artista, 5. @archivo_url, 6. @imagen_url
     */
    public function crearCancion($titulo, $duracion, $idAlbum, $idArtista, $archivoUrl, $imagenUrl) {
        $sql = "EXEC sp_CrearCancion ?, ?, ?, ?, ?, ?";
        
        $duracionVal  = (int)$duracion;
        $idArtistaVal = (int)$idArtista;
        $idAlbumVal   = ($idAlbum !== null && $idAlbum !== '' && $idAlbum !== 0) ? (int)$idAlbum : null;

        $params = array(
            $titulo,
            $duracionVal,
            $idAlbumVal,
            $idArtistaVal,
            $archivoUrl,
            $imagenUrl
        );

        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception("Error al crear canción: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);
        return true;
    }

    /**
     * Modifica el estado activo/inactivo de una canción.
     */
    public function cambiarEstadoCancion($idCancion, $estado) {
        $sql = "{CALL sp_CambiarEstadoCancion(?, ?)}";
        $params = array(
            array((int)$idCancion, SQLSRV_PARAM_IN),
            array((int)$estado, SQLSRV_PARAM_IN)
        );
        
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception("Error al cambiar estado de la canción: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);
        return true;
    }

    /**
     * Elimina físicamente un registro de canción por su ID.
     */
    public function eliminarCancion($idCancion) {
        $sql = "DELETE FROM Cancion WHERE id_cancion = ?";
        $params = array((int)$idCancion);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception("Error al eliminar la canción: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmt);
        return true;
    }


    // ==========================================
    // CONSULTAS DE LECTURA (STORED PROCEDURES)
    // ==========================================

    /**
     * Obtiene el perfil completo del artista a partir del ID del usuario logueado.
     */
    public function obtenerArtistaPorUsuario($idUsuario) {
        $sql = "EXEC sp_ObtenerArtistaPorUsuario ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idUsuario));
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($stmt !== false) sqlsrv_free_stmt($stmt);
        return $row ?: null;
    }

    /**
     * Lista todos los álbumes de tipo 'album' y activos pertenecientes al artista.
     */
    public function obtenerAlbumesArtista($idArtista) {
        $sql = "EXEC sp_ObtenerAlbumesArtista ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idArtista));
        $items = array();
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        return $items;
    }

    /**
     * Lista las canciones que son sencillos independientes (sin álbum) del artista.
     */
    public function obtenerSencillosArtista($idArtista) {
        $sql = "EXEC sp_ObtenerSencillosArtista ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idArtista));
        $items = array();
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        return $items;
    }

    /**
     * Obtiene los datos detallados de un álbum por su ID.
     */
    public function obtenerAlbumPorId($idAlbum) {
        $sql = "EXEC sp_ObtenerAlbumPorId ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idAlbum));
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($stmt !== false) sqlsrv_free_stmt($stmt);
        return $row ?: null;
    }

    /**
     * Obtiene todas las canciones vinculadas a un álbum específico.
     */
    public function obtenerCancionesAlbum($idAlbum) {
        $sql = "EXEC sp_ObtenerCancionesAlbum ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idAlbum));
        $items = array();
        if ($stmt !== false) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $items[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        }
        return $items;
    }

    /**
     * Obtiene la información detallada de una canción por su ID.
     */
    public function obtenerCancionPorId($idCancion) {
        $sql = "EXEC sp_ObtenerCancionPorId ?";
        $stmt = sqlsrv_query($this->conn, $sql, array((int)$idCancion));
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($stmt !== false) sqlsrv_free_stmt($stmt);
        return $row ?: null;
    }
}
