<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| URLs DE LAS APIs
|--------------------------------------------------------------------------
*/

$apiPrediccion = "https://geology-caption-aversion.ngrok-free.dev/api/prediccion";

$apiSimulacion = "https://basis-herbal-bow-lately.trycloudflare.com/api/simulacion";

/*
|--------------------------------------------------------------------------
| Variables
|--------------------------------------------------------------------------
*/

$activos = [];
$resultados = [];
$error = null;
$respuestaPrediccion = null;

/*
|--------------------------------------------------------------------------
| CONSULTAR ACTIVOS ACTUALES EN MYSQL
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    a.id_activo,
    a.codigo_activo,
    a.nombre,
    ta.nombre AS tipo_activo,
    a.criticidad,

    COALESCE(
        TIMESTAMPDIFF(YEAR, a.fecha_adquisicion, CURDATE()),
        0
    ) AS edad_activo_anios,

    COALESCE(
        a.horas_uso_acumuladas,
        0
    ) AS horas_uso_acumuladas,

    COALESCE(
        i.horas_trabajo,
        0
    ) AS horas_trabajo,

    COALESCE(
        i.tiempo_parada_horas,
        0
    ) AS tiempo_parada_horas,

    COALESCE(
        r.costo_repuestos,
        0
    ) AS costo_repuestos

FROM activo_tecnologico a

INNER JOIN tipo_activo ta
    ON ta.id_tipo_activo = a.id_tipo_activo

LEFT JOIN (
    SELECT
        om.id_activo,

        SUM(
            COALESCE(
                inv.horas_trabajo,
                0
            )
        ) AS horas_trabajo,

        SUM(
            COALESCE(
                inv.tiempo_parada_horas,
                0
            )
        ) AS tiempo_parada_horas

    FROM orden_mantenimiento om

    INNER JOIN intervencion inv
        ON inv.id_orden = om.id_orden

    GROUP BY om.id_activo

) i
    ON i.id_activo = a.id_activo

LEFT JOIN (
    SELECT
        om.id_activo,

        SUM(
            COALESCE(
                ir.costo_total,
                ir.cantidad * ir.costo_unitario,
                0
            )
        ) AS costo_repuestos

    FROM orden_mantenimiento om

    INNER JOIN intervencion inv
        ON inv.id_orden = om.id_orden

    INNER JOIN intervencion_repuesto ir
        ON ir.id_intervencion = inv.id_intervencion

    GROUP BY om.id_activo

) r
    ON r.id_activo = a.id_activo

ORDER BY a.id_activo
";

$consulta = $conn->query($sql);

if (!$consulta) {

    $error = "Error al consultar los activos: " . $conn->error;

} else {

    while ($fila = $consulta->fetch_assoc()) {

        $activos[] = $fila;
    }
}

/*
|--------------------------------------------------------------------------
| EJECUTAR PREDICCIÓN
|--------------------------------------------------------------------------
|
| Primero obtenemos la predicción de cada activo.
| Esa predicción será utilizada posteriormente
| por la estrategia "Inteligente".
|--------------------------------------------------------------------------
*/

