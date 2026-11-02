/*
|||  |======================================================|  |||
|||  |$$$$$$$$$$$$$$$$$$$$$$  INSERT  $$$$$$$$$$$$$$$$$$$$$$|  |||
|||  |======================================================|  |||
*/

INSERT INTO Rol (nombre_rol) VALUES 
('admin'),
('usuario'),
('artista');
-- SELECT * FROM Rol;

INSERT INTO Usuario (nombre, email, contrasena, id_rol) VALUES
('Test user', 'user@test.com', '123', 2),
('Test admin', 'admin@test.com', '123', 1),
('Test artist', 'artist@test.com', '123', 3);
SELECT * FROM Usuario

SELECT * FROM Artista

SELECT * FROM Genero

SELECT * FROM Album

SELECT * FROM Cancion

SELECT * FROM Playlist

DELETE Playlist WHERE id_playlist=5;

SELECT * FROM Playlist_Cancion

SELECT * FROM Historial_Reproduccion