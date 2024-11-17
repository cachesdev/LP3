<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "estudianteController.php";

$object = new estudianteController();
$idEstudiante = $_GET["id"];
$estudiante = $object->search($idEstudiante);
$ciudades = $object->combolist();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form PHP</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
</head>
<body>
    <?php require_once VIEW_PATH . "/navbar/navbar.php"; ?>

    <div class="container">
        <div class="mb-3">
            <h2>Editando registro</h2>
        </div>
        <form id="formPersona" action="update.php" method="post" class="g-3 needs-validation" novalidate>
            <div class="mb-3">
                <label for="id" class="form-label">ID Estudiante</label>
                <span class="col-md-1 col-md-offset-2 text-center">
                    <i class="fa fa-pencil-square-o bigicon"></i>
                </span>
                <input value="<?= $estudiante[0] ?>" type="text" class="form-control" id="id" name="id" readonly>
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <span class="col-md-1 col-md-offset-2 text-center">
                    <i class="fa fa-pencil-square-o bigicon"></i>
                </span>
                <input value="<?= $estudiante[1] ?>" type="text" class="form-control" id="nombre" name="nombre" autofocus required>
                <div class="invalid-feedback">ingrese un nombre válido!</div>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input value="<?= $estudiante[2] ?>" type="text" class="form-control" id="apellido" name="apellido" required>
                <div class="invalid-feedback">ingrese un apellido válido!</div>
            </div>

            <div class="mb-3">
                <label for="idCiudad" class="form-label">Código ciudad</label>
                <select class="form-control" name="idCiudad" id="idCiudad" required>
                    <option selected disabled value="">No especificado</option>
                    <?php foreach ($ciudades as $ciudad) {
                        if ($estudiante[3] == $ciudad["idCiudad"]) { ?>
                            <option selected="selected" value="<?= $ciudad[
                                "idCiudad"
                            ] ?>"><?= $ciudad["nombre"] ?></option>
                        <?php } else { ?>
                            <option value="<?= $ciudad[
                                "idCiudad"
                            ] ?>"><?= $ciudad["nombre"] ?></option>
                        <?php }
                    } ?>
                </select>
                <div class="invalid-feedback">seleccione un elemento válido!</div>
            </div>

            <div class="mb-3">
                <label for="cin" class="form-label">C. I. Nº</label>
                <input value="<?= $estudiante[4] ?>" type="number" class="form-control" id="cin" name="cin" required>
                <div class="invalid-feedback">ingrese una cédula válida!</div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/validate.js"></script>
</body>
</html>
