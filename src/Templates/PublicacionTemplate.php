<?php

abstract class PublicacionTemplate {
    protected $repo;
    protected $fileHandler;

    public function __construct($repo, $fileHandler) {
        $this->repo = $repo;
        $this->fileHandler = $fileHandler;
    }

    /**
     * El "Método Plantilla" que fija el algoritmo general de procesamiento.
     * Se marca como final para evitar que las subclases alteren la secuencia.
     */
    public final function procesar() {
        $this->validarSesion();
        $datos = $this->extraerDatos();
        $archivosGuardados = $this->procesarArchivos($datos);
        $resultado = $this->guardarEnBaseDatos($datos, $archivosGuardados);
        $this->redireccionar($resultado, $datos);
    }

    /**
     * Paso común: Validación de sesión activa
     */
    protected function validarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ../index.php");
            exit();
        }
    }

    // Pasos específicos que debe definir cada subclase concreta
    abstract protected function extraerDatos();
    abstract protected function procesarArchivos($datos);
    abstract protected function guardarEnBaseDatos($datos, $archivosGuardados);
    abstract protected function redireccionar($resultado, $datos);
}