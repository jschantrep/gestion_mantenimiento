<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Orden.php";

$ordenModel = new Orden($conn);

$accion = $_GET["accion"] ?? $_POST["accion"] ?? "listar";

/* =========================
   LISTAR ÓRDENES
========================= */

if ($accion === "listar") {

    $busqueda = $_GET["busqueda"] ?? "";
    $estado = $_GET["estado"] ?? "";
    $prioridad = $_GET["prioridad"] ?? "";
    $tipo = $_GET["tipo"] ?? "";

    $ordenes = $ordenModel->listar(
        $busqueda,
        $estado,
        $prioridad,
        $tipo
    );

    require_once "../ordenes/index.php";
    exit;
}


/* =========================
   FORMULARIO CREAR
========================= */

if ($accion === "crear") {

    $usuario = $_SESSION["usuario"];

    $activos = $ordenModel->activos(
        $usuario["id_empresa"]
    );

    $tecnicos = $ordenModel->tecnicos();

    require_once "../ordenes/crear.php";
    exit;
}


/* =========================
   GUARDAR
========================= */

if ($accion === "guardar") {

    $datos = [
        "id_activo" => $_POST["id_activo"] ?? null,
        "id_tecnico" => $_POST["id_tecnico"] ?? null,
        "tipo_mantenimiento" => $_POST["tipo_mantenimiento"] ?? "Preventivo",
        "fecha_programada" => $_POST["fecha_programada"] ?? null,
        "fecha_inicio" => $_POST["fecha_inicio"] ?? null,
        "fecha_fin" => $_POST["fecha_fin"] ?? null,
        "prioridad" => $_POST["prioridad"] ?? "Media",
        "estado_orden" => $_POST["estado_orden"] ?? "Pendiente",
        "descripcion" => trim($_POST["descripcion"] ?? "")
    ];

    $resultado = $ordenModel->crear($datos);

    if ($resultado) {
        header(
            "Location: OrdenController.php?accion=listar&mensaje=creada"
        );
        exit;
    }

    header(
        "Location: ../ordenes/crear.php?error=guardar"
    );
    exit;
}


/* =========================
   VER
========================= */

if ($accion === "ver") {

    $id = intval($_GET["id"] ?? 0);

    $orden = $ordenModel->obtenerPorId($id);

    if (!$orden) {
        header(
            "Location: OrdenController.php?accion=listar&error=no_encontrada"
        );
        exit;
    }

    require_once "../ordenes/ver.php";
    exit;
}


/* =========================
   FORMULARIO EDITAR
========================= */

if ($accion === "editar") {

    $id = intval($_GET["id"] ?? 0);

    $orden = $ordenModel->obtenerPorId($id);

    if (!$orden) {
        header(
            "Location: OrdenController.php?accion=listar&error=no_encontrada"
        );
        exit;
    }

    $usuario = $_SESSION["usuario"];

    $activos = $ordenModel->activos(
        $usuario["id_empresa"]
    );

    $tecnicos = $ordenModel->tecnicos();
    
    require_once "../ordenes/editar.php";
    exit;
}


/* =========================
   ACTUALIZAR
========================= */

if ($accion === "actualizar") {

    $id = intval($_POST["id_orden"] ?? 0);

    $datos = [
        "id_activo" => $_POST["id_activo"] ?? null,
        "id_tecnico" => $_POST["id_tecnico"] ?? null,
        "tipo_mantenimiento" => $_POST["tipo_mantenimiento"] ?? "Preventivo",
        "fecha_programada" => $_POST["fecha_programada"] ?? null,
        "fecha_inicio" => $_POST["fecha_inicio"] ?? null,
        "fecha_fin" => $_POST["fecha_fin"] ?? null,
        "prioridad" => $_POST["prioridad"] ?? "Media",
        "estado_orden" => $_POST["estado_orden"] ?? "Pendiente",
        "descripcion" => trim($_POST["descripcion"] ?? "")
    ];

    $resultado = $ordenModel->actualizar(
        $id,
        $datos
    );

    if ($resultado) {
        header(
            "Location: OrdenController.php?accion=listar&mensaje=actualizada"
        );
        exit;
    }

    header(
        "Location: OrdenController.php?accion=editar&id="
        . $id
        . "&error=actualizar"
    );
    exit;
}


/* =========================
   CANCELAR
========================= */

if ($accion === "cancelar") {

    $id = intval($_POST["id_orden"] ?? 0);

    $resultado = $ordenModel->cancelar($id);

    if ($resultado) {
        header(
            "Location: OrdenController.php?accion=listar&mensaje=cancelada"
        );
        exit;
    }

    header(
        "Location: OrdenController.php?accion=listar&error=cancelar"
    );
    exit;
}


/* =========================
   ACCIÓN NO VÁLIDA
========================= */

header(
    "Location: OrdenController.php?accion=listar"
);
exit;