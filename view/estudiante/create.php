<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("location: ../usuario/login.php");
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "estudianteController.php";

$object = new estudianteController();
$ciudades = $object->combolist();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <title>Form PHP</title>
</head>
<body>
    <?php require_once VIEW_PATH . "navbar/navbar.php"; ?>

    <div class="container">
        <div class="mb-3">
            <h2>Agregando nuevo registro</h2>
        </div>
        <form id="formPersona" action="store.php" method="post" class="g-3 needs-validation" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <span class="col-md-1 col-md-offset-2 text-center">
                    <i class="fa fa-pencil-square-o bigicon"></i>
                </span>
                <input type="text" class="form-control" id="nombre" name="nombre" autofocus required>
                <div class="invalid-feedback">ingrese un nombre válido!</div>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
                <div class="invalid-feedback">ingrese un apellido válido!</div>
            </div>

            <div class="mb-3">
                <label for="idCiudad" class="form-label">Código ciudad</label>
                <select class="form-control" name="idCiudad" id="idCiudad" required>
                    <option selected disabled value="">No especificado</option>
                    <?php foreach ($ciudades as $ciudad) { ?>
                        <option value="<?= $ciudad["idCiudad"] ?>"><?= $ciudad[
    "nombre"
] ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">seleccione un elemento válido!</div>
            </div>

            <div class="mb-3">
                <label for="cin" class="form-label">C. I. Nº</label>
                <input type="number" class="form-control" id="cin" name="cin" required>
                <div class="invalid-feedback">ingrese una cédula válida!</div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg col-4">Guardar</button>
        </form>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/validate.js"></script>
</body>
</html>
