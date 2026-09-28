<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Activo.php";

$activoModel = new Activo($conn);

$accion = $_GET["accion"] ?? $_POST["accion"] ?? "listar";

/*
|--------------------------------------------------------------------------
| LISTAR ACTIVOS
|--------------------------------------------------------------------------
*/

if ($accion === "listar") {

    $busqueda = $_GET["busqueda"] ?? "";
$estado = $_GET["estado"] ?? "";
$criticidad = $_GET["criticidad"] ?? "";
$tipo = $_GET["tipo"] ?? "";

$activos = $activoModel->listar(
    $busqueda,
    $estado,
    $criticidad,
    $tipo
);

$tipos = $activoModel->tipos();

require_once "../activos/index.php";
}


/*
|--------------------------------------------------------------------------
| MOSTRAR FORMULARIO DE CREACIÓN
|--------------------------------------------------------------------------
*/

if ($accion === "crear") {

    $usuario = $_SESSION["usuario"];

    $tipos = $activoModel->tipos();

    $ubicaciones = $activoModel->ubicaciones(
        $usuario["id_empresa"]
    );

    require_once "../activos/crear.php";

    exit;
}


/*
|--------------------------------------------------------------------------
| GUARDAR ACTIVO
|--------------------------------------------------------------------------
*/

if ($accion === "guardar") {

    $usuario = $_SESSION["usuario"];

    $datos = [

        "id_empresa" => $usuario["id_empresa"],

        "id_tipo_activo" =>
            $_POST["id_tipo_activo"] ?? null,

        "id_ubicacion" =>
            $_POST["id_ubicacion"] ?? null,

        "codigo_activo" =>
            trim($_POST["codigo_activo"] ?? ""),

        "nombre" =>
            trim($_POST["nombre"] ?? ""),

        "marca" =>
            trim($_POST["marca"] ?? ""),

        "modelo" =>
            trim($_POST["modelo"] ?? ""),

        "numero_serie" =>
            trim($_POST["numero_serie"] ?? ""),

        "fecha_adquisicion" =>
            $_POST["fecha_adquisicion"] ?? null,

        "criticidad" =>
            $_POST["criticidad"] ?? "Media",

        "estado" =>
            $_POST["estado"] ?? "Activo",

        "horas_uso_acumuladas" =>
            $_POST["horas_uso_acumuladas"] ?? 0
    ];

    $resultado = $activoModel->crear($datos);

    if ($resultado) {

        header("Location: ActivoController.php?accion=listar&mensaje=creado");
        exit;

    } else {

        header("Location: ../activos/crear.php?error=guardar");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| VER ACTIVO
|--------------------------------------------------------------------------
*/

if ($accion === "ver") {

    $id = intval($_GET["id"] ?? 0);

    $activo = $activoModel->obtenerPorId($id);

    if (!$activo) {

        header("Location: ../activos/index.php?error=no_encontrado");
        exit;
    }

    require_once "../activos/ver.php";

    exit;
}


/*
|--------------------------------------------------------------------------
| MOSTRAR FORMULARIO DE EDICIÓN
|--------------------------------------------------------------------------
*/

if ($accion === "editar") {

    $id = intval($_GET["id"] ?? 0);

    $activo = $activoModel->obtenerPorId($id);

    if (!$activo) {

        header("Location: ../activos/index.php?error=no_encontrado");
        exit;
    }

    $usuario = $_SESSION["usuario"];

    $tipos = $activoModel->tipos();

    $ubicaciones = $activoModel->ubicaciones(
        $usuario["id_empresa"]
    );

    require_once "../activos/editar.php";

    exit;
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR ACTIVO
|--------------------------------------------------------------------------
*/

if ($accion === "actualizar") {

    $id = intval($_POST["id_activo"] ?? 0);

    $datos = [

        "id_tipo_activo" =>
            $_POST["id_tipo_activo"] ?? null,

        "id_ubicacion" =>
            $_POST["id_ubicacion"] ?? null,

        "codigo_activo" =>
            trim($_POST["codigo_activo"] ?? ""),

        "nombre" =>
            trim($_POST["nombre"] ?? ""),

        "marca" =>
            trim($_POST["marca"] ?? ""),

        "modelo" =>
            trim($_POST["modelo"] ?? ""),

        "numero_serie" =>
            trim($_POST["numero_serie"] ?? ""),

        "fecha_adquisicion" =>
            $_POST["fecha_adquisicion"] ?? null,

        "criticidad" =>
            $_POST["criticidad"] ?? "Media",

        "estado" =>
            $_POST["estado"] ?? "Activo",

        "horas_uso_acumuladas" =>
            $_POST["horas_uso_acumuladas"] ?? 0
    ];

    $resultado = $activoModel->actualizar(
        $id,
        $datos
    );

    if ($resultado) {

        header(
            "Location: ActivoController.php?accion=listar&mensaje=actualizado"
        );

        exit;

    } else {

        header(
            "Location: ../activos/editar.php?id="
            . $id
            . "&error=actualizar"
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| ELIMINAR ACTIVO
|--------------------------------------------------------------------------
*/

if ($accion === "eliminar") {

    $id = intval($_POST["id_activo"] ?? 0);

    $resultado = $activoModel->eliminar($id);

    if ($resultado) {

        header(
            "Location: ActivoController.php?accion=listar&mensaje=eliminado"
        );

        exit;

    } else {

        header(
            "Location: ActivoController.php?accion=listar&error=eliminar"
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| ACCIÓN NO VÁLIDA
|--------------------------------------------------------------------------
*/

header("Location: ../activos/index.php");
exit;