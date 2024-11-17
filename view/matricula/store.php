<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "matriculaController.php";

$object = new matriculaController();

$fecha = $_REQUEST["fecha"];
$idEstudiante = $_REQUEST["idEstudiante"];
$idUsuario = $_REQUEST["idUsuario"];
$idCurso = $_REQUEST["idCurso"];

$registro = $object->insert($fecha, $idEstudiante, $idUsuario, $idCurso);
?>
<script>
    history.replaceState(null, null, location.pathname);
</script>
