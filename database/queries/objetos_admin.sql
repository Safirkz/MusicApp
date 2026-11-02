/*
|||  |=================================================================================================|  |||
|||  |$$$$$$$$$$$$$$$$$$$$$$  SCRIPT DE OBJETOS PARA EL MÓDULO DE ADMINISTRADOR  $$$$$$$$$$$$$$$$$$$$$$|  |||
|||  |=================================================================================================|  |||
*/
-- --------------------------------------------------------------------
-- VISTA: Para listar usuarios con el nombre de su rol
-- --------------------------------------------------------------------

CREATE VIEW vw_ListaUsuarios AS
SELECT 
    u.id_usuario,
    u.nombre AS NombreUsuario,
    u.email,
    u.activo,
    r.id_rol,
    r.nombre_rol AS NombreRol
FROM 
    Usuario u
INNER JOIN 
    Rol r ON u.id_rol = r.id_rol;
GO


-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: BORRADO LÓGICO (Suspender / Inactivar)
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_InactivarUsuario
    @id_usuario INT
AS
BEGIN
    -- Cambiamos el estado a 0 (Inactivo) en lugar de borrarlo
    UPDATE Usuario 
    SET activo = 0 
    WHERE id_usuario = @id_usuario;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: REACTIVAR USUARIO (Quitar suspensión)
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_ActivarUsuario
    @id_usuario INT
AS
BEGIN
    -- Cambiamos el estado a 1 (Activo)
    UPDATE Usuario 
    SET activo = 1 
    WHERE id_usuario = @id_usuario;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: BORRADO FÍSICO (Eliminación total)
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_EliminarUsuario
    @id_usuario INT
