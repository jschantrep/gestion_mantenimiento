<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Archivo CSV de resultados
|--------------------------------------------------------------------------
*/

$archivo = __DIR__ . "/../datos/simulacion/comparacion_final_simulacion.csv";

$resultados = [];
$error = null;

/*
|--------------------------------------------------------------------------
| Verificar archivo
|--------------------------------------------------------------------------
*/

if (!file_exists($archivo)) {

    $error = "No se encontró el archivo CSV.";

} elseif (filesize($archivo) === 0) {

    $error = "El archivo CSV existe, pero está vacío.";

} else {

    $handle = fopen($archivo, "r");

    if ($handle === false) {

        $error = "No fue posible abrir el archivo CSV.";

    } else {

        /*
        | Detectar automáticamente si usa coma o punto y coma
        */
        $primeraLinea = fgets($handle);

        rewind($handle);

        if (strpos($primeraLinea, ";") !== false) {
            $separador = ";";
        } else {
            $separador = ",";
        }

        /*
        | Leer encabezados
        */
        $encabezados = fgetcsv($handle, 0, $separador);

        if ($encabezados !== false) {

            // Eliminar BOM UTF-8 si existe
            $encabezados[0] = preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $encabezados[0]
            );

            while (($fila = fgetcsv($handle, 0, $separador)) !== false) {

                if (count($fila) === count($encabezados)) {

                    $registro = array_combine(
                        $encabezados,
                        $fila
                    );

                    if ($registro !== false) {
                        $resultados[] = $registro;
                    }
                }
            }
        }

        fclose($handle);
    }
}


/*
|--------------------------------------------------------------------------
| Buscar estrategias
|--------------------------------------------------------------------------
*/

$datos = [
    "Reactivo" => null,
    "Preventivo" => null,
    "Inteligente" => null
];


foreach ($resultados as $resultado) {

    if (!isset($resultado["estrategia"])) {
        continue;
    }

    $estrategia = trim($resultado["estrategia"]);

    if (isset($datos[$estrategia])) {

        $datos[$estrategia] = $resultado;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simulación de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container-fluid py-4">
    <?php if ($error): ?>

    <div class="alert alert-danger">
        <strong>Error:</strong>
        <?= htmlspecialchars($error) ?>

        <hr>

        <small>
            Ruta buscada:
            <?= htmlspecialchars($archivo) ?>
        </small>
    </div>

<?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">Simulación de Mantenimiento</h2>

            <p class="text-muted mb-0">
                Comparación de estrategias de mantenimiento mediante simulación.
            </p>
        </div>

    </div>


    <?php if (empty($resultados)): ?>

        <div class="alert alert-warning">

            No se encontraron resultados de la simulación.

            <br>

            Verifica que exista el archivo:

            <strong>
                datos/simulacion/comparacion_final_simulacion.csv
            </strong>

        </div>

    <?php else: ?>


        <!-- ========================================================= -->
        <!-- TARJETAS -->
        <!-- ========================================================= -->

        <div class="row g-4 mb-4">

            <?php foreach ($datos as $estrategia => $resultado): ?>

                <?php if ($resultado): ?>

                    <div class="col-md-4">

                        <div class="card shadow-sm h-100">

                            <div class="card-body">

                                <h5 class="card-title">
                                    <?= htmlspecialchars($estrategia) ?>
                                </h5>

                                <hr>

                                <p class="mb-2">
                                    <strong>Fallas:</strong>
                                    <?= number_format(
                                        (float)$resultado["fallas_totales"],
                                        0,
                                        ",",
                                        "."
                                    ) ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Horas de parada:</strong>
                                    <?= number_format(
                                        (float)$resultado["horas_parada_totales"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>
                                </p>

                                <p class="mb-0">
                                    <strong>Costo total:</strong>
                                    $
                                    <?= number_format(
                                        (float)$resultado["costo_total"],
                                        0,
                                        ",",
                                        "."
                                    ) ?>
                                </p>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>


        <!-- ========================================================= -->
        <!-- TABLA -->
        <!-- ========================================================= -->

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-3">
                    Comparación de estrategias
                </h5>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>Estrategia</th>

                                <th>Fallas totales</th>

                                <th>Horas de parada</th>

                                <th>Costo total</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($datos as $estrategia => $resultado): ?>

                            <?php if ($resultado): ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($estrategia) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= number_format(
                                            (float)$resultado["fallas_totales"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= number_format(
                                            (float)$resultado["horas_parada_totales"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>
                                    </td>

                                    <td>
                                        $
                                        <?= number_format(
                                            (float)$resultado["costo_total"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endif; ?>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- INTERPRETACIÓN -->
        <!-- ========================================================= -->

        <div class="card shadow-sm mt-4">

            <div class="card-body">

                <h5>
                    Interpretación de la simulación
                </h5>

                <p class="mb-2">
                    La simulación permite comparar el comportamiento de
                    diferentes estrategias de mantenimiento durante un
                    periodo de 365 días.
                </p>

                <p class="mb-2">
                    La estrategia reactiva presentó el mayor número de
                    fallas y horas de parada dentro del escenario simulado.
                </p>

                <p class="mb-0">
                    La estrategia inteligente utiliza el modelo predictivo
                    desarrollado en la etapa de minería de datos para
                    generar alertas y realizar intervenciones preventivas.
                </p>

            </div>

        </div>


    <?php endif; ?>

</div>

</body>

</html>