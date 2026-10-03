<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intervenciones</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Intervenciones</h2>
            <p class="text-muted">
                Registro y seguimiento de las intervenciones realizadas.
            </p>
        </div>

        <a href="../controllers/IntervencionController.php?accion=crear"
           class="btn btn-primary">
            + Nueva intervención
        </a>
    </div>

    <?php if (isset($_GET["mensaje"])): ?>

        <?php if ($_GET["mensaje"] === "creada"): ?>
            <div class="alert alert-success">
                Intervención registrada correctamente.
            </div>
        <?php endif; ?>

        <?php if ($_GET["mensaje"] === "actualizada"): ?>
            <div class="alert alert-success">
                Intervención actualizada correctamente.
            </div>
        <?php endif; ?>

    <?php endif; ?>


    <!-- FILTROS -->

    <div class="card mb-4">
        <div class="card-body">

            <form method="GET"
                  action="../controllers/IntervencionController.php"
                  class="row g-3">

                <input type="hidden" name="accion" value="listar">

                <div class="col-md-6">

                    <label class="form-label">
                        Buscar
                    </label>

                    <input
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Código, activo o descripción"
                        value="<?php echo htmlspecialchars($_GET["busqueda"] ?? ""); ?>"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Tipo de mantenimiento
                    </label>

                    <select name="tipo" class="form-select">

                        <option value="">
                            Todos
                        </option>

                        <option value="Preventivo"
                            <?php echo (($_GET["tipo"] ?? "") === "Preventivo") ? "selected" : ""; ?>>
                            Preventivo
                        </option>

                        <option value="Correctivo"
                            <?php echo (($_GET["tipo"] ?? "") === "Correctivo") ? "selected" : ""; ?>>
                            Correctivo
                        </option>

                        <option value="Predictivo"
                            <?php echo (($_GET["tipo"] ?? "") === "Predictivo") ? "selected" : ""; ?>>
                            Predictivo
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button type="submit"
                            class="btn btn-secondary w-100">
                        Buscar
                    </button>

                </div>

            </form>

        </div>
    </div>


    <!-- TABLA -->

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Activo</th>
                            <th>Orden</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Parada</th>
                            <th>Horas trabajo</th>
                            <th>Resultado</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($intervenciones && $intervenciones->num_rows > 0): ?>

                        <?php while ($intervencion = $intervenciones->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $intervencion["id_intervencion"]; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($intervencion["codigo_activo"]); ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($intervencion["activo"]); ?>
                                    </small>
                                </td>

                                <td>
                                    #<?php echo $intervencion["id_orden"]; ?>
                                </td>

                                <td>

                                    <?php
                                    $tipo = $intervencion["tipo_mantenimiento"];

                                    if ($tipo === "Preventivo") {
                                        echo '<span class="badge bg-success">Preventivo</span>';
                                    } elseif ($tipo === "Correctivo") {
                                        echo '<span class="badge bg-danger">Correctivo</span>';
                                    } else {
                                        echo '<span class="badge bg-info text-dark">'
                                            . htmlspecialchars($tipo)
                                            . '</span>';
                                    }
                                    ?>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($intervencion["fecha_intervencion"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($intervencion["tiempo_parada_horas"]); ?> h
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($intervencion["horas_trabajo"]); ?> h
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($intervencion["resultado"] ?? "Sin registrar"); ?>
                                </td>

                                <td>

                                    <a
                                        href="../controllers/IntervencionController.php?accion=ver&id=<?php echo $intervencion["id_intervencion"]; ?>"
                                        class="btn btn-sm btn-info">
                                        Ver
                                    </a>

                                    <a
                                        href="../controllers/IntervencionController.php?accion=editar&id=<?php echo $intervencion["id_intervencion"]; ?>"
                                        class="btn btn-sm btn-warning">
                                        Editar
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="9"
                                class="text-center text-muted py-4">

                                No hay intervenciones registradas.

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