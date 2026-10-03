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

    <title>Activos - Gestión de Mantenimiento</title>

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

    <a href="../dashboard/index.php">
        Dashboard
    </a>

    <a
    href="../controllers/ActivoController.php?accion=listar"
    class="active">

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


<!-- CONTENIDO -->

<div class="main-content">

    <!-- TOPBAR -->

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


    <!-- CONTENIDO -->

    <div class="content">

        <!-- ENCABEZADO -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2>
                    Activos
                </h2>

                <p class="text-muted mb-0">
                    Gestión de equipos, maquinaria e infraestructura.
                </p>

            </div>

            <a
                href="../controllers/ActivoController.php?accion=crear"
                class="btn btn-primary">

                + Registrar activo

            </a>

        </div>

        <!-- FILTROS -->

    <div class="stat-card">

        <form method="GET"
              action="../controllers/ActivoController.php"
              class="row g-3">

            <input type="hidden" name="accion" value="listar">

            <!-- Búsqueda -->
            <div class="col-md-4">

                <label class="form-label">
                    Buscar
                </label>

                <input
                    type="text"
                    name="busqueda"
                    class="form-control"
                    placeholder="Código, nombre, marca..."
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                >

            </div>


            <!-- Estado -->
            <div class="col-md-2">

                <label class="form-label">
                    Estado
                </label>

                <select name="estado" class="form-select">

                    <option value="">Todos</option>

                    <option value="Activo"
                        <?php echo ($estado === "Activo") ? "selected" : ""; ?>>
                        Activo
                    </option>

                    <option value="Inactivo"
                        <?php echo ($estado === "Inactivo") ? "selected" : ""; ?>>
                        Inactivo
                    </option>

                    <option value="En mantenimiento"
                        <?php echo ($estado === "En mantenimiento") ? "selected" : ""; ?>>
                        En mantenimiento
                    </option>

                    <option value="Fuera de servicio"
                        <?php echo ($estado === "Fuera de servicio") ? "selected" : ""; ?>>
                        Fuera de servicio
                    </option>

                </select>

            </div>


            <!-- Criticidad -->
            <div class="col-md-2">

                <label class="form-label">
                    Criticidad
                </label>

                <select name="criticidad" class="form-select">

                    <option value="">Todas</option>

                    <option value="Baja"
                        <?php echo ($criticidad === "Baja") ? "selected" : ""; ?>>
                        Baja
                    </option>

                    <option value="Media"
                        <?php echo ($criticidad === "Media") ? "selected" : ""; ?>>
                        Media
                    </option>

                    <option value="Alta"
                        <?php echo ($criticidad === "Alta") ? "selected" : ""; ?>>
                        Alta
                    </option>

                </select>

            </div>


            <!-- Tipo -->
            <div class="col-md-2">

                <label class="form-label">
                    Tipo de activo
                </label>

                <select name="tipo" class="form-select">

                    <option value="">Todos</option>

                    <?php foreach ($tipos as $tipoActivo): ?>

                        <option
                            value="<?php echo $tipoActivo["id_tipo_activo"]; ?>"
                            <?php echo ($tipo == $tipoActivo["id_tipo_activo"]) ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($tipoActivo["nombre"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Botones -->
            <div class="col-md-2 d-flex align-items-end gap-2">

                <button type="submit" class="btn btn-primary">
                    Buscar
                </button>

                <a href="../controllers/ActivoController.php?accion=listar"
                   class="btn btn-secondary">
                    Limpiar
                </a>

            </div>

        </form>

    </div>

        <!-- TABLA -->

        <div class="stat-card">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0">
                    Activos registrados
                </h5>

                <span class="text-muted">
                    <?php echo $activos->num_rows; ?> registros
                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Código</th>
                            <th>Activo</th>
                            <th>Tipo</th>
                            <th>Marca / Modelo</th>
                            <th>Ubicación</th>
                            <th>Criticidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($activos->num_rows > 0): ?>

                        <?php while ($activo = $activos->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($activo["codigo_activo"]); ?>
                                    </strong>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($activo["nombre"]); ?>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($activo["tipo_activo"]); ?>
                                </td>


                                <td>

                                    <?php echo htmlspecialchars($activo["marca"]); ?>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($activo["modelo"]); ?>
                                    </small>

                                </td>


                                <td>

                                    <?php echo htmlspecialchars($activo["ubicacion"]); ?>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($activo["area"]); ?>
                                    </small>

                                </td>


                                <td>

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

                                        <?php echo htmlspecialchars($activo["criticidad"]); ?>

                                    </span>

                                </td>


                                <td>
                                    <?php echo htmlspecialchars($activo["estado"]); ?>
                                </td>


                                <td>

                                    <div class="d-flex gap-1">

                                        <a
                                            href="../controllers/ActivoController.php?accion=ver&id=<?php echo $activo["id_activo"]; ?>"
                                            class="btn btn-sm btn-outline-primary">

                                            Ver

                                        </a>

                                        <a
                                            href="../controllers/ActivoController.php?accion=editar&id=<?php echo $activo["id_activo"]; ?>"
                                            class="btn btn-sm btn-outline-secondary">

                                            Editar

                                        </a>

                                    </div>

                                </td>

                            </tr>   

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-4">

                                No se encontraron activos.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>