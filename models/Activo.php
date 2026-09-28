<?php

class Activo
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listar($busqueda = "", $estado = "", $criticidad = "", $tipo = "")
{
   $sql = "SELECT 
            a.*,
            t.nombre AS tipo_activo,
            u.nombre AS ubicacion,
            u.area
        FROM activo_tecnologico a

        INNER JOIN tipo_activo t 
            ON a.id_tipo_activo = t.id_tipo_activo

        INNER JOIN ubicacion u 
            ON a.id_ubicacion = u.id_ubicacion

        WHERE 1=1";

    $parametros = [];
    $tipos = "";

    // Buscar por texto
    if ($busqueda !== "") {
        $sql .= " AND (
                    a.codigo_activo LIKE ?
                    OR a.nombre LIKE ?
                    OR a.marca LIKE ?
                    OR t.nombre LIKE ?
                    OR u.nombre LIKE ?
                  )";

        $busquedaLike = "%" . $busqueda . "%";

        $parametros[] = $busquedaLike;
        $parametros[] = $busquedaLike;
        $parametros[] = $busquedaLike;
        $parametros[] = $busquedaLike;
        $parametros[] = $busquedaLike;

        $tipos .= "sssss";
    }

    // Filtro por estado
    if ($estado !== "") {
        $sql .= " AND a.estado = ?";
        $parametros[] = $estado;
        $tipos .= "s";
    }

    // Filtro por criticidad
    if ($criticidad !== "") {
        $sql .= " AND a.criticidad = ?";
        $parametros[] = $criticidad;
        $tipos .= "s";
    }

    // Filtro por tipo
    if ($tipo !== "") {
        $sql .= " AND a.id_tipo_activo = ?";
        $parametros[] = $tipo;
        $tipos .= "i";
    }

    $sql .= " ORDER BY a.id_activo DESC";

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
                    a.*,
                    t.nombre AS tipo_activo,
                    u.nombre AS ubicacion,
                    u.edificio,
                    u.piso,
                    u.area
                FROM activo_tecnologico a

                INNER JOIN tipo_activo t
                    ON a.id_tipo_activo = t.id_tipo_activo

                INNER JOIN ubicacion u
                    ON a.id_ubicacion = u.id_ubicacion

                WHERE a.id_activo = ?";
        

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    public function tipos()
    {
        $sql = "SELECT
                    id_tipo_activo,
                    nombre
                FROM tipo_activo
                ORDER BY nombre";

        return $this->conn->query($sql);
    }


    public function ubicaciones($id_empresa)
    {
        $sql = "SELECT
                    id_ubicacion,
                    nombre,
                    edificio,
                    piso,
                    area
                FROM ubicacion
                WHERE id_empresa = ?
                ORDER BY nombre";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id_empresa);

        $stmt->execute();

        return $stmt->get_result();
    }


    public function crear($datos)
    {
        $sql = "INSERT INTO activo_tecnologico (
                    id_empresa,
                    id_tipo_activo,
                    id_ubicacion,
                    codigo_activo,
                    nombre,
                    marca,
                    modelo,
                    numero_serie,
                    fecha_adquisicion,
                    criticidad,
                    estado,
                    horas_uso_acumuladas
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iiissssssssi",
            $datos["id_empresa"],
            $datos["id_tipo_activo"],
            $datos["id_ubicacion"],
            $datos["codigo_activo"],
            $datos["nombre"],
            $datos["marca"],
            $datos["modelo"],
            $datos["numero_serie"],
            $datos["fecha_adquisicion"],
            $datos["criticidad"],
            $datos["estado"],
            $datos["horas_uso_acumuladas"]
        );

        return $stmt->execute();
    }


    public function actualizar($id, $datos)
    {
        $sql = "UPDATE activo_tecnologico
                SET
                    id_tipo_activo = ?,
                    id_ubicacion = ?,
                    codigo_activo = ?,
                    nombre = ?,
                    marca = ?,
                    modelo = ?,
                    numero_serie = ?,
                    fecha_adquisicion = ?,
                    criticidad = ?,
                    estado = ?,
                    horas_uso_acumuladas = ?
                WHERE id_activo = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iissssssssii",
            $datos["id_tipo_activo"],
            $datos["id_ubicacion"],
            $datos["codigo_activo"],
            $datos["nombre"],
            $datos["marca"],
            $datos["modelo"],
            $datos["numero_serie"],
            $datos["fecha_adquisicion"],
            $datos["criticidad"],
            $datos["estado"],
            $datos["horas_uso_acumuladas"],
            $id
        );

        return $stmt->execute();
    }


    public function eliminar($id)
    {
        $sql = "UPDATE activo_tecnologico
                SET estado = 'Fuera de servicio'
                WHERE id_activo = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error preparando consulta: " . $this->conn->error);
        }

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}