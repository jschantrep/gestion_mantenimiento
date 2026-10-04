<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";


// =====================================================
// CONFIGURACIÓN API
// =====================================================

$url_api = "https://geology-caption-aversion.ngrok-free.dev/api/prediccion";


// =====================================================
// CONSULTA DE ACTIVOS
// =====================================================

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

    COALESCE(a.horas_uso_acumuladas, 0) AS horas_uso_acumuladas,

    COALESCE(i.horas_trabajo, 0) AS horas_trabajo,

    COALESCE(i.tiempo_parada_horas, 0) AS tiempo_parada_horas,

    COALESCE(r.costo_repuestos, 0) AS costo_repuestos

FROM activo_tecnologico a

INNER JOIN tipo_activo ta
    ON ta.id_tipo_activo = a.id_tipo_activo

LEFT JOIN (
    SELECT
        om.id_activo,
        SUM(COALESCE(inv.horas_trabajo, 0)) AS horas_trabajo,
        SUM(COALESCE(inv.tiempo_parada_horas, 0)) AS tiempo_parada_horas

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

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}

// =====================================================
// PREPARAR DATOS PARA LA API
// =====================================================

$activos_api = [];

$activos_info = [];

while ($activo = $resultado->fetch_assoc()) {

    $activos_api[] = [

        "id_activo" => (int) $activo["id_activo"],

        "edad_activo_anios" =>
            (float) $activo["edad_activo_anios"],

        "horas_uso_acumuladas" =>
            (float) $activo["horas_uso_acumuladas"],

        "horas_trabajo" =>
            (float) $activo["horas_trabajo"],

        "tiempo_parada_horas" =>
            (float) $activo["tiempo_parada_horas"],

        "costo_repuestos" =>
            (float) $activo["costo_repuestos"],

        "tipo_activo" =>
            $activo["tipo_activo"],

        "criticidad" =>
            $activo["criticidad"]

    ];


    // Guardamos información para mostrar posteriormente

    $activos_info[$activo["id_activo"]] = $activo;
}


// =====================================================
// VALIDAR QUE EXISTAN ACTIVOS
// =====================================================

if (count($activos_api) === 0) {

    die("No existen activos registrados para analizar.");

}


// =====================================================
// CREAR JSON
// =====================================================

$datos_json = json_encode([

    "activos" => $activos_api

]);


// =====================================================
// ENVIAR A LA API FLASK
// =====================================================

$ch = curl_init($url_api);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $datos_json);

curl_setopt($ch, CURLOPT_HTTPHEADER, [

    "Content-Type: application/json",

    "Content-Length: " . strlen($datos_json)

]);

curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$respuesta_api = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$error_curl = curl_error($ch);

curl_close($ch);


// =====================================================
// VALIDAR RESPUESTA
// =====================================================

if ($respuesta_api === false) {

    die("Error conectando con la API: " . $error_curl);

}


if ($http_code !== 200) {

    die(
        "La API respondió con HTTP "
        . $http_code
        . "<br><br>"
        . htmlspecialchars($respuesta_api)
    );

}


$resultados_api = json_decode($respuesta_api, true);


if (!$resultados_api) {

    die("La respuesta de la API no es un JSON válido.");

}


if (isset($resultados_api["error"])) {

    die(
        "Error de la API: "
        . htmlspecialchars($resultados_api["error"])
    );

}


// =====================================================
// RESULTADOS
// =====================================================

$predicciones = $resultados_api["resultados"];

$total_activos = count($predicciones);

$total_fallas = 0;

$total_normales = 0;


foreach ($predicciones as $prediccion) {

    if ($prediccion["prediccion"] == 1) {

        $total_fallas++;

    } else {

        $total_normales++;

    }

}


$porcentaje_fallas = $total_activos > 0
    ? ($total_fallas / $total_activos) * 100
    : 0;

$porcentaje_normales = $total_activos > 0
    ? ($total_normales / $total_activos) * 100
    : 0;

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Predicción de Mantenimiento</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Predicción de Mantenimiento</h2>

            <p class="text-muted mb-0">

                Análisis predictivo de todos los activos registrados.

            </p>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- INDICADORES -->
    <!-- ================================================= -->

    <!-- ================================================= -->
