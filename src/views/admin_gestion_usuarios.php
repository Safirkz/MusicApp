<?php
session_start();

// Validar que solo el Administrador (Rol 1) pueda entrar aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../src/config/conexion.php");
include("../partials/header.php");

// Obtener búsqueda
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Consultar usuarios
if (!empty($buscar)) {

    $sql = "SELECT *
            FROM vw_ListaUsuarios
            WHERE NombreUsuario LIKE ?
            OR email LIKE ?
            OR NombreRol LIKE ?
            ORDER BY id_usuario DESC";

    $params = array(
        "%$buscar%",
        "%$buscar%",
        "%$buscar%"
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

} else {

    $sql = "SELECT *
            FROM vw_ListaUsuarios
            ORDER BY id_usuario DESC";

    $stmt = sqlsrv_query($conn, $sql);

}
?>

<div class="row mb-4">
    <div class="col-md-10">
        <h2>Gestión de Usuarios 👥</h2>
        <p class="text-muted">Administra los accesos y roles de la plataforma.</p>
    </div>
    <div class="col-md-2 text-end">
        <a href="admin.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>

<?php
// Mostrar mensajes de éxito o error que vengan del controlador
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'artista') echo "<div class='alert alert-primary'>Usuario convertido en artista correctamente.</div>";
    if ($_GET['msg'] == 'inactivado') echo "<div class='alert alert-warning'>Usuario suspendido correctamente.</div>";
    if ($_GET['msg'] == 'activado') echo "<div class='alert alert-success'>Usuario reactivado correctamente.</div>";
    if ($_GET['msg'] == 'eliminado') echo "<div class='alert alert-danger'>Usuario eliminado definitivamente.</div>";
}
?>

<form method="GET" class="mb-3">
    <div class="input-group">
        <input 
            type="text"
            name="buscar"
            class="form-control"
            placeholder="Buscar usuario por nombre, correo o rol..."
            value="<?php echo htmlspecialchars($buscar); ?>">
        <button type="submit" class="btn btn-dark">
            Buscar
        </button>
    </div>
</form>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        // Evitar que el admin se borre a sí mismo
                        $esElMismoAdmin = ($row['id_usuario'] == $_SESSION['id_usuario']);
                        ?>
                        <tr>
                            <td><?php echo $row['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($row['NombreUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $row['NombreRol']; ?></span>
                            </td>
                            <td>
                                <?php if ($row['activo'] == 1): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Suspendido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$esElMismoAdmin): ?>
                                    <?php if ($row['id_rol'] == 2): ?>
                                        <!-- Botón convertir en artista -->
                                        <form action="../controllers/procesar_usuario.php" method="POST" class="d-inline">
                                            <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                            <input type="hidden" name="accion" value="hacer_artista">
                                            <button 
                                                type="submit"
                                                class="btn btn-primary btn-sm"
                                                onclick="return confirm('¿Convertir este usuario en artista?');">
                                                Volver Artista
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <!-- Botón de Activar/Desactivar -->
                                    <form action="../controllers/procesar_usuario.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                        
                                        <?php if ($row['activo'] == 1): ?>
                                            <input type="hidden" name="accion" value="inactivar">
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('¿Seguro que quieres suspender a este usuario?');">
                                                Suspender
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="accion" value="activar">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                Reactivar
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <!-- Botón de Eliminar Físico -->
                                    <form action="../controllers/procesar_usuario.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('PELIGRO: ¿Eliminar definitivamente este usuario de la base de datos?');">
                                            Eliminar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Tu cuenta (Protegida)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../partials/footer.php"); ?>