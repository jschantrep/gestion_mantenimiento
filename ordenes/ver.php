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

    <title>Detalle de Orden</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Detalle de Orden</h2>

            <p class="text-muted mb-0">
                Información de la orden de mantenimiento
            </p>
        </div>

        <div>

            <a
                href="../controllers/OrdenController.php?accion=editar&id=<?= $orden["id_orden"] ?>"
                class="btn btn-warning"
            >
                Editar
            </a>

            <a
                href="../controllers/OrdenController.php?accion=listar"
                class="btn btn-secondary"
            >
                Volver
            </a>

        </div>

    </div>


    <div class="card">

        <div class="card-header">
            <strong>
                Orden #<?= $orden["id_orden"] ?>
            </strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <strong>Activo</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["codigo_activo"]) ?>
                        -
                        <?= htmlspecialchars($orden["activo"]) ?>
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Marca / Modelo</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["marca"] ?? "") ?>
                        /
                        <?= htmlspecialchars($orden["modelo"] ?? "") ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Tipo de mantenimiento</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["tipo_mantenimiento"]) ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Prioridad</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["prioridad"]) ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Estado</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["estado_orden"]) ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Fecha programada</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["fecha_programada"] ?? "") ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Fecha de inicio</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["fecha_inicio"] ?? "") ?>
                    </p>

                </div>


                <div class="col-md-4 mb-3">

                    <strong>Fecha de finalización</strong>

                    <p class="mb-0">
                        <?= htmlspecialchars($orden["fecha_fin"] ?? "") ?>
                    </p>

                </div>


                <div class="col-md-12 mb-3">

                    <strong>Descripción</strong>

                    <div class="border rounded p-3 mt-2 bg-light">

                        <?= nl2br(
                            htmlspecialchars(
                                $orden["descripcion"] ?? "Sin descripción"
                            )
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>