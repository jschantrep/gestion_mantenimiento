<?php

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalle del activo - Gestión de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../public/css/estilos.css">

</head>

<body>

<div class="sidebar">

    <div class="logo">
        Gestión de Mantenimiento
    </div>

    <a href="../dashboard/index.php">
        Dashboard
    </a>

    <a href="index.php" class="active">
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


<div class="main-content">

    <div class="topbar">

        <div>
            <strong>Gestión de Activos</strong>
        </div>

        <div class="user-info">

            <span>
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


    <div class="content">

        <!-- ENCABEZADO -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2>
                    Detalle del activo
                </h2>

                <p class="text-muted mb-0">
                    Información registrada del activo.
                </p>

            </div>

            <div class="d-flex gap-2">

                <a
                    href="../controllers/ActivoController.php?accion=editar&id=<?php echo $activo["id_activo"]; ?>"
                    class="btn btn-primary">

                    Editar

                </a>

                <form
                    method="POST"
                    action="../controllers/ActivoController.php"
                    onsubmit="return confirm('¿Está seguro de dar de baja este activo?');">

                    <input
                        type="hidden"
                        name="accion"
                        value="eliminar">

                    <input
                        type="hidden"
                        name="id_activo"
                        value="<?php echo $activo["id_activo"]; ?>">

                    <button
                        type="submit"
                        class="btn btn-outline-danger">

                        Dar de baja

                    </button>

                </form>

                <a
                    href="../controllers/ActivoController.php?accion=listar"
                    class="btn btn-secondary">

                    Volver

                </a>

            </div>

        </div>


        <!-- INFORMACIÓN -->

        <div class="stat-card">

            <div class="row g-4">


                <!-- INFORMACIÓN GENERAL -->

                <div class="col-md-6">

                    <h5 class="mb-3">
                        Información general
                    </h5>

                    <div class="mb-3">

                        <strong>
                            Código
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["codigo_activo"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Nombre
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["nombre"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Marca
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["marca"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Modelo
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["modelo"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Número de serie
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["numero_serie"]); ?>
                        </div>

                    </div>

                </div>


                <!-- INFORMACIÓN TÉCNICA -->

                <div class="col-md-6">

                    <h5 class="mb-3">
                        Información técnica
                    </h5>


                    <div class="mb-3">

                        <strong>
                            Tipo de activo
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["tipo_activo"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Ubicación
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["ubicacion"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Criticidad
                        </strong>

                        <div>

                            <?php

                            $badge = "bg-secondary";

                            if ($activo["criticidad"] === "Alta") {
                                $badge = "bg-danger";
                            } elseif ($activo["criticidad"] === "Media") {
                                $badge = "bg-warning text-dark";
                            } elseif ($activo["criticidad"] === "Baja") {
                                $badge = "bg-success";
                            }

                            ?>

                            <span class="badge <?php echo $badge; ?>">

                                <?php
                                echo htmlspecialchars(
                                    $activo["criticidad"]
                                );
                                ?>

                            </span>

                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Estado
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["estado"]); ?>
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Horas de uso acumuladas
                        </strong>

                        <div>
                            <?php
                            echo number_format(
                                $activo["horas_uso_acumuladas"],
                                0,
                                ",",
                                "."
                            );
                            ?>
                            horas
                        </div>

                    </div>


                    <div class="mb-3">

                        <strong>
                            Fecha de adquisición
                        </strong>

                        <div>
                            <?php echo htmlspecialchars($activo["fecha_adquisicion"]); ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>