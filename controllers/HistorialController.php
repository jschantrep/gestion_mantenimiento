<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Historial.php";

$historialModel = new Historial($conn);

$accion = $_GET["accion"] ?? "listar";


if ($accion === "listar") {

    $busqueda = $_GET["busqueda"] ?? "";

    $historial = $historialModel->listar($busqueda);

    require_once "../historial/index.php";

    exit;
}


if ($accion === "ver") {

    $id = intval($_GET["id"] ?? 0);

    $registro = $historialModel->obtenerPorId($id);

    if (!$registro) {

        header(
            "Location: HistorialController.php?accion=listar&error=no_encontrado"
        );

        exit;
    }

    require_once "../historial/ver.php";

    exit;
}


header("Location: HistorialController.php?accion=listar");

exit;