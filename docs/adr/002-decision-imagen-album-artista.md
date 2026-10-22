# ADR 002: Separar la subida de imagen de procesar_album_artista.php

## Estado
[Propuesto.]

## Contexto
En el archivo controllers/procesar_album_artista.php, dentro de la acción crear_album, se encuentra el bloque SUBIR IMAGEN, encargado de guardar la imagen de portada del álbum.
Actualmente, el controlador obtiene el nombre de la imagen mediante $_FILES['imagen']['name'], obtiene la ruta temporal con $_FILES['imagen']['tmp_name'] y utiliza move_uploaded_file() para guardar el archivo en la carpeta ../assets/img/albums/.
Después de guardar la imagen, el mismo controlador ejecuta el procedimiento almacenado sp_CrearAlbum para registrar la información del álbum en SQL Server.
Esto hace que controllers/procesar_album_artista.php se encargue tanto de procesar la creación del álbum como de guardar su imagen.

## Decisión 
Se propone crear el archivo services/AlbumImageService.php para separar únicamente la operación de guardar la imagen de portada.
Este archivo tendrá una función sencilla que recibirá la información de la imagen, utilizará move_uploaded_file() para guardarla en assets/img/albums/ y devolverá el nombre del archivo si la operación se realiza correctamente.
Si no es posible guardar la imagen, la función indicará que ocurrió un error.
Por su parte, controllers/procesar_album_artista.php utilizará AlbumImageService.php para guardar la portada. Si la operación tiene éxito, continuará ejecutando sp_CrearAlbum como lo hace actualmente. Si falla, no intentará registrar el álbum y mostrará un mensaje de error.
No se modificarán el procedimiento almacenado, los parámetros enviados a SQL Server ni la redirección que se realiza cuando el álbum se crea correctamente.

## Consecuencias

**Positivas**

* controllers/procesar_album_artista.php tendrá menos código dedicado a guardar imágenes.
* La operación de subida estará ubicada en un archivo específico.
* Será más fácil identificar y corregir errores relacionados con la imagen de portada.
* Se conservará el funcionamiento actual de creación de álbumes.

**Negativas**

* Será necesario crear services/AlbumImageService.php.
* Se deberá modificar controllers/procesar_album_artista.php para utilizar la nueva función.
* Será necesario probar tanto la subida correcta de una imagen como el comportamiento cuando la subida falla.
* Atributo de calidad que mejora
* Mantenibilidad: separar la subida de la imagen del proceso de creación del álbum facilita entender, modificar y corregir esa parte del código.

## Archivos afectados
controllers/procesar_album_artista.php: se modificará el bloque SUBIR IMAGEN para utilizar el nuevo servicio.
services/AlbumImageService.php: se creará este archivo para guardar la imagen y devolver el resultado de la operación.
assets/img/albums/: se conservará como la carpeta donde se guardan las imágenes de portada.



