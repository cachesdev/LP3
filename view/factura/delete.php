<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "facturaController.php";

$object = new facturaController();
$numero = $_REQUEST["numero"];
$object->update($numero);
?>
