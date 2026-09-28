<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";

if ($correo === "" || $password === "") {
    header("Location: login.php?error=campos");
    exit;
}

$sql = "SELECT 
            u.id_usuario,
            u.id_empresa,
            u.nombre,
            u.cargo,
            u.correo,
            u.estado,
            ua.password_hash,
            ua.rol,
            ua.estado AS estado_acceso
        FROM usuario u
        INNER JOIN usuario_acceso ua
            ON u.id_usuario = ua.id_usuario
        WHERE u.correo = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param("s", $correo);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: login.php?error=credenciales");
    exit;
}

$usuario = $resultado->fetch_assoc();

if ($usuario["estado"] !== "Activo" ||
    $usuario["estado_acceso"] !== "Activo") {

    header("Location: login.php?error=inactivo");
    exit;
}

if (!password_verify($password, $usuario["password_hash"])) {
    header("Location: login.php?error=credenciales");
    exit;
}

session_regenerate_id(true);

$_SESSION["usuario"] = [
    "id_usuario" => $usuario["id_usuario"],
    "id_empresa" => $usuario["id_empresa"],
    "nombre" => $usuario["nombre"],
    "cargo" => $usuario["cargo"],
    "correo" => $usuario["correo"],
    "rol" => $usuario["rol"]
];

$stmt->close();
$conn->close();

header("Location: ../dashboard/index.php");
exit;