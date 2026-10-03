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

    <title>Editar activo - Gestión de Mantenimiento</title>

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

    <a href="../controllers/ActivoController.php?accion=listar" class="active">
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

        <div class="mb-4">

            <h2>
                Editar activo
            </h2>

            <p class="text-muted mb-0">
                Modifica la información registrada del activo.
            </p>

        </div>


        <div class="stat-card">

            <form
                method="POST"
                action="../controllers/ActivoController.php">

                <input
                    type="hidden"
                    name="accion"
                    value="actualizar">

                <input
                    type="hidden"
                    name="id_activo"
                    value="<?php echo $activo["id_activo"]; ?>">


                <div class="row g-3">


                    <!-- CÓDIGO -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Código del activo
                        </label>

                        <input
                            type="text"
                            name="codigo_activo"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["codigo_activo"]); ?>"
                            required>

                    </div>


                    <!-- NOMBRE -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Nombre del activo
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["nombre"]); ?>"
                            required>

                    </div>


                    <!-- TIPO -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Tipo de activo
                        </label>

                        <select
                            name="id_tipo_activo"
                            class="form-select"
                            required>

                            <?php while ($tipo = $tipos->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $tipo["id_tipo_activo"]; ?>"
                                    <?php
                                    echo (
                                        $tipo["id_tipo_activo"]
                                        == $activo["id_tipo_activo"]
                                    )
                                    ? "selected"
                                    : "";
                                    ?>>

                                    <?php echo htmlspecialchars($tipo["nombre"]); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- UBICACIÓN -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Ubicación
                        </label>

                        <select
                            name="id_ubicacion"
                            class="form-select"
                            required>

                            <?php while ($ubicacion = $ubicaciones->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $ubicacion["id_ubicacion"]; ?>"
                                    <?php
                                    echo (
                                        $ubicacion["id_ubicacion"]
                                        == $activo["id_ubicacion"]
                                    )
                                    ? "selected"
                                    : "";
                                    ?>>

                                    <?php
                                    echo htmlspecialchars(
                                        $ubicacion["nombre"]
                                        . " - "
                                        . $ubicacion["edificio"]
                                        . " - "
                                        . $ubicacion["area"]
                                    );
                                    ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- MARCA -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Marca
                        </label>

                        <input
                            type="text"
                            name="marca"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["marca"]); ?>">

                    </div>


                    <!-- MODELO -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Modelo
                        </label>

                        <input
                            type="text"
                            name="modelo"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["modelo"]); ?>">

                    </div>


                    <!-- SERIAL -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Número de serie
                        </label>

                        <input
                            type="text"
                            name="numero_serie"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["numero_serie"]); ?>">

                    </div>


                    <!-- FECHA -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Fecha de adquisición
                        </label>

                        <input
                            type="date"
                            name="fecha_adquisicion"
                            class="form-control"
                            value="<?php echo htmlspecialchars($activo["fecha_adquisicion"]); ?>">

                    </div>


                    <!-- CRITICIDAD -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Criticidad
                        </label>

                        <select
                            name="criticidad"
                            class="form-select"
                            required>

                            <option
                                value="Baja"
                                <?php echo $activo["criticidad"] === "Baja" ? "selected" : ""; ?>>

                                Baja

                            </option>

                            <option
                                value="Media"
                                <?php echo $activo["criticidad"] === "Media" ? "selected" : ""; ?>>

                                Media

                            </option>

                            <option
                                value="Alta"
                                <?php echo $activo["criticidad"] === "Alta" ? "selected" : ""; ?>>

                                Alta

                            </option>

                        </select>

                    </div>


                    <!-- ESTADO -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Estado
                        </label>

                        <select
                            name="estado"
                            class="form-select"
                            required>

                            <option
                                value="Activo"
                                <?php echo $activo["estado"] === "Activo" ? "selected" : ""; ?>>

                                Activo

                            </option>

                            <option
                                value="Inactivo"
                                <?php echo $activo["estado"] === "Inactivo" ? "selected" : ""; ?>>

                                Inactivo

                            </option>

                            <option
                                value="En mantenimiento"
                                <?php echo $activo["estado"] === "En mantenimiento" ? "selected" : ""; ?>>

                                En mantenimiento

                            </option>

                            <option
                                value="Fuera de servicio"
                                <?php echo $activo["estado"] === "Fuera de servicio" ? "selected" : ""; ?>>

                                Fuera de servicio

                            </option>

                        </select>

                    </div>


                    <!-- HORAS -->

                    <div class="col-md-4">

                        <label class="form-label">
                            Horas de uso acumuladas
                        </label>

                        <input
                            type="number"
                            name="horas_uso_acumuladas"
                            class="form-control"
                            min="0"
                            value="<?php echo $activo["horas_uso_acumuladas"]; ?>"
                            required>

                    </div>

                </div>


                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="../controllers/ActivoController.php?accion=ver&id=<?php echo $activo["id_activo"]; ?>"
                        class="btn btn-secondary">

                        Cancelar

                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>