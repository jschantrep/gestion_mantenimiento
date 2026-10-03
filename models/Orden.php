<?php

class Orden
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /* LISTAR ÓRDENES */
    public function listar($busqueda = "", $estado = "", $prioridad = "", $tipo = "")
    {
        $sql = "SELECT
                    o.*,
                    a.codigo_activo,
                    a.nombre AS activo,
                    a.id_empresa
                FROM orden_mantenimiento o
                INNER JOIN activo_tecnologico a
                    ON o.id_activo = a.id_activo
                WHERE 1=1";

        $parametros = [];
        $tipos = "";

        if ($busqueda !== "") {
            $sql .= " AND (
                        a.codigo_activo LIKE ?
                        OR a.nombre LIKE ?
                        OR o.descripcion LIKE ?
                      )";

            $buscar = "%" . $busqueda . "%";

            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;

            $tipos .= "sss";
        }

        if ($estado !== "") {
            $sql .= " AND o.estado_orden = ?";
            $parametros[] = $estado;
            $tipos .= "s";
        }

        if ($prioridad !== "") {
            $sql .= " AND o.prioridad = ?";
            $parametros[] = $prioridad;
            $tipos .= "s";
        }

        if ($tipo !== "") {
            $sql .= " AND o.tipo_mantenimiento = ?";
            $parametros[] = $tipo;
            $tipos .= "s";
        }

        $sql .= " ORDER BY o.id_orden DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        if (!empty($parametros)) {
            $stmt->bind_param($tipos, ...$parametros);
        }

        $stmt->execute();

        return $stmt->get_result();
    }

    /* OBTENER ORDEN */
    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    o.*,
                    a.codigo_activo,
                    a.nombre AS activo,
                    a.marca,
                    a.modelo,
                    a.id_empresa
                FROM orden_mantenimiento o
                INNER JOIN activo_tecnologico a
                    ON o.id_activo = a.id_activo
                WHERE o.id_orden = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ACTIVOS DISPONIBLES */
    public function activos($id_empresa)
    {
        $sql = "SELECT
                    id_activo,
                    codigo_activo,
                    nombre
                FROM activo_tecnologico
                WHERE id_empresa = ?
                AND estado <> 'Fuera de servicio'
                ORDER BY nombre";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id_empresa);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* CREAR ORDEN */
    public function crear($datos)
{
    $sql = "INSERT INTO orden_mantenimiento (
                id_activo,
                id_tecnico,
                tipo_mantenimiento,
                fecha_programada,
                fecha_inicio,
                fecha_fin,
                prioridad,
                estado_orden,
                descripcion
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("ERROR PREPARANDO: " . $this->conn->error);
    }

    $stmt->bind_param(
        "iisssssss",
        $datos["id_activo"],
        $datos["id_tecnico"],
        $datos["tipo_mantenimiento"],
        $datos["fecha_programada"],
        $datos["fecha_inicio"],
        $datos["fecha_fin"],
        $datos["prioridad"],
        $datos["estado_orden"],
        $datos["descripcion"]
    );

    if (!$stmt->execute()) {
        die("ERROR AL GUARDAR ORDEN: " . $stmt->error);
    }

    return true;
}

    /* ACTUALIZAR ORDEN */
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE orden_mantenimiento
                SET
                    id_activo = ?,
                    id_tecnico = ?,
                    tipo_mantenimiento = ?,
                    fecha_programada = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?,
                    prioridad = ?,
                    estado_orden = ?,
                    descripcion = ?
                WHERE id_orden = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param(
            "iisssssssi",
            $datos["id_activo"],
            $datos["id_tecnico"],
            $datos["tipo_mantenimiento"],
            $datos["fecha_programada"],
            $datos["fecha_inicio"],
            $datos["fecha_fin"],
            $datos["prioridad"],
            $datos["estado_orden"],
            $datos["descripcion"],
            $id
        );

        return $stmt->execute();
    }

    /* CANCELAR ORDEN */
    public function cancelar($id)
    {
        $sql = "UPDATE orden_mantenimiento
                SET estado_orden = 'Cancelada'
                WHERE id_orden = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /* TÉCNICOS DISPONIBLES */
public function tecnicos()
{
    $sql = "SELECT
                id_tecnico,
                nombre
            FROM tecnico
            ORDER BY nombre";

    $resultado = $this->conn->query($sql);

    if (!$resultado) {
        die("Error consultando técnicos: " . $this->conn->error);
    }

    return $resultado;
}
}