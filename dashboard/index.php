<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}



require_once "../config/database.php";
$usuario = $_SESSION["usuario"];

// Total de activos
$sql_activos = "SELECT COUNT(*) AS total FROM activo_tecnologico";
$resultado_activos = $conn->query($sql_activos);
$total_activos = $resultado_activos->fetch_assoc()["total"];


// Total de mantenimientos
$sql_mantenimientos = "SELECT COUNT(*) AS total FROM orden_mantenimiento";
$resultado_mantenimientos = $conn->query($sql_mantenimientos);
$total_mantenimientos = $resultado_mantenimientos->fetch_assoc()["total"];


// Órdenes pendientes
$sql_pendientes = "
    SELECT COUNT(*) AS total
    FROM orden_mantenimiento
    WHERE estado_orden = 'Pendiente'
";

$resultado_pendientes = $conn->query($sql_pendientes);
$total_pendientes = $resultado_pendientes->fetch_assoc()["total"];


// Total de intervenciones
$sql_intervenciones = "SELECT COUNT(*) AS total FROM intervencion";
$resultado_intervenciones = $conn->query($sql_intervenciones);
$total_intervenciones = $resultado_intervenciones->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Gestión de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../public/css/estilos.css">

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        Gestión de Mantenimiento
    </div>

    <a href="index.php" class="active">
        Dashboard
    </a>

    <a href="../controllers/ActivoController.php?accion=listar">
        
        Activos
    </a>

    <a href="../mantenimiento/index.php">
        Mantenimientos
    </a>

    <a href="../controllers/OrdenController.php?accion=listar">
        Órdenes de mantenimiento
    </a>

    <a href="../intervenciones/index.php">
        Intervenciones
    </a>

    <a href="../costos/index.php">
        Costos
    </a>

    <a href="../historial/index.php">
        Historial
    </a>

    <hr>

    <a href="../analisis/indicadores.php">
        Indicadores
    </a>

    <a href="../analisis/clustering.php">
        Segmentación
    </a>

    <a href="../analisis/prediccion.php">
        Predicción
    </a>

    <hr>

    <a href="../simulacion/index.php">
        Simulación
    </a>

</div>


<!-- CONTENIDO PRINCIPAL -->

<div class="main-content">

    <!-- NAVBAR -->

    <div class="topbar">

        <div>
            <strong>Panel de control</strong>
        </div>

        <div class="user-info">

            <pan>
                <?php echo htmlspecialchars($usuario["nombre"]); ?>
            </span>

            <span class="badge bg-primary">
               <?php echo htmlspecialchars($usuario["rol"]); ?>
            </span>

            <a
                href="../auth/logout.php"
                class="btn btn-outline-danger btn-sm">

                Cerrar sesión

            </a>

        </div>

    </div>


    <!-- CONTENIDO -->

    <div class="content">

        <div class="mb-4">

            <h2>
                Dashboard
            </h2>

            <p class="text-muted">
                Gestión Inteligente del Mantenimiento
            </p>

        </div>


        <!-- INDICADORES -->

        <div class="row g-4 mb-4">

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <h6>
                        Total de activos
                    </h6>

                    <h2>
                        <?php echo $total_activos; ?>
                    </h2>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <h6>
                        Mantenimientos
                    </h6>

                    <h2>
                        <?php echo $total_mantenimientos; ?>
                    </h2>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <h6>
                        Órdenes pendientes
                    </h6>

                    <h2>
                        <?php echo $total_pendientes; ?>
                    </h2>

                </div>

            </div>


            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <h6>
                        Intervenciones
                    </h6>

                    <h2>
                        <?php echo $total_intervenciones; ?>
                    </h2>

                </div>

            </div>

        </div>


        <!-- MÓDULOS -->

        <h4 class="mb-3">
            Módulos del sistema
        </h4>


        <div class="row g-4">

            <div class="col-md-6 col-lg-4">

                <a href="../controllers/ActivoController.php?accion=listar"
                   class="module-card">

                    <h5>
                        Activos
                    </h5>

                    <p>
                        Registrar, consultar y gestionar
                        equipos y activos.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../mantenimiento/index.php"
                   class="module-card">

                    <h5>
                        Mantenimientos
                    </h5>

                    <p>
                        Programar y consultar actividades
                        de mantenimiento.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../controllers/OrdenController.php?accion=listar"
                   class="module-card">

                    <h5>
                        Órdenes
                    </h5>

                    <p>
                        Gestionar órdenes y asignación
                        de técnicos.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../intervenciones/index.php"
                   class="module-card">

                    <h5>
                        Intervenciones
                    </h5>

                    <p>
                        Registrar el resultado de las
                        intervenciones realizadas.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../historial/index.php"
                   class="module-card">

                    <h5>
                        Historial
                    </h5>

                    <p>
                        Consultar el historial de los
                        activos.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../analisis/indicadores.php"
                   class="module-card">

                    <h5>
                        Análisis e indicadores
                    </h5>

                    <p>
                        Consultar indicadores y resultados
                        del análisis de datos.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../analisis/clustering.php"
                   class="module-card">

                    <h5>
                        Segmentación de activos
                    </h5>

                    <p>
                        Consultar los grupos obtenidos
                        mediante clustering.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../analisis/prediccion.php"
                   class="module-card">

                    <h5>
                        Predicción de fallas
                    </h5>

                    <p>
                        Consultar resultados del modelo
                        predictivo.
                    </p>

                </a>

            </div>


            <div class="col-md-6 col-lg-4">

                <a href="../simulacion/index.php"
                   class="module-card">

                    <h5>
                        Simulación
                    </h5>

                    <p>
                        Analizar escenarios de mantenimiento
                        y apoyar la toma de decisiones.
                    </p>

                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>