AS
BEGIN
    -- Precaución: Esto eliminará el registro físicamente de la tabla.
    -- (Nota: Si hay tablas hijas como 'Playlist' o 'Historial' conectadas
    -- con llaves foráneas SIN 'ON DELETE CASCADE', este delete podría fallar.
    DELETE FROM Usuario 
    WHERE id_usuario = @id_usuario;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: INSERTAR GENERO 
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_InsertarGenero
    @nombre VARCHAR(50)
AS
BEGIN
    INSERT INTO Genero (nombre) VALUES (@nombre);
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: ELIMINAR GENERO (Eliminación total)
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_EliminarGenero
    @id_genero INT
AS
BEGIN
    DELETE FROM Genero WHERE id_genero = @id_genero;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: INSERTAR CANCION (Usado por admin y artista)
-- --------------------------------------------------------------------

-- Creamos el nuevo con tus campos reales
CREATE PROCEDURE sp_InsertarCancion
    @titulo NVARCHAR(150),
    @duracion INT,
    @id_album INT = NULL, -- Puede ser nulo si es un sencillo
    @id_artista INT,
    @archivo_url NVARCHAR(255),
    @imagen_url NVARCHAR(255) = NULL
AS
BEGIN
    INSERT INTO cancion (titulo, duracion, id_album, id_artista, archivo_url, fecha_subida, imagen_url, activo)
    VALUES (@titulo, @duracion, @id_album, @id_artista, @archivo_url, GETDATE(), @imagen_url, 1);
END
GO
select * from Cancion

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER LISTA DE ARTISTAS CON INFO EXTRA
-- --------------------------------------------------------------------
CREATE PROCEDURE sp_ObtenerCatalogoArtistas
AS
BEGIN
    SELECT 
        a.id_artista,
        a.nombre_artistico,
        a.nacionalidad,
        a.descripcion,
        a.imagen_url,

        COUNT(DISTINCT al.id_album) AS total_albumes,
        COUNT(DISTINCT s.id_cancion) AS total_sencillos,
        COUNT(DISTINCT c.id_cancion) AS total_canciones

    FROM Artista a
    -- Contar álbumes
    LEFT JOIN Album al
        ON a.id_artista = al.id_artista
        AND al.activo = 1
        AND al.tipo = 'album'
    -- Contar sencillos
    LEFT JOIN Cancion s
        ON a.id_artista = s.id_artista
        AND s.id_album IS NULL
        AND s.activo = 1
    -- Contar todas las canciones
    LEFT JOIN Cancion c
        ON a.id_artista = c.id_artista
        AND c.activo = 1
    WHERE a.activo = 1
    GROUP BY
        a.id_artista,
        a.nombre_artistico,
        a.nacionalidad,
        a.descripcion,
        a.imagen_url

    ORDER BY a.nombre_artistico ASC;
END;
GO

ALTER PROCEDURE sp_ObtenerCatalogoArtistas
    @buscar VARCHAR(100) = ''
AS
BEGIN
    SELECT 
        a.id_artista,
        a.nombre_artistico,
        a.nacionalidad,
        a.descripcion,
        a.imagen_url,
        COUNT(DISTINCT al.id_album) AS total_albumes,
        COUNT(DISTINCT s.id_cancion) AS total_sencillos,
        COUNT(DISTINCT c.id_cancion) AS total_canciones
    FROM Artista a
    LEFT JOIN Album al
        ON a.id_artista = al.id_artista
        AND al.activo = 1
        AND al.tipo = 'album'
    LEFT JOIN Cancion s
        ON a.id_artista = s.id_artista
        AND s.id_album IS NULL
        AND s.activo = 1
    LEFT JOIN Cancion c
        ON a.id_artista = c.id_artista
        AND c.activo = 1
    WHERE a.activo = 1
    AND (
        @buscar = ''
        OR a.nombre_artistico LIKE '%' + @buscar + '%'
        OR a.nacionalidad LIKE '%' + @buscar + '%'
    )
    GROUP BY
        a.id_artista,
        a.nombre_artistico,
        a.nacionalidad,
        a.descripcion,
        a.imagen_url
    ORDER BY a.nombre_artistico ASC;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER DETALLES DEL ARTISTA
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_ObtenerDetalleArtista
    @id_artista INT
AS
BEGIN

    SELECT 
        id_artista,
        nombre_artistico,
        nacionalidad,
        descripcion,
        imagen_url
    FROM Artista
    WHERE id_artista = @id_artista;

END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER ALBUMES DEL ARTISTA
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_ObtenerAlbumesArtista
    @id_artista INT
AS
BEGIN

    SELECT *
    FROM Album
    WHERE id_artista = @id_artista
    AND activo = 1
    AND tipo = 'album'
    ORDER BY fecha_lanzamiento DESC;

END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER LISTA DE ARTISTAS CON INFO EXTRA
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_ObtenerSencillosArtista
    @id_artista INT
AS
BEGIN

    SELECT *
    FROM Cancion
    WHERE id_artista = @id_artista
    AND id_album IS NULL
    AND activo = 1
    ORDER BY fecha_subida DESC;

END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER LISTA CANCIONES
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_BuscarCancionesCatalogo
    @buscar VARCHAR(100) = ''
AS
BEGIN

    SELECT 
        c.id_cancion,
        c.titulo AS titulo_cancion,
        c.duracion,
        c.imagen_url,
        c.archivo_url,
        a.nombre_artistico,
        al.titulo AS titulo_album
    FROM Cancion c
    INNER JOIN Artista a
        ON c.id_artista = a.id_artista
    LEFT JOIN Album al
        ON c.id_album = al.id_album
    WHERE c.activo = 1
    AND (
        @buscar = ''
        OR c.titulo LIKE '%' + @buscar + '%'
        OR a.nombre_artistico LIKE '%' + @buscar + '%'
        OR al.titulo LIKE '%' + @buscar + '%'
    )

    ORDER BY c.fecha_subida DESC;

END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER LISTA CANCIONES PARA EL USUARIO
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_BuscarCancionesUsuario
    @buscar VARCHAR(100),
    @id_usuario INT
AS
BEGIN
    SELECT
        c.id_cancion,
        c.titulo AS titulo_cancion,
        c.duracion,
        c.archivo_url,
        c.imagen_url,
        a.nombre_artistico,
        al.titulo AS titulo_album,
        CASE
            WHEN lc.id_usuario IS NOT NULL THEN 1
            ELSE 0
        END AS tiene_like
    FROM Cancion c
    INNER JOIN Artista a
        ON c.id_artista = a.id_artista
    LEFT JOIN Album al
        ON c.id_album = al.id_album
    LEFT JOIN Like_Cancion lc
        ON c.id_cancion = lc.id_cancion
        AND lc.id_usuario = @id_usuario
    WHERE
        c.activo = 1
        AND (
            c.titulo LIKE '%' + @buscar + '%'
            OR a.nombre_artistico LIKE '%' + @buscar + '%'
            OR al.titulo LIKE '%' + @buscar + '%'
        )
    ORDER BY c.fecha_subida DESC
END
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER LISTA ALBUMES
-- --------------------------------------------------------------------

CREATE PROCEDURE sp_BuscarAlbumesCatalogo
    @buscar VARCHAR(100) = ''
AS
BEGIN
    SELECT 
        al.id_album,
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        a.nombre_artistico,

        COUNT(c.id_cancion) AS total_canciones

    FROM Album al
    INNER JOIN Artista a
        ON al.id_artista = a.id_artista
    LEFT JOIN Cancion c
        ON al.id_album = c.id_album
        AND c.activo = 1
    WHERE al.activo = 1
    AND (
        @buscar = ''
        OR al.titulo LIKE '%' + @buscar + '%'
        OR a.nombre_artistico LIKE '%' + @buscar + '%'
    )
    GROUP BY
        al.id_album,
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        a.nombre_artistico
    ORDER BY al.fecha_lanzamiento DESC;
END;
GO

-- --------------------------------------------------------------------
--  PROCEDIMIENTO ALMACENADO: OBTENER CANCION POR ID
-- --------------------------------------------------------------------
CREATE PROCEDURE sp_ObtenerCancionPorId
    @id_cancion INT
AS
BEGIN
    SELECT 
        c.id_cancion,
        c.id_artista,
        c.titulo,
        c.duracion,
        c.archivo_url,
        c.imagen_url,
        c.fecha_subida,
        c.activo,
        a.nombre_artistico,
        al.titulo AS titulo_album
    FROM Cancion c
    INNER JOIN Artista a
        ON c.id_artista = a.id_artista
    LEFT JOIN Album al
        ON c.id_album = al.id_album
    WHERE c.id_cancion = @id_cancion;
END;
GO

-- ==================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER INFORMACIÓN COMPLETA DE UN ÁLBUM
-- ==================================================================

CREATE PROCEDURE sp_ObtenerAlbumPorId
    @id_album INT
AS
BEGIN
    SELECT 
        al.id_album,
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        al.activo,
        a.nombre_artistico,
        COUNT(c.id_cancion) AS total_canciones
    FROM Album al
    INNER JOIN Artista a
        ON al.id_artista = a.id_artista
    LEFT JOIN Cancion c
        ON al.id_album = c.id_album
    WHERE al.id_album = @id_album
    GROUP BY
        al.id_album,
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        al.activo,
        a.nombre_artistico;
END;
GO

ALTER PROCEDURE sp_ObtenerAlbumPorId
    @id_album INT
AS
BEGIN
    SELECT 
        al.id_album,
        al.id_artista, -- <-- Se incluye id_artista
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        al.activo,
        a.nombre_artistico,
        COUNT(c.id_cancion) AS total_canciones
    FROM Album al
    INNER JOIN Artista a 
        ON al.id_artista = a.id_artista
    LEFT JOIN Cancion c 
        ON al.id_album = c.id_album
    WHERE al.id_album = @id_album
    GROUP BY 
        al.id_album,
        al.id_artista, -- <-- Se incluye en el GROUP BY
        al.titulo,
        al.fecha_lanzamiento,
        al.imagen_url,
        al.activo,
        a.nombre_artistico;
END;
GO


-- =======================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER CANCIONES DE UN ÁLBUM
-- =======================================================

CREATE PROCEDURE sp_ObtenerCancionesAlbum
    @id_album INT
AS
BEGIN
    SELECT 
        id_cancion,
        titulo,
        duracion,
        archivo_url,
        imagen_url,
        activo
    FROM Cancion
    WHERE id_album = @id_album
    ORDER BY titulo ASC;
END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: CONVERTIR USUARIO EN ARTISTA
-- =======================================================

CREATE PROCEDURE sp_ConvertirEnArtista
    @id_usuario INT
AS
BEGIN

    -- Cambiar rol del usuario
    UPDATE Usuario
    SET id_rol = 3
    WHERE id_usuario = @id_usuario;

    -- Crear perfil de artista
    INSERT INTO Artista (
        id_usuario,
        nombre_artistico,
        activo
    )
    SELECT
        id_usuario,
        nombre,
        1
    FROM Usuario
    WHERE id_usuario = @id_usuario;

END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER INFO DEL ARTISTA 
-- =======================================================
CREATE PROCEDURE sp_ObtenerArtistaPorUsuario
    @id_usuario INT
AS
BEGIN

    SELECT *
    FROM Artista
    WHERE id_usuario = @id_usuario;

END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: EDITAR ARTISTA 
-- =======================================================

CREATE PROCEDURE sp_ActualizarPerfilArtista
    @id_artista INT,
    @nombre_artistico VARCHAR(100),
    @nacionalidad VARCHAR(50),
    @descripcion VARCHAR(300),
    @imagen_url VARCHAR(255)
AS
BEGIN

    UPDATE Artista
    SET
        nombre_artistico = @nombre_artistico,
        nacionalidad = @nacionalidad,
        descripcion = @descripcion,
        imagen_url = @imagen_url
    WHERE id_artista = @id_artista;

END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: CREAR ALBUM
-- =======================================================

CREATE PROCEDURE sp_CrearAlbum
    @titulo VARCHAR(150),
    @fecha_lanzamiento DATE,
    @id_artista INT,
    @tipo VARCHAR(20),
    @imagen_url VARCHAR(255)
AS
BEGIN
    INSERT INTO Album (
        titulo,
        fecha_lanzamiento,
        id_artista,
        tipo,
        imagen_url,
        activo
    )
    VALUES (
        @titulo,
        @fecha_lanzamiento,
        @id_artista,
        @tipo,
        @imagen_url,
        1
    );
END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: CAMBIAR ESTADO ALBUM
-- =======================================================

CREATE PROCEDURE sp_CambiarEstadoAlbum
    @id_album INT,
    @activo BIT
AS
BEGIN
    UPDATE Album
    SET activo = @activo
    WHERE id_album = @id_album;
END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: CREAR CANCION
-- =======================================================

CREATE PROCEDURE sp_CrearCancion
    @titulo VARCHAR(150),
    @duracion INT,
    @id_album INT = NULL,
    @id_artista INT,
    @archivo_url VARCHAR(255),
    @imagen_url VARCHAR(255)
AS
BEGIN
    INSERT INTO Cancion (
        titulo,
        duracion,
        id_album,
        id_artista,
        archivo_url,
        imagen_url,
        activo
    )
    VALUES (
        @titulo,
        @duracion,
        @id_album,
        @id_artista,
        @archivo_url,
        @imagen_url,
        1

    );
END;
GO

-- =======================================================
-- PROCEDIMIENTO ALMACENADO: CAMBIAR ESTADO CANCION
-- =======================================================

CREATE PROCEDURE sp_CambiarEstadoCancion
    @id_cancion INT,
    @activo BIT
AS
BEGIN
    UPDATE Cancion
    SET activo = @activo
    WHERE id_cancion = @id_cancion;
END;
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: GESTIONA LOS LIKES Y LA PLAYLIST 'TUS LIKES'
-- ======================================================================

CREATE PROCEDURE sp_ToggleLikeCancion

    @id_usuario INT,
    @id_cancion INT

AS
BEGIN

    SET NOCOUNT ON;
    DECLARE @id_playlist INT;

    -- ==========================================
    -- VERIFICAR SI YA EXISTE LIKE
    -- ==========================================

    IF EXISTS (
        SELECT 1
        FROM Like_Cancion
        WHERE id_usuario = @id_usuario
        AND id_cancion = @id_cancion
    )

    BEGIN

        -- ==========================================
        -- QUITAR LIKE
        -- ==========================================

        DELETE FROM Like_Cancion
        WHERE id_usuario = @id_usuario
        AND id_cancion = @id_cancion;

        -- ==========================================
        -- OBTENER PLAYLIST "TUS ME GUSTA"
        -- ==========================================

        SELECT @id_playlist = id_playlist
        FROM Playlist
        WHERE id_usuario = @id_usuario
        AND es_favoritos = 1;

        -- ==========================================
        -- ELIMINAR DE PLAYLIST
        -- ==========================================

        DELETE FROM Playlist_Cancion
        WHERE id_playlist = @id_playlist
        AND id_cancion = @id_cancion;

    END

    ELSE

    BEGIN

        -- ==========================================
        -- CREAR LIKE
        -- ==========================================

        INSERT INTO Like_Cancion (
            id_usuario,
            id_cancion
        )
        VALUES (
            @id_usuario,
            @id_cancion
        );

        -- ==========================================
        -- VERIFICAR SI EXISTE PLAYLIST
        -- ==========================================

        IF NOT EXISTS (
            SELECT 1
            FROM Playlist
            WHERE id_usuario = @id_usuario
            AND es_favoritos = 1
        )
        BEGIN
            INSERT INTO Playlist (
                nombre,
                id_usuario,
                es_favoritos,
                imagen_url
            )
            VALUES (
                'Tus Me Gusta',
                @id_usuario,
                1,
                'TusMeGusta.jpg'
            );
        END

        -- ==========================================
        -- OBTENER ID PLAYLIST FAVORITOS
        -- ==========================================

        SELECT @id_playlist = id_playlist
        FROM Playlist
        WHERE id_usuario = @id_usuario
        AND es_favoritos = 1;

        -- ==========================================
        -- INSERTAR EN PLAYLIST
        -- ==========================================

        INSERT INTO Playlist_Cancion (
            id_playlist,
            id_cancion,
            orden
        )
        VALUES (
            @id_playlist,
            @id_cancion,
            (
                SELECT ISNULL(MAX(orden), 0) + 1
                FROM Playlist_Cancion
                WHERE id_playlist = @id_playlist
            )
        );
    END
END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER CANCIONES DE LA PLAYLIST 
-- ======================================================================

CREATE PROCEDURE sp_ObtenerCancionesPlaylist
    @id_playlist INT
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        c.id_cancion,
        c.titulo,
        c.duracion,
        c.archivo_url,
        c.imagen_url,
        a.nombre_artistico,
        pc.orden,
        pc.fecha_agregada
    FROM Playlist_Cancion pc
    INNER JOIN Cancion c
        ON pc.id_cancion = c.id_cancion
    INNER JOIN Artista a
        ON c.id_artista = a.id_artista
    WHERE
        pc.id_playlist = @id_playlist
        AND c.activo = 1
    ORDER BY pc.orden ASC;
END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER LAS PLAYLIST DEL USUARIO
-- ======================================================================

CREATE PROCEDURE sp_ObtenerPlaylistsUsuario

    @id_usuario INT

AS
BEGIN

    SET NOCOUNT ON;

    SELECT
        p.id_playlist,
        p.nombre,
        p.imagen_url,
        p.fecha_creacion,
        p.es_favoritos,

        COUNT(pc.id_cancion) AS total_canciones

    FROM Playlist p

    LEFT JOIN Playlist_Cancion pc
        ON p.id_playlist = pc.id_playlist

    WHERE p.id_usuario = @id_usuario

    GROUP BY
        p.id_playlist,
        p.nombre,
        p.imagen_url,
        p.fecha_creacion,
        p.es_favoritos

    ORDER BY
        p.es_favoritos DESC,
        p.fecha_creacion DESC;

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: CREAR PLAYLIST DEL USUARIO
-- ======================================================================

CREATE PROCEDURE sp_CrearPlaylist

    @nombre VARCHAR(100),
    @id_usuario INT,
    @imagen_url VARCHAR(255)

AS
BEGIN

    INSERT INTO Playlist (
        nombre,
        id_usuario,
        imagen_url
    )
    VALUES (
        @nombre,
        @id_usuario,
        @imagen_url
    );

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER LAS PLAYLIST DEL USUARIO
-- ======================================================================

CREATE PROCEDURE sp_ObtenerPlaylist

    @id_playlist INT,
    @id_usuario INT

AS
BEGIN

    SET NOCOUNT ON;

    SELECT
        id_playlist,
        nombre,
        imagen_url,
        fecha_creacion,
        es_favoritos

    FROM Playlist

    WHERE id_playlist = @id_playlist
    AND id_usuario = @id_usuario;

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: AGREGAR CANCION A PLAYLIST
-- ======================================================================

CREATE PROCEDURE sp_AgregarCancionPlaylist

    @id_playlist INT,
    @id_cancion INT

AS
BEGIN

    SET NOCOUNT ON;

    -- ==========================================
    -- EVITAR DUPLICADOS
    -- ==========================================

    IF NOT EXISTS (

        SELECT 1
        FROM Playlist_Cancion
        WHERE id_playlist = @id_playlist
        AND id_cancion = @id_cancion
    )

    BEGIN

        -- ==========================================
        -- INSERTAR CANCIÓN
        -- ==========================================

        INSERT INTO Playlist_Cancion (
            id_playlist,
            id_cancion,
            orden
        )
        VALUES (
            @id_playlist,
            @id_cancion,
            (
                SELECT ISNULL(MAX(orden), 0) + 1
                FROM Playlist_Cancion
                WHERE id_playlist = @id_playlist
            )
        );

    END

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: QUITAR CANCION DE LA PLAYLIST
-- ======================================================================

CREATE PROCEDURE sp_QuitarCancionPlaylist

    @id_playlist INT,
    @id_cancion INT

AS
BEGIN

    SET NOCOUNT ON;

    -- ==========================================
    -- ELIMINAR CANCIÓN DE PLAYLIST
    -- ==========================================

    DELETE FROM Playlist_Cancion
    WHERE id_playlist = @id_playlist
    AND id_cancion = @id_cancion;

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER ALBUM PARA EL USUARIO
-- ======================================================================

CREATE PROCEDURE sp_ObtenerAlbumUsuario

    @id_album INT

AS
BEGIN

    SET NOCOUNT ON;

    SELECT
        al.id_album,
        al.titulo,
        al.imagen_url,
        al.fecha_lanzamiento,

        ar.id_artista,
        ar.nombre_artistico

    FROM Album al

    INNER JOIN Artista ar
        ON al.id_artista = ar.id_artista

    WHERE al.id_album = @id_album;

END
GO

-- ======================================================================
-- PROCEDIMIENTO ALMACENADO: OBTENER CANCIONES DEL ALBUM PARA EL USUARIO
-- ======================================================================

CREATE PROCEDURE sp_ObtenerCancionesAlbumUsuario

    @id_album INT,
    @id_usuario INT

AS
BEGIN

    SET NOCOUNT ON;

    SELECT
        c.id_cancion,
        c.titulo,
        c.duracion,
        c.archivo_url,
        c.imagen_url,

        ar.nombre_artistico,

        CASE
            WHEN lc.id_usuario IS NOT NULL
            THEN 1
            ELSE 0
        END AS tiene_like

    FROM Cancion c

    INNER JOIN Artista ar
        ON c.id_artista = ar.id_artista

    LEFT JOIN Like_Cancion lc
        ON c.id_cancion = lc.id_cancion
        AND lc.id_usuario = @id_usuario

    WHERE c.id_album = @id_album
    AND c.activo = 1

    ORDER BY c.fecha_subida ASC;

END
GO

SELECT * FROM Usuario