<!-- INDICADORES -->
<!-- ================================================= -->

<div class="row mb-4">

    <div class="col-md-3">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Activos analizados
                </h6>

                <h2>
                    <?php echo $total_activos; ?>
                </h2>

                <small class="text-muted">
                    Total de activos evaluados
                </small>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Posible falla futura
                </h6>

                <h2 class="text-danger">
                    <?php echo $total_fallas; ?>
                </h2>

                <small class="text-danger">

                    <?php
                    echo number_format(
                        $porcentaje_fallas,
                        2
                    );
                    ?>%

                    del total

                </small>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Operación normal
                </h6>

                <h2 class="text-success">
                    <?php echo $total_normales; ?>
                </h2>

                <small class="text-success">

                    <?php
                    echo number_format(
                        $porcentaje_normales,
                        2
                    );
                    ?>%

                    del total

                </small>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Modelo
                </h6>

                <h5 class="mt-2">
                    Decision Tree
                </h5>

                <small class="text-muted">
                    Predicción de falla futura
                </small>

            </div>

        </div>

    </div>

</div>

<!-- ================================================= -->
<!-- INTERPRETACIÓN -->
<!-- ================================================= -->

<div class="alert alert-info shadow-sm mb-4">

    <h5 class="alert-heading">
        Interpretación del análisis
    </h5>

    <p class="mb-2">

        Se analizaron
        <strong><?php echo $total_activos; ?></strong>
        activos registrados en el sistema.

        El modelo clasificó
        <strong><?php echo $total_fallas; ?></strong>
        activos como

        <strong>posibles casos de falla futura</strong>,

        correspondientes al

        <strong>
            <?php
            echo number_format(
                $porcentaje_fallas,
                2
            );
            ?>%
        </strong>

        del total.

    </p>

    <p class="mb-0">

        Estos activos pueden ser considerados
        <strong>prioritarios para revisión preventiva</strong>,
        especialmente aquellos que presentan una criticidad alta
        y una predicción de falla futura.

    </p>

</div>

    <!-- ================================================= -->
    <!-- TABLA -->
    <!-- ================================================= -->

    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="card-title mb-3">

                Resultados de la predicción

            </h5>


            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>Activo</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Criticidad</th>
                            <th>Edad</th>
                            <th>Horas uso</th>
                            <th>Horas mantenimiento</th>
                            <th>Tiempo parada</th>
                            <th>Costo histórico repuestos</th>
                            <th>Predicción</th>
                            <th>Probabilidad</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($predicciones as $prediccion): ?>

                        <?php

                        $id = $prediccion["id_activo"];

                        $activo = $activos_info[$id];

                        $es_falla =
                            $prediccion["prediccion"] == 1;

                        ?>

                        <tr>

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $activo["codigo_activo"]
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $activo["nombre"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $activo["tipo_activo"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $activo["criticidad"]
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo $activo["edad_activo_anios"];
                                ?>
                                años

                            </td>


                            <td>

                                <?php
                                echo number_format(
                                    $activo["horas_uso_acumuladas"],
                                    2
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo number_format(
                                    $activo["horas_trabajo"],
                                    2
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo number_format(
                                    $activo["tiempo_parada_horas"],
                                    2
                                );
                                ?>
                                h

                            </td>


                            <td>

                                $
                                <?php
                                echo number_format(
                                    $activo["costo_repuestos"],
                                    0,
                                    ",",
                                    "."
                                );
                                ?>

                            </td>


                            <td>

                                <?php if ($es_falla): ?>

                                    <span class="badge bg-danger">

                                        Falla futura

                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-success">

                                        Operación normal

                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php

                                if (
                                    $prediccion["probabilidad_falla"]
                                    !== null
                                ) {

                                    echo number_format(
                                        $prediccion[
                                            "probabilidad_falla"
                                        ] * 100,
                                        2
                                    );

                                    echo "%";

                                } else {

                                    echo "N/A";

                                }

                                ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>