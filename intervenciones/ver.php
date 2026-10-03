<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de intervención</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Detalle de intervención</h2>
            <p class="text-muted">
                Información de la intervención registrada.
            </p>
        </div>

        <a href="../controllers/IntervencionController.php?accion=listar"
           class="btn btn-secondary">
            Volver
        </a>

    </div>


    <div class="card">

        <div class="card-header">
            <strong>
                Intervención #<?php echo $intervencion["id_intervencion"]; ?>
            </strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <strong>Activo</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["codigo_activo"]); ?>
                        -
                        <?php echo htmlspecialchars($intervencion["activo"]); ?>
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Orden de mantenimiento</strong>

                    <p class="mb-0">
                        #<?php echo $intervencion["id_orden"]; ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Tipo de mantenimiento</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["tipo_mantenimiento"]); ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Fecha de intervención</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["fecha_intervencion"]); ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Fecha programada</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["fecha_programada"]); ?>
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Horas de trabajo</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["horas_trabajo"] ?? 0); ?>
                        horas
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Tiempo de parada</strong>

                    <p class="mb-0">
                        <?php echo htmlspecialchars($intervencion["tiempo_parada_horas"] ?? 0); ?>
                        horas
                    </p>

                </div>


                <div class="col-12 mb-3">

                    <strong>Resultado</strong>

                    <div class="border rounded p-3 mt-2 bg-light">

                        <?php echo nl2br(
                            htmlspecialchars(
                                $intervencion["resultado"] ?? "Sin registrar"
                            )
                        ); ?>

                    </div>

                </div>


                <div class="col-12 mb-3">

                    <strong>Causa de falla</strong>

                    <div class="border rounded p-3 mt-2 bg-light">

                        <?php echo nl2br(
                            htmlspecialchars(
                                $intervencion["causa_falla"] ?? "No registrada"
                            )
                        ); ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">

            <a
                href="../controllers/IntervencionController.php?accion=editar&id=<?php echo $intervencion["id_intervencion"]; ?>"
                class="btn btn-warning">
                Editar
            </a>

        </div>

    </div>

</div>

</body>
</html>