CREATE DATABASE MusicApp;
GO

USE MusicApp;
GO

/*
|||  |======================================================|  |||
|||  |$$$$$$$$$$$$$$$$$$$$$$  FASE 1  $$$$$$$$$$$$$$$$$$$$$$|  |||
|||  |======================================================|  |||
*/

CREATE TABLE Rol (
    id_rol INT IDENTITY(1,1) PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Usuario (
    id_usuario INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT GETDATE(),
    id_rol INT NOT NULL,
    activo BIT NOT NULL DEFAULT 1,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    FOREIGN KEY (id_rol) REFERENCES Rol(id_rol)
);

SELECT * from Usuario;

CREATE TABLE Artista (
    id_artista INT IDENTITY(1,1) PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_artistico VARCHAR(100) NOT NULL,
    nacionalidad VARCHAR(50),
    descripcion VARCHAR(MAX),
    imagen_url VARCHAR(255),
    activo BIT NOT NULL DEFAULT 1,

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);


CREATE TABLE Album (
    id_album INT IDENTITY(1,1) PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    fecha_lanzamiento DATE,
    id_artista INT NOT NULL,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('album', 'sencillo')),
    imagen_url VARCHAR(255),
    activo BIT NOT NULL DEFAULT 1,

    FOREIGN KEY (id_artista) REFERENCES Artista(id_artista) ON DELETE CASCADE
);

CREATE TABLE Cancion (
    id_cancion INT IDENTITY(1,1) PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    duracion INT NOT NULL, --en segundos--
    id_album INT NULL,
    id_artista INT NOT NULL,
    archivo_url VARCHAR(255) NOT NULL,
    fecha_subida DATETIME DEFAULT GETDATE(),
    imagen_url VARCHAR(255),
    activo BIT NOT NULL DEFAULT 1,

    FOREIGN KEY (id_album) REFERENCES Album(id_album) ON DELETE CASCADE,
    FOREIGN KEY (id_artista) REFERENCES Artista(id_artista)
);

CREATE TABLE Playlist (
    id_playlist INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_usuario INT NOT NULL,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    imagen_url VARCHAR(255),
    es_favoritos BIT NOT NULL DEFAULT 0,

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
);


CREATE TABLE Playlist_Cancion (
    id_playlist INT NOT NULL,
    id_cancion INT NOT NULL,
    fecha_agregada DATETIME DEFAULT GETDATE(),
    orden INT NOT NULL,

    PRIMARY KEY (id_playlist, id_cancion),

    UNIQUE (id_playlist, orden),

    FOREIGN KEY (id_playlist) REFERENCES Playlist(id_playlist) ON DELETE CASCADE,
    FOREIGN KEY (id_cancion) REFERENCES Cancion(id_cancion) ON DELETE CASCADE
);


CREATE TABLE Like_Cancion (
    id_usuario INT NOT NULL,
    id_cancion INT NOT NULL,
    fecha_like DATETIME DEFAULT GETDATE(),

    PRIMARY KEY (id_usuario, id_cancion),

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cancion) REFERENCES Cancion(id_cancion) ON DELETE CASCADE
);

/*
|||  |======================================================|  |||
|||  |$$$$$$$$$$$$$$$$$$$$$$  FASE 2  $$$$$$$$$$$$$$$$$$$$$$|  |||
|||  |======================================================|  |||
*/

CREATE TABLE Membresia (
    id_membresia INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL, --free, premium--
    precio DECIMAL(10,2),
    descripcion VARCHAR(255)
);

CREATE TABLE Usuario_Membresia (
    id_usuario INT NOT NULL,
    id_membresia INT NOT NULL,
    fecha_inicio DATETIME DEFAULT GETDATE(),
    fecha_fin DATETIME,
    estado VARCHAR(20) CHECK (estado IN ('activa', 'cancelada', 'vencida')),

    PRIMARY KEY (id_usuario, id_membresia, fecha_inicio),

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_membresia) REFERENCES Membresia(id_membresia) ON DELETE CASCADE
);

CREATE TABLE Historial_Reproduccion (
    id_historial INT IDENTITY(1,1) PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cancion INT NOT NULL,
    fecha DATETIME DEFAULT GETDATE(),

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cancion) REFERENCES Cancion(id_cancion) ON DELETE CASCADE
);

CREATE TABLE Genero (
    id_genero INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Cancion_Genero (
    id_cancion INT NOT NULL,
    id_genero INT NOT NULL,

    PRIMARY KEY (id_cancion, id_genero),

    FOREIGN KEY (id_cancion) REFERENCES Cancion(id_cancion) ON DELETE CASCADE,
    FOREIGN KEY (id_genero) REFERENCES Genero(id_genero)
);

CREATE TABLE Usuario_Artista (-- SEGUIR ARTISTA
    id_usuario INT NOT NULL,
    id_artista INT NOT NULL,
    fecha_seguimiento DATETIME DEFAULT GETDATE(),

    PRIMARY KEY (id_usuario, id_artista),

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_artista) REFERENCES Artista(id_artista) ON DELETE CASCADE
);

