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

    <title>Nueva Orden de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Nueva Orden de Mantenimiento</h2>

            <p class="text-muted">
                Registrar una nueva orden para un activo
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
            No fue posible guardar la orden.
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
                    value="guardar"
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

                        <option value="">
                            Seleccione un activo
                        </option>

                        <?php while ($activo = $activos->fetch_assoc()): ?>

                            <option value="<?= $activo["id_activo"] ?>">

                                <?= htmlspecialchars($activo["codigo_activo"]) ?>
                                -
                                <?= htmlspecialchars($activo["nombre"]) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="row">


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

                            <option value="Preventivo">
                                Preventivo
                            </option>

                            <option value="Correctivo">
                                Correctivo
                            </option>

                            <option value="Predictivo">
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

                            <option value="Baja">
                                Baja
                            </option>

                            <option value="Media" selected>
                                Media
                            </option>

                            <option value="Alta">
                                Alta
                            </option>

                            <option value="Crítica">
                                Crítica
                            </option>

                        </select>

                    </div>


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

                            <option value="Pendiente" selected>
                                Pendiente
                            </option>

                            <option value="En proceso">
                                En proceso
                            </option>

                            <option value="Completada">
                                Completada
                            </option>

                            <option value="Cancelada">
                                Cancelada
                            </option>

                        </select>

                    </div>

                </div>


                <div class="row">


                    <!-- TÉCNICO -->

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

        <option value="">
            Seleccione un técnico
        </option>

        <?php while ($tecnico = $tecnicos->fetch_assoc()): ?>

            <option value="<?= $tecnico["id_tecnico"] ?>">
                <?= htmlspecialchars($tecnico["nombre"]) ?>
            </option>

        <?php endwhile; ?>

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
                        placeholder="Describa el mantenimiento que se debe realizar..."
                    ></textarea>

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
                        Guardar orden
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>