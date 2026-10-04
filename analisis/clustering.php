<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| API DE MINERÍA
|--------------------------------------------------------------------------
*/

$apiSegmentacion =
    "https://geology-caption-aversion.ngrok-free.dev/api/segmentacion";


/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$activos = [];
$resultados = [];
$distribucion = [];
$error = null;


/*
|--------------------------------------------------------------------------
| CONSULTAR ACTIVOS ACTUALES EN MYSQL
|--------------------------------------------------------------------------
|
| Las variables corresponden exactamente a las utilizadas
| durante el entrenamiento del modelo K-Means.
|
*/

$sql = "

SELECT

    a.id_activo,
    a.codigo_activo,
    a.nombre,
    ta.nombre AS tipo_activo,
    a.criticidad,

    COALESCE(
        TIMESTAMPDIFF(
            YEAR,
            a.fecha_adquisicion,
            CURDATE()
        ),
        0
    ) AS edad_activo_anios,

    COALESCE(
        a.horas_uso_acumuladas,
        0
    ) AS horas_uso_acumuladas,


    /* Total de mantenimientos */

    COALESCE(
        m.num_mantenimientos,
        0
    ) AS num_mantenimientos,


    /* Mantenimientos correctivos */

    COALESCE(
        m.num_correctivos,
        0
    ) AS num_correctivos,


    /* Mantenimientos preventivos */

    COALESCE(
        m.num_preventivos,
        0
    ) AS num_preventivos,


    /* Mantenimientos predictivos */

    COALESCE(
        m.num_predictivos,
        0
    ) AS num_predictivos,


    /* Horas de trabajo */

    COALESCE(
        i.horas_trabajo_total,
        0
    ) AS horas_trabajo_total,


    /* Tiempo de parada */

    COALESCE(
        i.tiempo_parada_total,
        0
    ) AS tiempo_parada_total,


    /* Costo histórico de repuestos */

    COALESCE(
        r.costo_repuestos_total,
        0
    ) AS costo_repuestos_total


FROM activo_tecnologico a


INNER JOIN tipo_activo ta

    ON ta.id_tipo_activo =
       a.id_tipo_activo


/*
|--------------------------------------------------------------------------
| MANTENIMIENTOS
|--------------------------------------------------------------------------
*/

LEFT JOIN (

    SELECT

        om.id_activo,

        COUNT(
            om.id_orden
        ) AS num_mantenimientos,


        SUM(
            CASE

                WHEN LOWER(
                    om.tipo_mantenimiento
                ) = 'correctivo'

                THEN 1

                ELSE 0

            END
        ) AS num_correctivos,


        SUM(
            CASE

                WHEN LOWER(
                    om.tipo_mantenimiento
                ) = 'preventivo'

                THEN 1

                ELSE 0

            END
        ) AS num_preventivos,


        SUM(
            CASE

                WHEN LOWER(
                    om.tipo_mantenimiento
                ) = 'predictivo'

                THEN 1

                ELSE 0

            END
        ) AS num_predictivos


    FROM orden_mantenimiento om

    GROUP BY om.id_activo

) m

    ON m.id_activo =
       a.id_activo


/*
|--------------------------------------------------------------------------
| INTERVENCIONES
|--------------------------------------------------------------------------
*/

LEFT JOIN (

    SELECT

        om.id_activo,


        SUM(
            COALESCE(
                inv.horas_trabajo,
                0
            )
        ) AS horas_trabajo_total,


        SUM(
            COALESCE(
                inv.tiempo_parada_horas,
                0
            )
        ) AS tiempo_parada_total


    FROM orden_mantenimiento om


    INNER JOIN intervencion inv

        ON inv.id_orden =
           om.id_orden


    GROUP BY om.id_activo

) i

    ON i.id_activo =
       a.id_activo


/*
|--------------------------------------------------------------------------
| COSTOS DE REPUESTOS
|--------------------------------------------------------------------------
*/

LEFT JOIN (

    SELECT

        om.id_activo,


        SUM(
            COALESCE(
                ir.costo_total,
                ir.cantidad *
                ir.costo_unitario,
                0
            )
        ) AS costo_repuestos_total


    FROM orden_mantenimiento om


    INNER JOIN intervencion inv

        ON inv.id_orden =
           om.id_orden


    INNER JOIN intervencion_repuesto ir

        ON ir.id_intervencion =
           inv.id_intervencion


    GROUP BY om.id_activo

) r

    ON r.id_activo =
       a.id_activo


ORDER BY
    a.id_activo

";


