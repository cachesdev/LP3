<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

$usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : null;
$clave = isset($_POST["clave"]) ? $_POST["clave"] : null;

include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "usuarioController.php";
$object = new usuarioController();

$resultado = $object->search($usuario);
if ($resultado) {
    $data = $resultado;
    $idUsuario = $resultado["idUsuario"];
    $usuario = $resultado["alias"];
    $hash = $resultado["clave"];

    if (password_verify($clave, $hash)) {
        $_SESSION["idUsuario"] = $idUsuario;
        $_SESSION["usuario"] = $usuario;
    } else {
        $_SESSION["idUsuario"] = null;
        $_SESSION["usuario"] = null;
        $data = null;
    }
} else {
    $_SESSION["idUsuario"] = null;
    $_SESSION["usuario"] = null;
    $data = null;
}

print json_encode($data);
exit();
?>
