## 2. Componentes y responsabilidades

| Componente | Responsabilidad | Patrón / Rol | Dependencias | Ubicación |
| :--- | :--- | :--- | :--- | :--- |
| `API Gateway` | Interceptar las solicitudes del cliente en la ruta `/api/artista/*` y reenviarlas al microservicio del módulo de artistas. | Gateway / Proxy | Microservicio Artistas | `src/gateway/` |
| `ArtistaController` | Recibir la petición HTTP, validar entradas básicas de datos del artista y retornar respuestas JSON/HTTP. | Controlador HTTP | `ArtistaFacade` | `src/controllers/artista.controller.php` |
| `AlbumController` | Procesar peticiones HTTP exclusivas para la gestión, consulta y creación de álbumes. | Controlador HTTP | `ArtistaFacade` | `src/controllers/album.controller.php` |
| `CancionController` | Procesar peticiones HTTP de carga, edición y eliminación de canciones individuales. | Controlador HTTP | `ArtistaFacade` | `src/controllers/cancion.controller.php` |
| `ArtistaFacade` | Coordinar la secuencia del flujo de trabajo unificando la llamada a la fábrica, la plantilla de publicación y la persistencia en una interfaz limpia. | **Facade** | `LanzamientoFactory`, `PublicacionTemplate` | `src/facades/artista.facade.php` |
| `LanzamientoFactory` | Instanciar dinámicamente objetos de dominio de tipo `Album` o `Sencillo` según el parámetro de la solicitud. | **Factory Method** | Entidades `Album`, `Sencillo` | `src/factories/lanzamiento.factory.php` |
| `PublicacionTemplate` | Definir el algoritmo base e inmutable para publicar un contenido: `validarMetadatos()`, `procesarArchivos()`, `guardarEnBD()`. | **Template Method** | `ArtistaRepository`, `FileSystemHandler` | `src/templates/publicacion.template.php` |
| `PublicarSencillo` | Implementar los pasos específicos de la plantilla para procesar un único archivo de audio `.mp3` y su portada. | Subclase Template | `ArtistaRepository`, `FileSystemHandler` | `src/templates/publicar-sencillo.php` |
| `PublicarAlbum` | Implementar los pasos específicos de la plantilla para iterar sobre un lote de canciones `.mp3`, ordenarlas por pista y vincularlas a un álbum. | Subclase Template | `ArtistaRepository`, `FileSystemHandler` | `src/templates/publicar-album.php` |
| `ArtistaRepository` | Ejecutar las consultas SQL y procedimientos almacenados directos sobre la base de datos relacional. | Repository / DAO | SQL Server | `src/repositories/artista.repository.php` |
| `FileSystemHandler` | Gestionar la lectura, escritura, validación de formato y almacenamiento físico de archivos `.mp3` e imágenes en el disco local. | Storage Handler | Disco Duro Local | `src/utils/file-system.handler.php` |