<?php
class facturaModel
{
    private $PDO;

    public function __construct()
    {
        include_once $_SERVER["DOCUMENT_ROOT"] .
            "/semana5/tallermvcphp/routes.php";
        require_once DAO_PATH . "database.php";
        $data = new dataConex();
        $this->PDO = $data->conexion();
    }

    public function listar()
    {
        $sql = 'SELECT f.numero,
                       DATE_FORMAT(f.fecha, "%d/%m/%Y") fecha,
                       concat(e.nombre, " ", e.apellido) cliente,
                       e.cin,
                       SUM(d.cantidad * d.importe) total
                FROM facturas f
                JOIN detallefacturas d ON f.numero = d.numero
                JOIN estudiantes e ON f.idEstudiante = e.idEstudiante
                WHERE f.anulada = 0
                GROUP BY f.numero
                ORDER BY f.numero DESC';
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function listarCursos()
    {
        $sql = "SELECT * FROM cursos ORDER BY idCurso";
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function buscar($factura)
    {
        $sql = "SELECT * FROM facturas WHERE numero = :numero";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":numero", $factura);
        return $statement->execute() ? $statement->fetch() : false;
    }

    public function insertar($fecha, $idEstudiante, $idFormaPago, $idUsuario)
    {
        $sql =
            "INSERT INTO facturas VALUES (0, :fecha, :idEstudiante, :idFormaPago, 0, :idUsuario)";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":fecha", $fecha);
        $statement->bindParam(":idEstudiante", $idEstudiante);
        $statement->bindParam(":idFormaPago", $idFormaPago);
        $statement->bindParam(":idUsuario", $idUsuario);
        $statement->execute();
        return $this->PDO->lastInsertId();
    }

    public function insertarDetalle($numero, $idCurso, $cantidad, $importe)
    {
        $sql =
            "INSERT INTO detallefacturas VALUES (:numero, :idCurso, :cantidad, :importe)";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":numero", $numero);
        $statement->bindParam(":idCurso", $idCurso);
        $statement->bindParam(":cantidad", $cantidad);
        $statement->bindParam(":importe", $importe);
        $statement->execute();
        return $this->PDO->lastInsertId();
    }

    public function actualizar($numero)
    {
        $sql = "UPDATE facturas SET anulada = 1 WHERE numero = :numero";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":numero", $numero);
        return $statement->execute() ? true : false;
    }

    public function cargarEstudiantes()
    {
        $sql = 'SELECT e.idEstudiante,
                       concat(e.nombre, " ", e.apellido) estudiante,
                       e.cin
                FROM estudiantes e
                ORDER BY e.nombre';
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function cargarEstudiantesID($idEstudiante)
    {
        $sql = 'SELECT e.idEstudiante,
                       concat(e.nombre, " ", e.apellido) razonsocial,
                       e.cin,
                       c.nombre ciudad
                FROM estudiantes e
                JOIN ciudades c ON e.idCiudad = c.idCiudad
                WHERE e.idEstudiante = :idEstudiante
                ORDER BY e.nombre';
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":idEstudiante", $idEstudiante);
        return $statement->execute() ? $statement->fetch() : false;
    }

    public function cargarFormPagos()
    {
        $sql = 'SELECT f.idFormaPago, f.descripcion
                FROM formapagos f
                ORDER BY f.idFormaPago';
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }
}
?>
