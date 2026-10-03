<?php
// Vista cargada desde OrdenController.php
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar Orden</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Editar Orden</h2>

            <p class="text-muted mb-0">
                Orden #<?= $orden["id_orden"] ?>
            </p>

        </div>

        <a
            href="../controllers/OrdenController.php?accion=listar"
            class="btn btn-secondary"
        >
            Volver
        </a>

    </div>


    <?php if (isset($_GET["error"])): ?>

        <div class="alert alert-danger">
            No fue posible actualizar la orden.
        </div>

    <?php endif; ?>


    <div class="card">

        <div class="card-body">

            <form
                method="POST"
                action="../controllers/OrdenController.php"
            >

                <input
                    type="hidden"
                    name="accion"
                    value="actualizar"
                >

                <input
                    type="hidden"
                    name="id_orden"
                    value="<?= $orden["id_orden"] ?>"
                >


                <!-- ACTIVO -->

                <div class="mb-3">

                    <label class="form-label">
                        Activo *
                    </label>

                    <select
                        name="id_activo"
                        class="form-select"
                        required
                    >

                        <?php while ($activo = $activos->fetch_assoc()): ?>

                            <option
                                value="<?= $activo["id_activo"] ?>"
                                <?= $activo["id_activo"] == $orden["id_activo"] ? "selected" : "" ?>
                            >

                                <?= htmlspecialchars($activo["codigo_activo"]) ?>
                                -
                                <?= htmlspecialchars($activo["nombre"]) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="row">


                    <!-- TÉCNICO -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Técnico *
                        </label>

                        <select
                            name="id_tecnico"
                            class="form-select"
                            required
                        >

                            <?php while ($tecnico = $tecnicos->fetch_assoc()): ?>

                                <option
                                    value="<?= $tecnico["id_tecnico"] ?>"
                                    <?= $tecnico["id_tecnico"] == $orden["id_tecnico"] ? "selected" : "" ?>
                                >

                                    <?= htmlspecialchars($tecnico["nombre"]) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- TIPO -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Tipo de mantenimiento *
                        </label>

                        <select
                            name="tipo_mantenimiento"
                            class="form-select"
                            required
                        >

                            <option
                                value="Preventivo"
                                <?= $orden["tipo_mantenimiento"] === "Preventivo" ? "selected" : "" ?>
                            >
                                Preventivo
                            </option>

                            <option
                                value="Correctivo"
                                <?= $orden["tipo_mantenimiento"] === "Correctivo" ? "selected" : "" ?>
                            >
                                Correctivo
                            </option>

                            <option
                                value="Predictivo"
                                <?= $orden["tipo_mantenimiento"] === "Predictivo" ? "selected" : "" ?>
                            >
                                Predictivo
                            </option>

                        </select>

                    </div>


                    <!-- PRIORIDAD -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Prioridad *
                        </label>

                        <select
                            name="prioridad"
                            class="form-select"
                            required
                        >

                            <option
                                value="Baja"
                                <?= $orden["prioridad"] === "Baja" ? "selected" : "" ?>
                            >
                                Baja
                            </option>

                            <option
                                value="Media"
                                <?= $orden["prioridad"] === "Media" ? "selected" : "" ?>
                            >
                                Media
                            </option>

                            <option
                                value="Alta"
                                <?= $orden["prioridad"] === "Alta" ? "selected" : "" ?>
                            >
                                Alta
                            </option>

                            <option
                                value="Crítica"
                                <?= $orden["prioridad"] === "Crítica" ? "selected" : "" ?>
                            >
                                Crítica
                            </option>

                        </select>

                    </div>

                </div>


                <div class="row">


                    <!-- ESTADO -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Estado *
                        </label>

                        <select
                            name="estado_orden"
                            class="form-select"
                            required
                        >

                            <option
                                value="Pendiente"
                                <?= $orden["estado_orden"] === "Pendiente" ? "selected" : "" ?>
                            >
                                Pendiente
                            </option>

                            <option
                                value="En proceso"
                                <?= $orden["estado_orden"] === "En proceso" ? "selected" : "" ?>
                            >
                                En proceso
                            </option>

                            <option
                                value="Completada"
                                <?= $orden["estado_orden"] === "Completada" ? "selected" : "" ?>
                            >
                                Completada
                            </option>

                            <option
                                value="Cancelada"
                                <?= $orden["estado_orden"] === "Cancelada" ? "selected" : "" ?>
                            >
                                Cancelada
                            </option>

                        </select>

                    </div>


                    <!-- FECHA PROGRAMADA -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Fecha programada *
                        </label>

                        <input
                            type="date"
                            name="fecha_programada"
                            class="form-control"
                            value="<?= htmlspecialchars($orden["fecha_programada"] ?? "") ?>"
                            required
                        >

                    </div>


                    <!-- FECHA INICIO -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Fecha de inicio
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            class="form-control"
                            value="<?= htmlspecialchars($orden["fecha_inicio"] ?? "") ?>"
                        >

                    </div>

                </div>


                <!-- FECHA FIN -->

                <div class="mb-3">

                    <label class="form-label">
                        Fecha de finalización
                    </label>

                    <input
                        type="date"
                        name="fecha_fin"
                        class="form-control"
                        value="<?= htmlspecialchars($orden["fecha_fin"] ?? "") ?>"
                    >

                </div>


                <!-- DESCRIPCIÓN -->

                <div class="mb-4">

                    <label class="form-label">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        class="form-control"
                        rows="4"
                    ><?= htmlspecialchars($orden["descripcion"] ?? "") ?></textarea>

                </div>


                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="../controllers/OrdenController.php?accion=listar"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>