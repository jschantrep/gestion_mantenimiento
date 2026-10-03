<?php

class Historial
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listar($busqueda = "")
    {
        $sql = "SELECT
                    h.*,
                    a.codigo_activo,
                    a.nombre AS activo,
                    a.marca,
                    a.modelo
                FROM historial_estado_activo h
                INNER JOIN activo_tecnologico a
                    ON h.id_activo = a.id_activo
                WHERE 1=1";

        $parametros = [];
        $tipos = "";

        if ($busqueda !== "") {

            $sql .= " AND (
                        a.codigo_activo LIKE ?
                        OR a.nombre LIKE ?
                        OR h.estado_anterior LIKE ?
                        OR h.estado_nuevo LIKE ?
                        OR h.motivo LIKE ?
                    )";

            $buscar = "%" . $busqueda . "%";

            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;
            $parametros[] = $buscar;

            $tipos .= "sssss";
        }

        $sql .= " ORDER BY h.fecha DESC, h.id_historial DESC";

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
                    h.*,
                    a.codigo_activo,
                    a.nombre AS activo,
                    a.marca,
                    a.modelo
                FROM historial_estado_activo h
                INNER JOIN activo_tecnologico a
                    ON h.id_activo = a.id_activo
                WHERE h.id_historial = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}