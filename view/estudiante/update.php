<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "estudianteController.php";

$object = new estudianteController();

$idEstudiante = $_REQUEST["id"];
$nombre = $_REQUEST["nombre"];
$apellido = $_REQUEST["apellido"];
$idCiudad = $_REQUEST["idCiudad"];
$cin = $_REQUEST["cin"];

$object->update($idEstudiante, $nombre, $apellido, $idCiudad, $cin);
?>
<script>
    history.replaceState(null, null, location.pathname);
</script>
