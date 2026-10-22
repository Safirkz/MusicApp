# ADR 003: Separar la subida de archivos de procesar_cancion_artista.php

## Estado
[Propuesto.]

## Contexto 
En el archivo controllers/`procesar_cancion_artista.php`, la subida de la imagen y del audio de las canciones se realiza directamente dentro del mismo archivo que procesa la solicitud para crear una canción.
En el bloque SUBIR IMAGEN, se obtiene la información del archivo y se utiliza la función `move_uploaded_file()` para guardar la imagen en la carpeta `assets/img/canciones/.`
De igual manera, en el bloque SUBIR AUDIO, se obtiene la información del archivo de audio y se utiliza `move_uploaded_file()` para guardarlo en la carpeta assets/audio/.
Esto hace que controllers/`procesar_cancion_artista.php` tenga tanto la responsabilidad de procesar la creación de una canción como la de guardar los archivos asociados.

## Decisión
Se propone crear el archivo `src/Services/FileService.php` para separar de controllers/`procesar_cancion_artista.php` la lógica encargada de guardar las imágenes y los audios de las canciones.
El nuevo archivo `FileService.php` será responsable de realizar las operaciones necesarias para guardar ambos tipos de archivos en sus respectivas carpetas y devolver sus nombres o las rutas que necesite el proceso de creación de la canción.
Por su parte, controllers/`procesar_cancion_artista.php` utilizará `FileService.php` para guardar los archivos y continuará coordinando el proceso de creación de la canción y el registro de su información en SQL Server.

## Consecuencias

**Positivas**

* controllers/procesar_cancion_artista.php tendrá menos código dedicado a guardar imágenes y audios.
* La lógica para guardar archivos estará centralizada en src/Services/FileService.php.
* Será más fácil realizar cambios en la forma de guardar los archivos sin modificar directamente todo el proceso de creación de canciones.
* El código será más organizado y fácil de mantener.

**Negativas** 

* Será necesario crear el archivo src/Services/FileService.php.
* Se deberá modificar controllers/procesar_cancion_artista.php para utilizar el nuevo servicio.
* Será necesario probar que las imágenes, los audios y el registro de las canciones continúen funcionando correctamente.

## Atributo de calidad que mejora
* Mantenibilidad: se separa la lógica para guardar los archivos del proceso de creación de canciones, facilitando la comprensión y modificación del código.

## Archivos afectados
* controllers/procesar_cancion_artista.php: se modificarán los bloques SUBIR IMAGEN y SUBIR AUDIO para delegar el guardado de los archivos a FileService.php.
* src/Services/FileService.php: se creará este archivo para centralizar la lógica de guardado de imágenes y audios.
* assets/img/canciones/: se conservará como la carpeta destinada a las imágenes de las canciones.
* assets/audio/: se conservará como la carpeta destinada a los audios de las canciones.
