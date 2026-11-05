# 🎵 MusicApp - Módulo de Artista (Refactorización)

Sistema de gestión musical para artistas refactorizado bajo una arquitectura modular limpia utilizando los patrones **API Gateway**, **Controller**, **Facade**, **Factory Method**, **Template Method** y **Repository**.

---

## 👥 Integrantes del Equipo

* **Isabela Perico Rojas**
* **Sara Vanessa Barrera Taborda**
* **Samuel Salazar Montoya**
* **Safir Khan Zuluaga**

* **Profesor:** Fernando Higuita Betancur
* **Asignatura:** Arquitectura de Software
* **Programa:** Ingeniería de Sistemas (Semestre 6)
* **Institución:** Universidad Católica Luis Amigó (2026)

---

## 📄 Documentación de Arquitectura

Puedes consultar el documento formal de arquitectura con el detalle técnico de los patrones implementados, diagramas, flujo de datos y ADRs en el siguiente enlace:

👉 [Documentación Técnica de Arquitectura (Google Docs)](https://docs.google.com/document/d/10y22IoC6W_rRw6kmm5hxM7_B2i0ZmhVxIDeG2Aa6ZoI/edit?usp=sharing)

---

## 🛠️ Requisitos y Dependencias

Para ejecutar este proyecto en un entorno local se requiere:

* **Servidor Web:** Apache 2.4 (vía XAMPP, WAMP o Laragon).
* **Lenguaje de Programación:** PHP >= 8.0.
* **Motor de Base de Datos:** Microsoft SQL Server (2019 o superior).
* **Extensiones de PHP requeridas:**
  * `php_sqlsrv` (Driver oficial de Microsoft para conexión a SQL Server).
  * `php_pdo_sqlsrv`.
* **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5.

---

## 🚀 Pasos para Instalar y Ejecutar el Proyecto

1. **Clonar el repositorio:**

   ```bash
   git clone https://github.com/Safirkz/MusicApp.git
   ```

2. **Ubicar el proyecto en la carpeta del servidor web:** copia o mueve la carpeta clonada a la ruta raíz de tu servidor Apache:

   ```text
   C:\xampp\htdocs\musicapp_refactorizacion
   ```

3. **Configurar la Base de Datos en SQL Server:**
   * Abre SQL Server Management Studio (SSMS).
   * Ejecuta el script de creación de la base de datos `MusicApp`.
   * Compila los Procedimientos Almacenados actualizados (`sp_ObtenerAlbumesArtista`, `sp_CrearAlbum`, `sp_CrearCancion`, `sp_CambiarEstadoAlbum`, `sp_CambiarEstadoCancion`, `sp_ObtenerArtistaPorUsuario`).

4. **Verificar Credenciales de Conexión:** asegúrate de ajustar los datos de acceso en el archivo `src/config/conexion.php`:

   ```php
   $serverName = "localhost"; // Nombre del servidor o instancia SQL Server
   $connectionOptions = array(
       "Database" => "MusicApp",
       "Uid" => "tu_usuario",
       "PWD" => "tu_contraseña",
       "CharacterSet" => "UTF-8"
   );
   ```

5. **Iniciar los Servicios:**
   * Inicia el módulo de Apache en XAMPP.
   * Verifica que el servicio de SQL Server esté activo.

6. **Acceder a la Aplicación:** navega en tu explorador web a la siguiente URL:

   ```text
   http://localhost/musicapp_refactorizacion/src/views/artista_gestion_catalogo.php
   ```

---

## 📌 Tabla de Endpoints API (API Gateway)

Todas las peticiones del módulo se procesan de manera centralizada a través del punto único de entrada:

`POST /src/gateway/ArtistaGateway.php`

| Método | Ruta / Acción | Descripción | Ejemplo de Datos de Entrada | Ejemplo de Respuesta (JSON / Estado) |
|--------|---------------|-------------|-----------------------------|--------------------------------------|
| POST | `/src/gateway/ArtistaGateway.php?accion=editar_perfil` | Actualiza los datos del perfil del artista logueado. | Form-Data:<br>`accion`: "editar_perfil"<br>`nombre_artistico`: "Safir"<br>`nacionalidad`: "Colombia"<br>`descripcion`: "Productor urbano"<br>`imagen`: [perfil.jpg] | `{"status": 200, "message": "Perfil actualizado correctamente", "redirect": "../views/artista_perfil.php"}` |
| POST | `/src/gateway/ArtistaGateway.php?accion=crear_album` | Registra un nuevo álbum y guarda la imagen de portada en disco. | Form-Data:<br>`accion`: "crear_album"<br>`titulo`: "Nuevo Horizonte"<br>`fecha_lanzamiento`: "2026-11-05"<br>`id_artista`: 12<br>`imagen`: [portada.jpg] | `{"status": 201, "message": "Álbum publicado exitosamente", "id_album": 45}` |
| POST | `/src/gateway/ArtistaGateway.php?accion=cambiar_estado_album` | Cambia el estado (activo/inactivo) de un álbum completo. | Form-Data:<br>`accion`: "cambiar_estado_album"<br>`id_album`: 45<br>`estado`: 0 | `{"status": 200, "message": "Estado del álbum actualizado", "nuevo_estado": 0}` |
| POST | `/src/gateway/ArtistaGateway.php?accion=crear_cancion` | Publica una canción o sencillo guardando audio e imagen. | Form-Data:<br>`accion`: "crear_cancion"<br>`titulo`: "Noches Frías"<br>`duracion`: 210<br>`id_album`: 45<br>`id_artista`: 12<br>`audio`: [cancion.mp3]<br>`imagen`: [portada.png] | `{"status": 201, "message": "Canción publicada correctamente", "archivo_audio": "audio_173082.mp3"}` |
| POST | `/src/gateway/ArtistaGateway.php?accion=cambiar_estado_cancion` | Cambia el estado (activo/inactivo) de una canción individual. | Form-Data:<br>`accion`: "cambiar_estado_cancion"<br>`id_cancion`: 102<br>`estado`: 1 | `{"status": 200, "message": "Estado de la canción modificado", "nuevo_estado": 1}` |
| POST | `/src/gateway/ArtistaGateway.php?accion=eliminar_cancion` | Elimina permanentemente una canción del catálogo del artista. | Form-Data:<br>`accion`: "eliminar_cancion"<br>`id_cancion`: 102 | `{"status": 200, "message": "Canción eliminada exitosamente"}` |
