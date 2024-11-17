<?php
class matriculaModel
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
        $sql = 'SELECT m.idMatricula,
                       m.fecha,
                       m.idEstudiante,
                       e.nombre,
                       e.apellido,
                       c.nombre curso,
                       m.idCurso,
                       u.alias usuario,
                       m.idUsuario
                FROM matriculas m
                JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                JOIN cursos c ON m.idCurso = c.idCurso
                JOIN usuarios u ON m.idUsuario = u.idUsuario
                ORDER BY m.idMatricula DESC';
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function insertar($fecha, $idEstudiante, $idUsuario, $idCurso)
    {
        $sql =
            "INSERT INTO matriculas VALUES (0, :fecha, :idEstudiante, :idUsuario, :idCurso)";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":fecha", $fecha);
        $statement->bindParam(":idEstudiante", $idEstudiante);
        $statement->bindParam(":idUsuario", $idUsuario);
        $statement->bindParam(":idCurso", $idCurso);
        $statement->execute();
        return $this->PDO->lastInsertId();
    }

    public function actualizar(
        $idMatricula,
        $fecha,
        $idEstudiante,
        $idUsuario,
        $idCurso
    ) {
        $sql = 'UPDATE matriculas
                SET fecha = :fecha,
                    idEstudiante = :idEstudiante,
                    idUsuario = :idUsuario,
                    idCurso = :idCurso
                WHERE idMatricula = :idMatricula';
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":fecha", $fecha);
        $statement->bindParam(":idEstudiante", $idEstudiante);
        $statement->bindParam(":idUsuario", $idUsuario);
        $statement->bindParam(":idCurso", $idCurso);
        $statement->bindParam(":idMatricula", $idMatricula);
        return $statement->execute() ? true : false;
    }

    public function eliminar($idMatricula)
    {
        $sql = "DELETE FROM matriculas WHERE idMatricula = :idMatricula";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":idMatricula", $idMatricula);
        return $statement->execute() ? true : false;
    }

    public function cargarDesplegableCursos()
    {
        $sql = "SELECT idCurso, nombre FROM cursos ORDER BY idCurso ASC";
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function cargarDesplegableUsuario($Usuario)
    {
        $sql = "SELECT idUsuario, alias FROM usuarios WHERE alias = :Usuario";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":Usuario", $Usuario);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function cargarDesplegableEstudiantes()
    {
        $sql = 'SELECT idEstudiante,
                       CONCAT(nombre, " ", apellido) AS estudiante
                FROM estudiantes
                ORDER BY idEstudiante ASC';
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function buscar($idMatricula)
    {
        $sql = "SELECT * FROM matriculas WHERE idMatricula = :idMatricula";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":idMatricula", $idMatricula);
        return $statement->execute() ? $statement->fetch() : false;
    }
}
?>
