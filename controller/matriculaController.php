<?php
class matriculaController
{
    private $model;

    public function __construct()
    {
        include_once $_SERVER["DOCUMENT_ROOT"] .
            "/semana5/tallermvcphp/routes.php";
        require_once MODEL_PATH . "matriculaModel.php";
        $this->model = new matriculaModel();
    }

    public function select()
    {
        return $this->model->listar()
            ? $this->model->listar()
            : $this->model->listar();
    }

    public function insert($fecha, $idEstudiante, $idUsuario, $idCurso)
    {
        $id = $this->model->insertar(
            $fecha,
            $idEstudiante,
            $idUsuario,
            $idCurso
        );
        return $id != false
            ? header("location: ./index.php")
            : header("location: ./create.php");
    }

    public function update($id, $fecha, $idEstudiante, $idUsuario, $idCurso)
    {
        return $this->model->actualizar(
            $id,
            $fecha,
            $idEstudiante,
            $idUsuario,
            $idCurso
        ) != false
            ? header("location: ./index.php")
            : header("location: ./edit.php?id=" . $id);
    }

    public function delete($id)
    {
        return $this->model->eliminar($id)
            ? header("location: ./index.php")
            : header("location: ./index.php");
    }

    public function search($id)
    {
        return $this->model->buscar($id) != false
            ? $this->model->buscar($id)
            : header("location: ./index.php");
    }

    public function combolistEstudiantes()
    {
        return $this->model->cargarDesplegableEstudiantes()
            ? $this->model->cargarDesplegableEstudiantes()
            : false;
    }

    public function combolistUsuario($Usuario)
    {
        return $this->model->cargarDesplegableUsuario($Usuario)
            ? $this->model->cargarDesplegableUsuario($Usuario)
            : false;
    }

    public function combolistCursos()
    {
        return $this->model->cargarDesplegableCursos()
            ? $this->model->cargarDesplegableCursos()
            : false;
    }
}
?>
