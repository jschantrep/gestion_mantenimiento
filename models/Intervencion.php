<?php

class Intervencion
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listar($busqueda = "", $tipo = "")
    {
        $sql = "SELECT
                    i.*,
                    o.id_orden,
                    o.tipo_mantenimiento,
                    o.fecha_programada,
                    a.codigo_activo,
                    a.nombre AS activo
                FROM intervencion i
                INNER JOIN orden_mantenimiento o
                    ON i.id_orden = o.id_orden
                INNER JOIN activo_tecnologico a
                    ON o.id_activo = a.id_activo
                WHERE 1=1";

        $parametros = [];
        $tipos = "";

        if ($busqueda !== "") {

            $sql .= " AND (
                        a.codigo_activo LIKE ?
                        OR a.nombre LIKE ?
                        OR i.resultado LIKE ?
                        OR i.causa_falla LIKE ?
                    )";

            $buscar = "%" . $busqueda . "%";

            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;

            $tipos .= "ssss";
        }

        if ($tipo !== "") {

            $sql .= " AND o.tipo_mantenimiento = ?";

            $parametros[] = $tipo;
            $tipos .= "s";
        }

        $sql .= " ORDER BY i.id_intervencion DESC";

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


    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    i.*,
                    o.id_orden,
                    o.tipo_mantenimiento,
                    o.fecha_programada,
                    o.descripcion AS descripcion_orden,
                    a.codigo_activo,
                    a.nombre AS activo,
                    a.marca,
                    a.modelo
                FROM intervencion i
                INNER JOIN orden_mantenimiento o
                    ON i.id_orden = o.id_orden
                INNER JOIN activo_tecnologico a
                    ON o.id_activo = a.id_activo
                WHERE i.id_intervencion = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    public function ordenes()
    {
        $sql = "SELECT
                    o.id_orden,
                    o.tipo_mantenimiento,
                    o.fecha_programada,
                    a.codigo_activo,
                    a.nombre AS activo
                FROM orden_mantenimiento o
                INNER JOIN activo_tecnologico a
                    ON o.id_activo = a.id_activo
                WHERE o.estado_orden <> 'Cancelada'
                ORDER BY o.id_orden DESC";

        $resultado = $this->conn->query($sql);

        if (!$resultado) {
            die("Error consultando órdenes: " . $this->conn->error);
        }

        return $resultado;
    }


    public function crear($datos)
    {
        $sql = "INSERT INTO intervencion (
                    id_orden,
                    fecha_intervencion,
                    horas_trabajo,
                    tiempo_parada_horas,
                    resultado,
                    causa_falla
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("ERROR PREPARANDO: " . $this->conn->error);
        }

        $stmt->bind_param(
            "isddss",
            $datos["id_orden"],
            $datos["fecha_intervencion"],
            $datos["horas_trabajo"],
            $datos["tiempo_parada_horas"],
            $datos["resultado"],
            $datos["causa_falla"]
        );

        if (!$stmt->execute()) {
            die("ERROR AL GUARDAR INTERVENCIÓN: " . $stmt->error);
        }

        return true;
    }


    public function actualizar($id, $datos)
    {
        $sql = "UPDATE intervencion SET
                    id_orden = ?,
                    fecha_intervencion = ?,
                    horas_trabajo = ?,
                    tiempo_parada_horas = ?,
                    resultado = ?,
                    causa_falla = ?
                WHERE id_intervencion = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("ERROR PREPARANDO: " . $this->conn->error);
        }

        $stmt->bind_param(
            "isddssi",
            $datos["id_orden"],
            $datos["fecha_intervencion"],
            $datos["horas_trabajo"],
            $datos["tiempo_parada_horas"],
            $datos["resultado"],
            $datos["causa_falla"],
            $id
        );

        if (!$stmt->execute()) {
            die("ERROR AL ACTUALIZAR INTERVENCIÓN: " . $stmt->error);
        }

        return true;
    }
}