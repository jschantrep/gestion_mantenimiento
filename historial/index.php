<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Historial de activos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body>

<div class="container mt-4">


    <!-- ENCABEZADO -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Historial de activos</h2>

            <p class="text-muted">
                Registro de cambios de estado de los activos.
            </p>

        </div>

    </div>


    <!-- MENSAJES -->

    <?php if (isset($_GET["error"]) && $_GET["error"] === "no_encontrado"): ?>

        <div class="alert alert-warning">
            El registro solicitado no fue encontrado.
        </div>

    <?php endif; ?>


    <!-- BUSCADOR -->

    <div class="card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="../controllers/HistorialController.php"
                class="row g-3"
            >

                <input
                    type="hidden"
                    name="accion"
                    value="listar"
                >


                <div class="col-md-10">

                    <label class="form-label">
                        Buscar
                    </label>

                    <input
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Código, activo, estado o motivo"
                        value="<?php echo htmlspecialchars(
                            $_GET["busqueda"] ?? ""
                        ); ?>"
                    >

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-secondary w-100"
                    >
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

                            <th>Fecha</th>

                            <th>Estado anterior</th>

                            <th>Estado nuevo</th>

                            <th>Motivo</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($historial && $historial->num_rows > 0): ?>

                        <?php while ($registro = $historial->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $registro["id_historial"]; ?>
                                </td>


                                <td>

                                    <strong>
                                        <?php echo htmlspecialchars(
                                            $registro["codigo_activo"]
                                        ); ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(
                                            $registro["activo"]
                                        ); ?>
                                    </small>

                                </td>


                                <td>
                                    <?php echo htmlspecialchars(
                                        $registro["fecha"]
                                    ); ?>
                                </td>


                                <td>

                                    <span class="badge bg-secondary">

                                        <?php echo htmlspecialchars(
                                            $registro["estado_anterior"]
                                            ?? "Sin registro"
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="badge bg-primary">

                                        <?php echo htmlspecialchars(
                                            $registro["estado_nuevo"]
                                            ?? "Sin registro"
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <?php echo htmlspecialchars(
                                        $registro["motivo"]
                                        ?? "Sin motivo"
                                    ); ?>

                                </td>


                                <td>

                                    <a
                                        href="../controllers/HistorialController.php?accion=ver&id=<?php echo $registro["id_historial"]; ?>"
                                        class="btn btn-sm btn-info"
                                    >
                                        Ver
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>


                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center text-muted py-4"
                            >

                                No hay registros en el historial.

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