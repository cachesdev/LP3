<?php
class usuarioModel
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
        $sql = "SELECT * FROM usuarios ORDER BY idUsuario";
        $statement = $this->PDO->prepare($sql);
        return $statement->execute() ? $statement->fetchAll() : false;
    }

    public function buscar($usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE alias = :alias";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":alias", $usuario);
        return $statement->execute() ? $statement->fetch() : false;
    }

    public function insertar($alias, $clave, $idrol)
    {
        $clave = password_hash($clave, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios VALUES (0, :alias, :clave, :idrol)";
        $statement = $this->PDO->prepare($sql);
        $statement->bindParam(":alias", $alias);
        $statement->bindParam(":clave", $clave);
        $statement->bindParam(":idrol", $idrol);
        $statement->execute();
        return $this->PDO->lastInsertId();
    }
}
?>