/*
|--------------------------------------------------------------------------
| EJECUTAR CONSULTA
|--------------------------------------------------------------------------
*/

$consulta = $conn->query($sql);


if (!$consulta) {

    $error =
        "Error al consultar los activos: "
        . $conn->error;

} else {

    while (
        $fila =
        $consulta->fetch_assoc()
    ) {

        $activos[] = $fila;

    }

}


/*
|--------------------------------------------------------------------------
| PREPARAR DATOS PARA K-MEANS
|--------------------------------------------------------------------------
*/

if (
    !$error &&
    !empty($activos)
) {

    $payload = [
        "activos" => []
    ];


    foreach (
        $activos as $activo
    ) {

        $payload["activos"][] = [

            "id_activo" =>
                (int)$activo["id_activo"],

            "edad_activo_anios" =>
                (float)$activo[
                    "edad_activo_anios"
                ],

            "horas_uso_acumuladas" =>
                (float)$activo[
                    "horas_uso_acumuladas"
                ],

            "num_mantenimientos" =>
                (int)$activo[
                    "num_mantenimientos"
                ],

            "num_correctivos" =>
                (int)$activo[
                    "num_correctivos"
                ],

            "num_preventivos" =>
                (int)$activo[
                    "num_preventivos"
                ],

            "num_predictivos" =>
                (int)$activo[
                    "num_predictivos"
                ],

            "horas_trabajo_total" =>
                (float)$activo[
                    "horas_trabajo_total"
                ],

            "tiempo_parada_total" =>
                (float)$activo[
                    "tiempo_parada_total"
                ],

            "costo_repuestos_total" =>
                (float)$activo[
                    "costo_repuestos_total"
                ]

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | LLAMAR API DE SEGMENTACIÓN
    |--------------------------------------------------------------------------
    */

    $ch = curl_init(
        $apiSegmentacion
    );


    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_POST,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($payload)
    );


    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            "Content-Type: application/json"
        ]
    );


    curl_setopt(
        $ch,
        CURLOPT_TIMEOUT,
        60
    );


    $respuesta = curl_exec($ch);


    $httpCode =
        curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    $curlError =
        curl_error($ch);


    curl_close($ch);


    /*
    |--------------------------------------------------------------------------
    | VALIDAR RESPUESTA
    |--------------------------------------------------------------------------
    */

    if ($respuesta === false) {

        $error =
            "No fue posible conectar con "
            . "la API de segmentación. "
            . $curlError;

    } elseif ($httpCode !== 200) {

        $error =
            "La API de segmentación respondió "
            . "con código HTTP "
            . $httpCode
            . ".";

    } else {

        $respuestaAPI =
            json_decode(
                $respuesta,
                true
            );


        if (
            !$respuestaAPI ||
            !isset(
                $respuestaAPI["resultados"]
            )
        ) {

            $error =
                "La respuesta de la API "
                . "no tiene el formato esperado.";

        } else {

            $resultados =
                $respuestaAPI[
                    "resultados"
                ];


            $distribucion =
                $respuestaAPI[
                    "distribucion"
                ] ?? [];

        }

    }

}


/*
|--------------------------------------------------------------------------
| MAPEAR SEGMENTOS A LOS ACTIVOS
|--------------------------------------------------------------------------
*/

$segmentos = [];

foreach (
    $resultados as $resultado
) {

    $segmentos[
        (int)$resultado["id_activo"]
    ] =
        (int)$resultado["segmento"];

}


foreach (
    $activos as &$activo
) {

    $id =
        (int)$activo["id_activo"];


    $activo["segmento"] =
        $segmentos[$id] ?? null;

}

unset($activo);

/*
|--------------------------------------------------------------------------
| PERFIL DE LOS SEGMENTOS
|--------------------------------------------------------------------------
*/

$perfilesSegmentos = [];

