<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../controllers/ArtistaController.php';
require_once __DIR__ . '/../controllers/AlbumController.php';
require_once __DIR__ . '/../controllers/CancionController.php';

class ArtistaGateway {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handle() {
        // 1. SEGURIDAD CENTRALIZADA (Middleware)
        if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 3) {
            header("Location: ../index.php?error=no_autorizado");
            exit();
        }

        // 2. ENRUTAMIENTO HACIA CONTROLADORES ESPECÍFICOS
        $accion = $_REQUEST['accion'] ?? '';

        try {
            switch ($accion) {
                // --- PERFIL ARTISTA ---
                case 'editar_perfil':
                    (new ArtistaController($this->conn))->editarPerfil();
                    break;

                // --- ÁLBUMES ---
                case 'crear_album':
                case 'cambiar_estado_album':
                    (new AlbumController($this->conn))->procesar($accion);
                    break;

                // --- CANCIONES ---
                case 'crear_cancion':
                case 'publicar_cancion':
                case 'eliminar_cancion':
                case 'cambiar_estado_cancion':
                    (new CancionController($this->conn))->procesar($accion);
                    break;

                // --- ACCIONES CON NOMBRES LEGACY ('inactivar', 'activar', 'eliminar') ---
                default:
                    if (isset($_REQUEST['id_cancion'])) {
                        (new CancionController($this->conn))->procesar($accion);
                    } elseif (isset($_REQUEST['id_album'])) {
                        (new AlbumController($this->conn))->procesar($accion);
                    } else {
                        throw new Exception("Acción '$accion' no reconocida por ArtistaGateway.");
                    }
                    break;
            }
        } catch (Exception $e) {
            die("Error en ArtistaGateway: " . $e->getMessage());
        }
    }
}

// Ejecución
$gateway = new ArtistaGateway($conn);
$gateway->handle();