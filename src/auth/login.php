<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MusicApp</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 20px;">

        <div class="text-center mb-4">
            <h2 class="fw-bold">🎵 BeatUp</h2>
            <p class="text-muted">Inicia sesión para continuar</p>
        </div>

        <?php
        // Si la URL dice login.php?mensaje=exito
        if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'exito') {
            echo "
            <div class='alert alert-success text-center'>
                ¡Cuenta creada con éxito! Ahora puedes iniciar sesión.
            </div>";
        }
        ?>

        <form action="../controllers/procesar_login.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Correo:</label>
                <input type="email" name="email" class="form-control" placeholder="ejemplo@gmail.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="contrasena" class="form-control" placeholder="********" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Entrar
            </button>

        </form>

        <div class="text-center mt-4">
            <p class="d-inline">¿No tienes cuenta?</p>
            <a href="registro.php" class="text-decoration-none">
                Crear cuenta nueva
            </a>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>