if (!$error && count($activos) > 0) {

    $activos_api = [];

    foreach ($activos as $activo) {

        $activos_api[] = [
            "id_activo" => (int)$activo["id_activo"],

            "edad_activo_anios" =>
                (float)$activo["edad_activo_anios"],

            "horas_uso_acumuladas" =>
                (float)$activo["horas_uso_acumuladas"],

            "horas_trabajo" =>
                (float)$activo["horas_trabajo"],

            "tiempo_parada_horas" =>
                (float)$activo["tiempo_parada_horas"],

            "costo_repuestos" =>
                (float)$activo["costo_repuestos"],

            "tipo_activo" =>
                $activo["tipo_activo"],

            "criticidad" =>
                $activo["criticidad"]
        ];
    }

    $datosPrediccion = json_encode([
        "activos" => $activos_api
    ]);

    /*
    |--------------------------------------------------------------------------
    | CURL - PREDICCIÓN
    |--------------------------------------------------------------------------
    */

    $ch = curl_init($apiPrediccion);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => $datosPrediccion,

        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Content-Length: " . strlen($datosPrediccion)
        ],

        CURLOPT_TIMEOUT => 120,

        CURLOPT_CONNECTTIMEOUT => 15

    ]);

    $respuestaPrediccionRaw = curl_exec($ch);

    $errorCurl = curl_error($ch);

    $httpPrediccion = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR PREDICCIÓN
    |--------------------------------------------------------------------------
    */

    if ($respuestaPrediccionRaw === false || $errorCurl) {

        $error =
            "No fue posible comunicarse con la API de predicción. "
            . $errorCurl;

    } elseif ($httpPrediccion !== 200) {

        $error =
            "La API de predicción respondió con HTTP "
            . $httpPrediccion;

    } else {

        $respuestaPrediccion =
            json_decode(
                $respuestaPrediccionRaw,
                true
            );

        if (
            !is_array($respuestaPrediccion) ||
            !isset($respuestaPrediccion["resultados"])
        ) {

            $error =
                "La API de predicción devolvió una respuesta inválida.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| AGREGAR PREDICCIÓN A CADA ACTIVO
|--------------------------------------------------------------------------
*/

if (!$error && $respuestaPrediccion) {

    $predicciones = [];

    foreach (
        $respuestaPrediccion["resultados"]
        as $resultado
    ) {

        $id = (int)$resultado["id_activo"];

        $predicciones[$id] =
            (int)$resultado["prediccion"];
    }

    foreach ($activos as &$activo) {

        $id = (int)$activo["id_activo"];

        $activo["prediccion"] =
            $predicciones[$id] ?? 0;
    }

    unset($activo);
}

/*
|--------------------------------------------------------------------------
| EJECUTAR SIMULACIÓN MONTE CARLO
|--------------------------------------------------------------------------
*/

if (!$error && count($activos) > 0) {

    $activos_simulacion = [];

    foreach ($activos as $activo) {

        $activos_simulacion[] = [

            "id_activo" =>
                (int)$activo["id_activo"],

            "edad_activo_anios" =>
                (float)$activo["edad_activo_anios"],

            "horas_uso_acumuladas" =>
                (float)$activo["horas_uso_acumuladas"],

            "horas_trabajo" =>
                (float)$activo["horas_trabajo"],

            "tiempo_parada_horas" =>
                (float)$activo["tiempo_parada_horas"],

            "costo_repuestos" =>
                (float)$activo["costo_repuestos"],

            "tipo_activo" =>
                $activo["tipo_activo"],

            "criticidad" =>
                $activo["criticidad"],

            "prediccion" =>
                (int)$activo["prediccion"]
        ];
    }

    $datosSimulacion = json_encode([

        "dias_simulacion" => 365,

        "iteraciones" => 1000,

        "activos" => $activos_simulacion

    ]);

    /*
    |--------------------------------------------------------------------------
    | CURL - SIMULACIÓN
    |--------------------------------------------------------------------------
    */

    $ch = curl_init($apiSimulacion);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => $datosSimulacion,

        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Content-Length: " . strlen($datosSimulacion)
        ],

        CURLOPT_TIMEOUT => 180,

        CURLOPT_CONNECTTIMEOUT => 15

    ]);

    $respuestaSimulacionRaw = curl_exec($ch);

    $errorCurl = curl_error($ch);

    $httpSimulacion = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR SIMULACIÓN
    |--------------------------------------------------------------------------
    */

    if (
        $respuestaSimulacionRaw === false ||
        $errorCurl
    ) {

        $error =
            "No fue posible comunicarse con la API de simulación. "
            . $errorCurl;

    } elseif ($httpSimulacion !== 200) {

        $error =
            "La API de simulación respondió con HTTP "
            . $httpSimulacion;

    } else {

        $respuestaSimulacion =
            json_decode(
                $respuestaSimulacionRaw,
                true
            );

        if (
            !is_array($respuestaSimulacion) ||
            !isset($respuestaSimulacion["escenarios"])
        ) {

            $error =
                "La API de simulación devolvió una respuesta inválida.";

        } else {

            $resultados =
                $respuestaSimulacion["escenarios"];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

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

        </div>

    <?php endif; ?>


    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">
                Simulación de Mantenimiento
            </h2>

            <p class="text-muted mb-0">

                Comparación de estrategias mediante
                simulación Monte Carlo.

            </p>

        </div>

    </div>


    <?php if (!$error && !empty($resultados)): ?>


        <!-- ========================================================= -->
        <!-- INFORMACIÓN DE LA SIMULACIÓN -->
        <!-- ========================================================= -->

        <div class="alert alert-info">

            <div class="row">

                <div class="col-md-4">

                    <strong>Activos analizados:</strong>

                    <?= count($activos) ?>

                </div>

                <div class="col-md-4">

                    <strong>Periodo:</strong>

                    365 días

                </div>

                <div class="col-md-4">

                    <strong>Iteraciones:</strong>

                    1.000 simulaciones

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- TARJETAS -->
        <!-- ========================================================= -->

        <div class="row g-4 mb-4">

            <?php foreach ($resultados as $resultado): ?>

                <div class="col-md-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h5 class="card-title">

                                <?= htmlspecialchars(
                                    $resultado["estrategia"]
                                ) ?>

                            </h5>

                            <hr>

                            <p class="mb-2">

                                <strong>
                                    Fallas promedio:
                                </strong>

                                <?= number_format(
                                    (float)$resultado["fallas_promedio"],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                            </p>


                            <p class="mb-2">

                                <strong>
                                    Horas de parada promedio:
                                </strong>

                                <?= number_format(
                                    (float)$resultado["horas_parada_promedio"],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                            </p>


                            <p class="mb-2">

                                <strong>
                                    Costo promedio:
                                </strong>

                                $

                                <?= number_format(
                                    (float)$resultado["costo_promedio"],
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </p>


                            <p class="mb-0">

                                <strong>
                                    Activos intervenidos:
                                </strong>

                                <?= number_format(
                                    (float)$resultado["activos_intervenidos_promedio"],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                            </p>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <!-- ========================================================= -->
        <!-- RECOMENDACIÓN -->
        <!-- ========================================================= -->

        <?php

        $estrategiaRecomendada =
            $respuestaSimulacion["estrategia_recomendada"] ?? null;

        ?>

        <?php if ($estrategiaRecomendada): ?>

            <div class="alert alert-success shadow-sm">

                <h5 class="mb-2">

                    Estrategia recomendada:
                    <strong>
                        <?= htmlspecialchars(
                            $estrategiaRecomendada
                        ) ?>
                    </strong>

                </h5>

                <p class="mb-0">

                    Según las 1.000 iteraciones de Monte Carlo,
                    esta estrategia presentó el menor costo
                    promedio entre los escenarios simulados.

                </p>

            </div>

        <?php endif; ?>


        <!-- ========================================================= -->
        <!-- TABLA -->
        <!-- ========================================================= -->

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-3">

                    Comparación de estrategias

                </h5>

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th>
                                    Estrategia
                                </th>

                                <th>
                                    Fallas promedio
                                </th>

                                <th>
                                    Horas de parada
                                </th>

                                <th>
                                    Costo promedio
                                </th>

                                <th>
                                    Activos intervenidos
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($resultados as $resultado): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $resultado["estrategia"]
                                        ) ?>

                                    </strong>

                                </td>

                                <td>

                                    <?= number_format(
                                        (float)$resultado["fallas_promedio"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </td>

                                <td>

                                    <?= number_format(
                                        (float)$resultado["horas_parada_promedio"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </td>

                                <td>

                                    $

                                    <?= number_format(
                                        (float)$resultado["costo_promedio"],
                                        0,
                                        ",",
                                        "."
                                    ) ?>

                                </td>

                                <td>

                                    <?= number_format(
                                        (float)$resultado["activos_intervenidos_promedio"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </td>

                            </tr>

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

                    La simulación utiliza los datos actuales
                    registrados en el sistema y ejecuta
                    1.000 iteraciones Monte Carlo durante
                    un periodo de 365 días.

                </p>

                <p class="mb-2">

                    Se comparan tres estrategias:

                    <strong>reactiva</strong>,
                    <strong>preventiva</strong> e
                    <strong>inteligente</strong>.

                </p>

                <p class="mb-0">

                    La estrategia inteligente utiliza las
                    predicciones obtenidas mediante el modelo
                    de minería de datos para determinar qué
                    activos requieren intervención preventiva.

                </p>

            </div>

        </div>


    <?php elseif (!$error): ?>


        <div class="alert alert-warning">

            No se encontraron activos disponibles para
            realizar la simulación.

        </div>

    <?php endif; ?>


</div>

</body>

</html>