foreach ($activos as $activo) {

    if ($activo["segmento"] === null) {
        continue;
    }

    $segmento = (int)$activo["segmento"];

    if (!isset($perfilesSegmentos[$segmento])) {

        $perfilesSegmentos[$segmento] = [
            "cantidad" => 0,
            "edad" => 0,
            "horas_uso" => 0,
            "mantenimientos" => 0,
            "correctivos" => 0,
            "preventivos" => 0,
            "predictivos" => 0,
            "horas_trabajo" => 0,
            "tiempo_parada" => 0,
            "costo_repuestos" => 0
        ];

    }

    $perfilesSegmentos[$segmento]["cantidad"]++;

    $perfilesSegmentos[$segmento]["edad"] +=
        (float)$activo["edad_activo_anios"];

    $perfilesSegmentos[$segmento]["horas_uso"] +=
        (float)$activo["horas_uso_acumuladas"];

    $perfilesSegmentos[$segmento]["mantenimientos"] +=
        (int)$activo["num_mantenimientos"];

    $perfilesSegmentos[$segmento]["correctivos"] +=
        (int)$activo["num_correctivos"];

    $perfilesSegmentos[$segmento]["preventivos"] +=
        (int)$activo["num_preventivos"];

    $perfilesSegmentos[$segmento]["predictivos"] +=
        (int)$activo["num_predictivos"];

    $perfilesSegmentos[$segmento]["horas_trabajo"] +=
        (float)$activo["horas_trabajo_total"];

    $perfilesSegmentos[$segmento]["tiempo_parada"] +=
        (float)$activo["tiempo_parada_total"];

    $perfilesSegmentos[$segmento]["costo_repuestos"] +=
        (float)$activo["costo_repuestos_total"];

}


/*
|--------------------------------------------------------------------------
| CALCULAR PROMEDIOS
|--------------------------------------------------------------------------
*/

foreach (
    $perfilesSegmentos as $segmento => &$perfil
) {

    $cantidad = $perfil["cantidad"];

    if ($cantidad > 0) {

        $perfil["edad"] /=
            $cantidad;

        $perfil["horas_uso"] /=
            $cantidad;

        $perfil["mantenimientos"] /=
            $cantidad;

        $perfil["correctivos"] /=
            $cantidad;

        $perfil["preventivos"] /=
            $cantidad;

        $perfil["predictivos"] /=
            $cantidad;

        $perfil["horas_trabajo"] /=
            $cantidad;

        $perfil["tiempo_parada"] /=
            $cantidad;

        $perfil["costo_repuestos"] /=
            $cantidad;

    }

}

/*
|--------------------------------------------------------------------------
| INTERPRETACIÓN DINÁMICA DE LOS SEGMENTOS
|--------------------------------------------------------------------------
*/

$interpretacionesSegmentos = [];

// Promedios generales de todos los segmentos
$totalSegmentos = count($perfilesSegmentos);

$promedioGeneral = [
    "edad" => 0,
    "horas_uso" => 0,
    "mantenimientos" => 0,
    "correctivos" => 0,
    "preventivos" => 0,
    "horas_trabajo" => 0,
    "tiempo_parada" => 0,
    "costo_repuestos" => 0
];

foreach ($perfilesSegmentos as $perfil) {

    $promedioGeneral["edad"] += $perfil["edad"];
    $promedioGeneral["horas_uso"] += $perfil["horas_uso"];
    $promedioGeneral["mantenimientos"] += $perfil["mantenimientos"];
    $promedioGeneral["correctivos"] += $perfil["correctivos"];
    $promedioGeneral["preventivos"] += $perfil["preventivos"];
    $promedioGeneral["horas_trabajo"] += $perfil["horas_trabajo"];
    $promedioGeneral["tiempo_parada"] += $perfil["tiempo_parada"];
    $promedioGeneral["costo_repuestos"] += $perfil["costo_repuestos"];
}

if ($totalSegmentos > 0) {

    foreach ($promedioGeneral as $campo => $valor) {
        $promedioGeneral[$campo] =
            $valor / $totalSegmentos;
    }

}


/*
|--------------------------------------------------------------------------
| CREAR INTERPRETACIÓN DE CADA SEGMENTO
|--------------------------------------------------------------------------
*/

