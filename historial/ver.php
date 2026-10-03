<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detalle del historial</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body>

<div class="container mt-4">


    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Detalle del historial</h2>

            <p class="text-muted">
                Información del cambio de estado.
            </p>

        </div>


        <a
            href="../controllers/HistorialController.php?accion=listar"
            class="btn btn-secondary"
        >
            Volver
        </a>

    </div>


    <div class="card">


        <div class="card-header">

            <strong>
                Registro #<?php echo $registro["id_historial"]; ?>
            </strong>

        </div>


        <div class="card-body">


            <div class="row">


                <div class="col-md-6 mb-3">

                    <strong>Activo</strong>

                    <p>
                        <?php echo htmlspecialchars(
                            $registro["codigo_activo"]
                        ); ?>

                        -

                        <?php echo htmlspecialchars(
                            $registro["activo"]
                        ); ?>
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Fecha</strong>

                    <p>
                        <?php echo htmlspecialchars(
                            $registro["fecha"]
                        ); ?>
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Estado anterior</strong>

                    <p>

                        <span class="badge bg-secondary">

                            <?php echo htmlspecialchars(
                                $registro["estado_anterior"]
                                ?? "Sin registro"
                            ); ?>

                        </span>

                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Estado nuevo</strong>

                    <p>

                        <span class="badge bg-primary">

                            <?php echo htmlspecialchars(
                                $registro["estado_nuevo"]
                                ?? "Sin registro"
                            ); ?>

                        </span>

                    </p>

                </div>


                <div class="col-12">

                    <strong>Motivo</strong>

                    <div class="border rounded p-3 mt-2 bg-light">

                        <?php echo nl2br(
                            htmlspecialchars(
                                $registro["motivo"]
                                ?? "Sin motivo registrado"
                            )
                        ); ?>

                    </div>

                </div>


            </div>

        </div>

    </div>

</div>

</body>

</html>