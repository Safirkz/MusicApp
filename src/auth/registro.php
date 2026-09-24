<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MusicApp</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4" style="width: 420px; border-radius: 20px;">

        <div class="text-center mb-4">
            <h2 class="fw-bold">🎵 MusicApp</h2>
            <p class="text-muted">Crea tu cuenta</p>
        </div>

        <form action="../controllers/procesar_registro.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nombre Completo:</label>
                <input 
                    type="text" 
                    name="nombre" 
                    class="form-control"
                    placeholder="Tu nombre"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Correo:</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control"
                    placeholder="ejemplo@gmail.com"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input 
                    type="password" 
                    name="contrasena" 
                    class="form-control"
                    placeholder="********"
                    required
                >
            </div>

            <button type="submit" class="btn btn-success w-100">
                Registrarme
            </button>

        </form>

        <div class="text-center mt-4">
            <p class="d-inline">¿Ya tienes cuenta?</p>
            <a href="login.php" class="text-decoration-none">
                Inicia sesión aquí
            </a>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>