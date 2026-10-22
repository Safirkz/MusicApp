<?php
session_start();

// Validar que solo el Administrador pueda ejecutar acciones
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../src/config/conexion.php");

// Validar datos
if (
    !isset($_POST['id_album']) ||
    !isset($_POST['accion'])
) {

    header("Location: ../admin/admin_gestion_catalogo.php?filtro=album");

    exit();
}

$id_album = $_POST['id_album'];
$accion = $_POST['accion'];

// ==========================================
// INACTIVAR ÁLBUM
// ==========================================

if ($accion == "inactivar") {

    // Inactivar álbum
    $sqlAlbum = "
    UPDATE Album
    SET activo = 0
    WHERE id_album = ?
    ";

    $paramsAlbum = array($id_album);
    sqlsrv_query($conn, $sqlAlbum, $paramsAlbum);

    // Inactivar canciones del álbum
    $sqlCanciones = "
    UPDATE Cancion
    SET activo = 0
    WHERE id_album = ?
    ";

    $paramsCanciones = array($id_album);
    sqlsrv_query($conn, $sqlCanciones, $paramsCanciones);
    header("Location: ../admin/admin_album.php?id=$id_album&msg=inactivado");
    exit();
}

// ==========================================
// ACTIVAR ÁLBUM
// ==========================================

if ($accion == "activar") {
    // Activar álbum
    $sqlAlbum = "
    UPDATE Album
    SET activo = 1
    WHERE id_album = ?
    ";

    $paramsAlbum = array($id_album);
    sqlsrv_query($conn, $sqlAlbum, $paramsAlbum);

    // Activar canciones del álbum
    $sqlCanciones = "
    UPDATE Cancion
    SET activo = 1
    WHERE id_album = ?
    ";
    $paramsCanciones = array($id_album);
    sqlsrv_query($conn, $sqlCanciones, $paramsCanciones);
    header("Location: ../admin/admin_album.php?id=$id_album&msg=activado");
    exit();
}

// ==========================================
// ELIMINAR ÁLBUM
// ==========================================

if ($accion == "eliminar") {
    // Eliminar álbum
    // Las canciones se eliminan automáticamente
    // gracias al ON DELETE CASCADE

    $sql = "
    DELETE FROM Album
    WHERE id_album = ?
    ";
    $params = array($id_album);
    sqlsrv_query($conn, $sql, $params);
    header("Location: ../views/admin_gestion_catalogo.php?filtro=album&msg=eliminado");
    exit();
}
?>