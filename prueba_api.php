<?php

$url = "https://geology-caption-aversion.ngrok-free.dev/api/prediccion";

$datos = [

    "activos" => [

        [
            "id_activo" => 1,
            "edad_activo_anios" => 8,
            "horas_uso_acumuladas" => 5200,
            "horas_trabajo" => 18,
            "tiempo_parada_horas" => 6,
            "costo_repuestos" => 3500000,
            "tipo_activo" => "Maquinaria",
            "criticidad" => "Alta"
        ],

        [
            "id_activo" => 2,
            "edad_activo_anios" => 3,
            "horas_uso_acumuladas" => 1500,
            "horas_trabajo" => 8,
            "tiempo_parada_horas" => 1,
            "costo_repuestos" => 500000,
            "tipo_activo" => "Equipo",
            "criticidad" => "Media"
        ],

        [
            "id_activo" => 3,
            "edad_activo_anios" => 12,
            "horas_uso_acumuladas" => 9000,
            "horas_trabajo" => 30,
            "tiempo_parada_horas" => 15,
            "costo_repuestos" => 7000000,
            "tipo_activo" => "Maquinaria",
            "criticidad" => "Alta"
        ]

    ]

];

$json = json_encode($datos);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($json)
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

$respuesta = curl_exec($ch);

if ($respuesta === false) {

    die("Error cURL: " . curl_error($ch));

}

$codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "<h3>Código HTTP: $codigo</h3>";

echo "<h3>Datos enviados:</h3>";

echo "<pre>";
echo htmlspecialchars(
    json_encode(
        $datos,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    )
);
echo "</pre>";

echo "<h3>Respuesta de la API:</h3>";

echo "<pre>";
echo htmlspecialchars(
    json_encode(
        json_decode($respuesta, true),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    )
);
echo "</pre>";