<?php

$host = "localhost";
$usuario = "root";
$password = "admin";
$base_datos = "gestion_mantenimiento";

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");