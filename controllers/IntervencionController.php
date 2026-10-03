<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();


if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Intervencion.php";

$intervencionModel = new Intervencion($conn);

$accion = $_GET["accion"] ?? $_POST["accion"] ?? "listar";


/* =========================
   LISTAR
========================= */

if ($accion === "listar") {

    $busqueda = $_GET["busqueda"] ?? "";
    $tipo = $_GET["tipo"] ?? "";

    $intervenciones = $intervencionModel->listar(
        $busqueda,
        $tipo
    );

    require_once "../intervenciones/index.php";
    exit;
}


/* =========================
   CREAR
========================= */

if ($accion === "crear") {

    $ordenes = $intervencionModel->ordenes();

    require_once "../intervenciones/crear.php";
    exit;
}


/* =========================
   GUARDAR
========================= */

if ($accion === "guardar") {

    $datos = [
    "id_orden" => $_POST["id_orden"] ?? null,
    "fecha_intervencion" => $_POST["fecha_intervencion"] ?? null,
    "horas_trabajo" => $_POST["horas_trabajo"] ?? 0,
    "tiempo_parada_horas" => $_POST["tiempo_parada_horas"] ?? 0,
    "resultado" => trim($_POST["resultado"] ?? ""),
    "causa_falla" => trim($_POST["causa_falla"] ?? "")
];

    $resultado = $intervencionModel->crear($datos);

    if ($resultado) {

        header(
            "Location: IntervencionController.php?accion=listar&mensaje=creada"
        );

        exit;
    }

    header(
        "Location: ../intervenciones/crear.php?error=guardar"
    );

    exit;
}


/* =========================
   VER
========================= */

if ($accion === "ver") {

    $id = intval($_GET["id"] ?? 0);

    $intervencion = $intervencionModel->obtenerPorId($id);

    if (!$intervencion) {

        header(
            "Location: IntervencionController.php?accion=listar&error=no_encontrada"
        );

        exit;
    }

    require_once "../intervenciones/ver.php";
    exit;
}


/* =========================
   EDITAR
========================= */

if ($accion === "editar") {

    $id = intval($_GET["id"] ?? 0);

    $intervencion = $intervencionModel->obtenerPorId($id);

    if (!$intervencion) {

        header(
            "Location: IntervencionController.php?accion=listar&error=no_encontrada"
        );

        exit;
    }

    $ordenes = $intervencionModel->ordenes();

    require_once "../intervenciones/editar.php";
    exit;
}


/* =========================
   ACTUALIZAR
========================= */

if ($accion === "actualizar") {

    $id = intval($_POST["id_intervencion"] ?? 0);

    $datos = [
    "id_orden" => $_POST["id_orden"] ?? null,
    "fecha_intervencion" => $_POST["fecha_intervencion"] ?? null,
    "horas_trabajo" => $_POST["horas_trabajo"] ?? 0,
    "tiempo_parada_horas" => $_POST["tiempo_parada_horas"] ?? 0,
    "resultado" => trim($_POST["resultado"] ?? ""),
    "causa_falla" => trim($_POST["causa_falla"] ?? "")
];

    $resultado = $intervencionModel->actualizar(
        $id,
        $datos
    );

    if ($resultado) {

        header(
            "Location: IntervencionController.php?accion=listar&mensaje=actualizada"
        );

        exit;
    }

    header(
        "Location: IntervencionController.php?accion=editar&id="
        . $id
        . "&error=actualizar"
    );

    exit;
}


/* =========================
   ACCIÓN NO VÁLIDA
========================= */

header(
    "Location: IntervencionController.php?accion=listar"
);

exit;