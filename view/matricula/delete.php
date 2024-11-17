<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "matriculaController.php";

$object = new matriculaController();
$idMatricula = $_REQUEST["id"];
$object->delete($idMatricula);
?>