foreach ($perfilesSegmentos as $segmento => $perfil) {

    $caracteristicas = [];
    $recomendaciones = [];

    /*
    |---------------------------------------------------------------
    | EDAD Y USO
    |---------------------------------------------------------------
    */

    if (
        $perfil["edad"] >
        $promedioGeneral["edad"]
    ) {

        $caracteristicas[] =
            "mayor antigüedad que el promedio";

        $recomendaciones[] =
            "realizar seguimiento al desgaste del activo";

    } else {

        $caracteristicas[] =
            "antigüedad inferior al promedio";

    }


    if (
        $perfil["horas_uso"] >
        $promedioGeneral["horas_uso"]
    ) {

        $caracteristicas[] =
            "alto nivel de utilización";

        $recomendaciones[] =
            "priorizar el seguimiento de las horas de uso";

    } else {

        $caracteristicas[] =
            "nivel de utilización inferior al promedio";

    }


    /*
    |---------------------------------------------------------------
    | MANTENIMIENTOS CORRECTIVOS
    |---------------------------------------------------------------
    */

    if (
        $perfil["correctivos"] >
        $promedioGeneral["correctivos"]
    ) {

        $caracteristicas[] =
            "mayor presencia de mantenimientos correctivos";

        $recomendaciones[] =
            "revisar las causas de las intervenciones correctivas";

    }


    /*
    |---------------------------------------------------------------
    | MANTENIMIENTOS PREVENTIVOS
    |---------------------------------------------------------------
    */

    if (
        $perfil["preventivos"] >
        $promedioGeneral["preventivos"]
    ) {

        $caracteristicas[] =
            "mayor actividad preventiva";

        $recomendaciones[] =
            "mantener la planificación preventiva";

    }


    /*
    |---------------------------------------------------------------
    | TIEMPO DE PARADA
    |---------------------------------------------------------------
    */

    if (
        $perfil["tiempo_parada"] >
        $promedioGeneral["tiempo_parada"]
    ) {

        $caracteristicas[] =
            "tiempo de parada superior al promedio";

        $recomendaciones[] =
            "priorizar la reducción de tiempos de parada";

    }


    /*
    |---------------------------------------------------------------
    | COSTO DE REPUESTOS
    |---------------------------------------------------------------
    */

    if (
        $perfil["costo_repuestos"] >
        $promedioGeneral["costo_repuestos"]
    ) {

        $caracteristicas[] =
            "alto costo histórico de repuestos";

        $recomendaciones[] =
            "evaluar el costo de mantener y reparar estos activos";

    }


    /*
    |---------------------------------------------------------------
    | SI NO HAY CARACTERÍSTICAS DESTACADAS
    |---------------------------------------------------------------
    */

    if (empty($caracteristicas)) {

        $caracteristicas[] =
            "comportamiento cercano al promedio general";

    }

    if (empty($recomendaciones)) {

        $recomendaciones[] =
            "mantener seguimiento periódico";

    }


    /*
    |---------------------------------------------------------------
    | GUARDAR INTERPRETACIÓN
    |---------------------------------------------------------------
    */

    $interpretacionesSegmentos[$segmento] = [

        "caracteristicas" =>
            $caracteristicas,

        "recomendaciones" =>
            $recomendaciones

    ];

}

unset($perfil);
?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Segmentación de Activos
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body>

<div class="container-fluid py-4">


    <!-- ========================================================= -->
    <!-- ENCABEZADO -->
    <!-- ========================================================= -->

    <div class="mb-4">

        <h2 class="mb-1">
            Segmentación de Activos
        </h2>

        <p class="text-muted mb-0">

            Clasificación de activos mediante
            algoritmo K-Means.

        </p>

    </div>

    <!-- ========================================================= -->
