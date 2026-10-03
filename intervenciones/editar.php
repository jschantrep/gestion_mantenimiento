<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar intervención</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Editar intervención</h2>
            <p class="text-muted">
                Modifique la información de la intervención.
            </p>
        </div>

        <a href="../controllers/IntervencionController.php?accion=listar"
           class="btn btn-secondary">
            Volver
        </a>

    </div>


    <div class="card">

        <div class="card-body">

            <form method="POST"
                  action="../controllers/IntervencionController.php">

                <input type="hidden"
                       name="accion"
                       value="actualizar">

                <input type="hidden"
                       name="id_intervencion"
                       value="<?php echo $intervencion["id_intervencion"]; ?>">


                <!-- ORDEN -->

                <div class="mb-3">

                    <label class="form-label">
                        Orden de mantenimiento *
                    </label>

                    <select name="id_orden"
                            class="form-select"
                            required>

                        <?php while ($orden = $ordenes->fetch_assoc()): ?>

                            <option
                                value="<?php echo $orden["id_orden"]; ?>"
                                <?php echo (
                                    $orden["id_orden"] == $intervencion["id_orden"]
                                ) ? "selected" : ""; ?>
                            >

                                Orden #<?php echo $orden["id_orden"]; ?>
                                -
                                <?php echo htmlspecialchars($orden["codigo_activo"]); ?>
                                -
                                <?php echo htmlspecialchars($orden["activo"]); ?>
                                -
                                <?php echo htmlspecialchars($orden["tipo_mantenimiento"]); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="row">


                    <!-- FECHA -->

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Fecha de intervención *
                        </label>

                        <?php
                        $fecha = date(
                            'Y-m-d\TH:i',
                            strtotime($intervencion["fecha_intervencion"])
                        );
                        ?>

                        <input
                            type="datetime-local"
                            name="fecha_intervencion"
                            class="form-control"
                            value="<?php echo $fecha; ?>"
                            required
                        >

                    </div>


                    <!-- HORAS -->

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Horas de trabajo
                        </label>

                        <input
                            type="number"
                            name="horas_trabajo"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="<?php echo htmlspecialchars(
                                $intervencion["horas_trabajo"] ?? 0
                            ); ?>"
                        >

                    </div>


                    <!-- PARADA -->

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Tiempo de parada (horas)
                        </label>

                        <input
                            type="number"
                            name="tiempo_parada_horas"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="<?php echo htmlspecialchars(
                                $intervencion["tiempo_parada_horas"] ?? 0
                            ); ?>"
                        >

                    </div>

                </div>


                <!-- RESULTADO -->

                <div class="mb-3">

                    <label class="form-label">
                        Resultado de la intervención
                    </label>

                    <textarea
                        name="resultado"
                        class="form-control"
                        rows="4"
                    ><?php echo htmlspecialchars(
                        $intervencion["resultado"] ?? ""
                    ); ?></textarea>

                </div>


                <!-- CAUSA -->

                <div class="mb-3">

                    <label class="form-label">
                        Causa de falla
                    </label>

                    <textarea
                        name="causa_falla"
                        class="form-control"
                        rows="3"
                    ><?php echo htmlspecialchars(
                        $intervencion["causa_falla"] ?? ""
                    ); ?></textarea>

                </div>


                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="../controllers/IntervencionController.php?accion=listar"
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