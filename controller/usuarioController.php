<?php
class usuarioController
{
    private $model;

    public function __construct()
    {
        include_once $_SERVER["DOCUMENT_ROOT"] .
            "/semana5/tallermvcphp/routes.php";
        require_once MODEL_PATH . "usuarioModel.php";
        $this->model = new usuarioModel();
    }

    public function search($usuario)
    {
        return $this->model->buscar($usuario) != false
            ? $this->model->buscar($usuario)
            : false;
    }

    public function select()
    {
        return $this->model->listar() ? $this->model->listar() : false;
    }
}
?>
