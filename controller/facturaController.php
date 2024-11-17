<?php
class facturaController
{
    private $model;

    public function __construct()
    {
        include_once $_SERVER["DOCUMENT_ROOT"] .
            "/semana5/tallermvcphp/routes.php";
        require_once MODEL_PATH . "facturaModel.php";
        $this->model = new facturaModel();
    }

    public function search($factura)
    {
        return $this->model->buscar($factura) != false
            ? $this->model->buscar($factura)
            : false;
    }

    public function select()
    {
        return $this->model->listar() != false
            ? $this->model->listar()
            : $this->model->listar();
    }

    public function listcursos()
    {
        return $this->model->listarCursos() != false
            ? $this->model->listarCursos()
            : false;
    }

    public function combolistestudiantes()
    {
        return $this->model->cargarEstudiantes() != false
            ? $this->model->cargarEstudiantes()
            : false;
    }

    public function listestudiantes($idEstudiante)
    {
        return $this->model->cargarEstudiantesID($idEstudiante) != false
            ? $this->model->cargarEstudiantesID($idEstudiante)
            : false;
    }

    public function combolistformapagos()
    {
        return $this->model->cargarFormPagos() != false
            ? $this->model->cargarFormPagos()
            : false;
    }

    public function insert($fecha, $idEstudiante, $idFormaPago, $idUsuario)
    {
        $id = $this->model->insertar(
            $fecha,
            $idEstudiante,
            $idFormaPago,
            $idUsuario
        );
        return $id != false ? $id : false;
    }

    public function insertdetail($numero, $idCurso, $cantidad, $importe)
    {
        $id = $this->model->insertarDetalle(
            $numero,
            $idCurso,
            $cantidad,
            $importe
        );
        return $id != false ? $id : false;
    }

    public function update($numero)
    {
        return $this->model->actualizar($numero) != false
            ? header("location: ./index.php")
            : false;
    }
}
?>
