<?php

require_once "../config/database.php";

$correo = "admin@gestion.com";
$password = "Admin123";
$rol = "admin";

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "SELECT id_usuario FROM usuario WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("El usuario no existe.");
}

$usuario = $resultado->fetch_assoc();
$id_usuario = $usuario['id_usuario'];

$sql = "INSERT INTO usuario_acceso 
        (id_usuario, password_hash, rol, estado)
        VALUES (?, ?, ?, 'Activo')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $id_usuario, $password_hash, $rol);

if ($stmt->execute()) {
    echo "Administrador creado correctamente.";
} else {
    echo "Error: " . $stmt->error;
}