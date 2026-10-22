<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}
include("../src/config/conexion.php");
include("../partials/header.php");
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Catálogo de Géneros Musicales 🎸</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="admin.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form action="../controllers/procesar_genero.php" method="POST" class="row g-3">
            <div class="col-auto">
                <label class="form-label">Nuevo Género:</label>
            </div>
            <div class="col-auto">
                <input type="text" name="nombre_genero" class="form-control" placeholder="Ej: Rock Alternativo" required>
            </div>
            <div class="col-auto">
                <button type="submit" name="accion" value="insertar" class="btn btn-primary">Agregar Género</button>
            </div>
        </form>
    </div>
</div>

<?php
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'creado') echo "<div class='alert alert-success'>Género agregado con éxito.</div>";
    if ($_GET['msg'] == 'eliminado') echo "<div class='alert alert-danger'>Género eliminado.</div>";
    if ($_GET['msg'] == 'error') echo "<div class='alert alert-danger'>Error: Quizás hay canciones usando este género.</div>";
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Género</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM Genero ORDER BY nombre ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?php echo $row['id_genero']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                    <td class="text-center">
                        <form action="../controllers/procesar_genero.php" method="POST" onsubmit="return confirm('¿Eliminar este género?');">
                            <input type="hidden" name="id_genero" value="<?php echo $row['id_genero']; ?>">
                            <button type="submit" name="accion" value="eliminar" class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../partials/footer.php"); ?>