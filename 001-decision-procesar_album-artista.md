
# ADR 001: Implementación de API Gateway para el controlador procesar_album_artista

## Estado
[Propuesto]

## Contexto
Actualmente, los scripts de procesamiento como `procesar_album_artista.php` mezclan múltiples responsabilidades en un solo archivo monolítico; a su vez, se identificó que los controladores se encuentran mezclados con el modelo de base de datos.
Esta arquitectura acoplada sobrecarga la lógica con tareas transversales y complica la integración de los patrones de diseño planificados (*Factory Method*, *Facade* y *Template Method*).

## Decisión
Implementar un **API Gateway** como punto único de entrada (*Single Point of Entry*) para todas las peticiones del sistema.
El Gateway recibirá todas las peticiones externas y las redirigirá al archivo de procesamiento correspondiente (artistas, álbumes o canciones), permitiendo que cada uno trabaje de forma independiente, similar a microservicios. Todo esto, junto a los patrones de diseño planificados (*Factory Method*, *Facade* y *Template Method*), permitirá publicar un sencillo o un álbum sin que estos se crucen o se encuentren dentro de un mismo controlador.

## Consecuencias
**Positivas:**
* **Principio de Responsabilidad Única (SRP):** Cada procesador se encarga únicamente de su lógica de negocio, delegando al Gateway la responsabilidad del enrutamiento y la autenticación.
* **Principio Abierto/Cerrado (OCP):** Permite añadir nuevos módulos o procesadores de catálogo sin necesidad de modificar el código existente.
* **Principio DRY (Don't Repeat Yourself):** Elimina la duplicación de controles de sesión y configuraciones en cada script PHP al centralizarlos en la entrada principal.
* **Bajo Acoplamiento:** Separa la interfaz de usuario de la persistencia de datos, permitiendo que ambos evolucionen de forma independiente.

**Negativas:**
* Introduce una capa adicional de abstracción y enrutamiento en la arquitectura.