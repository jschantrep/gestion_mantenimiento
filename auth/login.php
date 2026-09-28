<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: ../dashboard/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión Inteligente del Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-5">

                <div class="card shadow">

                    <div class="card-body p-4">

                        <h3 class="text-center mb-2">
                            Gestión Inteligente
                        </h3>

                        <p class="text-center text-muted mb-4">
                            Sistema de gestión de mantenimiento
                        </p>

                        <form action="validar.php" method="POST">

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="correo"
                                    name="correo"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Iniciar sesión
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

</body>

</html>