<!-- PERFILES DE LOS SEGMENTOS -->
<!-- ========================================================= -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5 class="mb-1">
            Perfil de los segmentos
        </h5>

        <p class="text-muted">
            Valores promedio de los activos pertenecientes
            a cada segmento.
        </p>


        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead>

                    <tr>

                        <th>Segmento</th>
                        <th>Activos</th>
                        <th>Edad promedio</th>
                        <th>Horas uso</th>
                        <th>Mantenimientos</th>
                        <th>Correctivos</th>
                        <th>Preventivos</th>
                        <th>Predictivos</th>
                        <th>Horas trabajo</th>
                        <th>Parada</th>
                        <th>Costo repuestos</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach (
                    $perfilesSegmentos
                    as $segmento => $perfil
                ): ?>

                    <tr>

                        <td>

                            <span class="badge bg-primary">

                                Segmento
                                <?= $segmento ?>

                            </span>

                        </td>

                        <td>

                            <?= $perfil["cantidad"] ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["edad"],
                                1,
                                ",",
                                "."
                            ) ?>

                            años

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["horas_uso"],
                                0,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["mantenimientos"],
                                1,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["correctivos"],
                                1,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["preventivos"],
                                1,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["predictivos"],
                                1,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["horas_trabajo"],
                                1,
                                ",",
                                "."
                            ) ?>

                        </td>

                        <td>

                            <?= number_format(
                                $perfil["tiempo_parada"],
                                1,
                                ",",
                                "."
                            ) ?>

                            h

                        </td>

                        <td>

                            $

                            <?= number_format(
                                $perfil["costo_repuestos"],
                                0,
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
<!-- INTERPRETACIÓN DINÁMICA -->
<!-- ========================================================= -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5 class="mb-1">
            Interpretación de los segmentos
        </h5>

        <p class="text-muted">
            Las características y recomendaciones se generan
            comparando los perfiles actuales de los segmentos.
        </p>

        <div class="row g-4">

            <?php foreach (
                $interpretacionesSegmentos
                as $segmento => $interpretacion
            ): ?>

                <div class="col-md-6">

                    <div class="card h-100 border">

                        <div class="card-body">

                            <h5 class="mb-3">

                                Segmento <?= $segmento ?>

                            </h5>


                            <h6>
                                Características
                            </h6>

                            <ul>

                                <?php foreach (
                                    $interpretacion["caracteristicas"]
                                    as $caracteristica
                                ): ?>

                                    <li>
                                        <?= htmlspecialchars(
                                            $caracteristica
                                        ) ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>


                            <h6 class="mt-3">
                                Recomendación
                            </h6>

                            <ul>

                                <?php foreach (
                                    $interpretacion["recomendaciones"]
                                    as $recomendacion
                                ): ?>

                                    <li>
                                        <?= htmlspecialchars(
                                            $recomendacion
                                        ) ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>


    <!-- ========================================================= -->
    <!-- ERROR -->
    <!-- ========================================================= -->

    <?php if ($error): ?>

        <div class="alert alert-danger">

            <strong>
                Error:
            </strong>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <?php if (!$error): ?>


        <!-- ===================================================== -->
        <!-- RESUMEN -->
        <!-- ===================================================== -->

        <div class="row g-3 mb-4">


            <div class="col-md-4">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Activos analizados
                        </h6>

                        <h3 class="mb-0">

                            <?= count($activos) ?>

                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Segmentos
                        </h6>

                        <h3 class="mb-0">

                            4

                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Modelo
                        </h6>

                        <h3 class="mb-0">

                            K-Means

                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- DISTRIBUCIÓN -->
        <!-- ===================================================== -->

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-3">
                    Distribución de activos
                </h5>


                <div class="row g-3">

                    <?php foreach (
                        $distribucion
                        as $segmento =>
                        $cantidad
                    ): ?>

                        <div class="col-md-3">

                            <div
                                class="border rounded p-3 text-center"
                            >

                                <div class="text-muted">
                                    Segmento
                                </div>

                                <h3>
                                    <?= htmlspecialchars(
                                        $segmento
                                    ) ?>
                                </h3>

                                <strong>
                                    <?= $cantidad ?>
                                    activos
                                </strong>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- TABLA -->
        <!-- ===================================================== -->

        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-3">
                    Segmentación de activos
                </h5>


                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle"
                    >

                        <thead>

                            <tr>

                                <th>
                                    Código
                                </th>

                                <th>
                                    Activo
                                </th>

                                <th>
                                    Tipo
                                </th>

                                <th>
                                    Criticidad
                                </th>

                                <th>
                                    Edad
                                </th>

                                <th>
                                    Horas uso
                                </th>

                                <th>
                                    Mantenimientos
                                </th>

                                <th>
                                    Correctivos
                                </th>

                                <th>
                                    Preventivos
                                </th>

                                <th>
                                    Segmento
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach (
                            $activos
                            as $activo
                        ): ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars(
                                        $activo[
                                            "codigo_activo"
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $activo[
                                            "nombre"
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $activo[
                                            "tipo_activo"
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $activo[
                                            "criticidad"
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <?= number_format(
                                        (float)$activo[
                                            "edad_activo_anios"
                                        ],
                                        0,
                                        ",",
                                        "."
                                    ) ?>

                                    años

                                </td>


                                <td>

                                    <?= number_format(
                                        (float)$activo[
                                            "horas_uso_acumuladas"
                                        ],
                                        0,
                                        ",",
                                        "."
                                    ) ?>

                                </td>


                                <td>

                                    <?= $activo[
                                        "num_mantenimientos"
                                    ] ?>

                                </td>


                                <td>

                                    <?= $activo[
                                        "num_correctivos"
                                    ] ?>

                                </td>


                                <td>

                                    <?= $activo[
                                        "num_preventivos"
                                    ] ?>

                                </td>


                                <td>

                                    <?php if (
                                        $activo[
                                            "segmento"
                                        ] !== null
                                    ): ?>

                                        <span
                                            class="badge bg-primary fs-6"
                                        >

                                            Segmento
                                            <?= $activo[
                                                "segmento"
                                            ] ?>

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-secondary"
                                        >

                                            Sin clasificar

                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    <?php endif; ?>


</div>

</body>

</html>