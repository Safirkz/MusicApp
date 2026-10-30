<?php

class ArtistaRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Consulta el ID del artista por su ID de usuario.
     */
    public function obtenerArtistaPorUsuario($idUsuario) {
        $sql = "EXEC sp_ObtenerArtistaPorUsuario ?";
        $params = array((int)$idUsuario);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            throw new Exception("Error al consultar artista: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);

        return $row['id_artista'] ?? null;
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
     * Obtiene los álbumes activos de un artista para alimentar el <select>.
     */
    public function obtenerAlbumsPorArtista($idArtista) {
        $sql = "SELECT id_album, titulo FROM Album WHERE id_artista = ? AND activo = 1";
        $params = array((int)$idArtista);
        $stmt = sqlsrv_query($this->conn, $sql, $params);

        if ($stmt === false) {
            return [];
        }

        $albums = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $albums[] = $row;
        }

        sqlsrv_free_stmt($stmt);
        return $albums;
    }
}
