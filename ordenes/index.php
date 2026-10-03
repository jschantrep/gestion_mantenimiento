<?php
// Esta vista es cargada desde OrdenController.php.
// No iniciar sesión aquí.
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Órdenes de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Órdenes de Mantenimiento</h2>
            <p class="text-muted mb-0">
                Gestión y seguimiento de órdenes de mantenimiento
            </p>
        </div>

        <a
            href="OrdenController.php?accion=crear"
            class="btn btn-primary"
        >
            + Nueva orden
        </a>

    </div>


    <?php if (isset($_GET["mensaje"])): ?>

        <div class="alert alert-success">

            <?php
            switch ($_GET["mensaje"]) {

                case "creada":
                    echo "Orden creada correctamente.";
                    break;

                case "actualizada":
                    echo "Orden actualizada correctamente.";
                    break;

                case "cancelada":
                    echo "Orden cancelada correctamente.";
                    break;

                default:
                    echo "Operación realizada correctamente.";
            }
            ?>

        </div>

    <?php endif; ?>


    <?php if (isset($_GET["error"])): ?>

        <div class="alert alert-danger">
            Ocurrió un error al realizar la operación.
        </div>

    <?php endif; ?>


    <!-- FILTROS -->

    <div class="card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="OrdenController.php"
                class="row g-3"
            >

                <input
                    type="hidden"
                    name="accion"
                    value="listar"
                >

                <div class="col-md-4">

                    <label class="form-label">
                        Buscar
                    </label>

                    <input
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Código, activo o descripción"
                        value="<?= htmlspecialchars($_GET["busqueda"] ?? "") ?>"
                    >

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="form-select"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option value="Pendiente"
                            <?= ($_GET["estado"] ?? "") === "Pendiente" ? "selected" : "" ?>>
                            Pendiente
                        </option>

                        <option value="En proceso"
                            <?= ($_GET["estado"] ?? "") === "En proceso" ? "selected" : "" ?>>
                            En proceso
                        </option>

                        <option value="Completada"
                            <?= ($_GET["estado"] ?? "") === "Completada" ? "selected" : "" ?>>
                            Completada
                        </option>

                        <option value="Cancelada"
                            <?= ($_GET["estado"] ?? "") === "Cancelada" ? "selected" : "" ?>>
                            Cancelada
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Prioridad
                    </label>

                    <select
                        name="prioridad"
                        class="form-select"
                    >

                        <option value="">
                            Todas
                        </option>

                        <option value="Baja"
                            <?= ($_GET["prioridad"] ?? "") === "Baja" ? "selected" : "" ?>>
                            Baja
                        </option>

                        <option value="Media"
                            <?= ($_GET["prioridad"] ?? "") === "Media" ? "selected" : "" ?>>
                            Media
                        </option>

                        <option value="Alta"
                            <?= ($_GET["prioridad"] ?? "") === "Alta" ? "selected" : "" ?>>
                            Alta
                        </option>

                        <option value="Crítica"
                            <?= ($_GET["prioridad"] ?? "") === "Crítica" ? "selected" : "" ?>>
                            Crítica
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Tipo
                    </label>

                    <select
                        name="tipo"
                        class="form-select"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option value="Preventivo"
                            <?= ($_GET["tipo"] ?? "") === "Preventivo" ? "selected" : "" ?>>
                            Preventivo
                        </option>

                        <option value="Correctivo"
                            <?= ($_GET["tipo"] ?? "") === "Correctivo" ? "selected" : "" ?>>
                            Correctivo
                        </option>

                        <option value="Predictivo"
                            <?= ($_GET["tipo"] ?? "") === "Predictivo" ? "selected" : "" ?>>
                            Predictivo
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-dark w-100"
                    >
                        Filtrar
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- TABLA -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>
                            <th>Activo</th>
                            <th>Tipo</th>
                            <th>Fecha programada</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($ordenes && $ordenes->num_rows > 0): ?>

                        <?php while ($orden = $ordenes->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?= $orden["id_orden"] ?>
                                </td>

                                <td>

                                    <strong>
                                        <?= htmlspecialchars($orden["codigo_activo"]) ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        <?= htmlspecialchars($orden["activo"]) ?>
                                    </small>

                                </td>

                                <td>
                                    <?= htmlspecialchars($orden["tipo_mantenimiento"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($orden["fecha_programada"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($orden["prioridad"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($orden["estado_orden"]) ?>
                                </td>

                                <td>

                                    <a
                                        href="OrdenController.php?accion=ver&id=<?= $orden["id_orden"] ?>"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="OrdenController.php?accion=editar&id=<?= $orden["id_orden"] ?>"
                                        class="btn btn-sm btn-outline-warning"
                                    >
                                        Editar
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-4 text-muted"
                            >
                                No hay órdenes de mantenimiento registradas.
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