<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["usuario"])) {
    header("location: ../usuario/login.php");
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "facturaController.php";

$object = new facturaController();
$estudiantes = $object->combolistestudiantes();
$formapagos = $object->combolistformapagos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once ROOT_PATH . "header.php"; ?>
    <title>Form PHP</title>
</head>
<body>
    <?php require_once VIEW_PATH . "navbar/navbar.php"; ?>

    <div class="container">
        <div class="mb-3">
            <h4>Agregando factura</h4>
        </div>

        <form id="addFactura" role="form" class="g-3 needs-validation" novalidate>
            <div class="row">
                <div class="col">
                    <label for="fecha" class="form-label fw-bolder">Fecha</label>
                    <input type="text" class="form-control" id="fecha" name="fecha" value="<?= date(
                        "d/m/Y"
                    ) ?>" disabled>
                    <div class="invalid-feedback">ingrese fecha válida!</div>
                </div>
                <div class="col">
                    <label for="idEstudiante" class="form-label fw-bolder">Estudiante</label>
                    <select class="form-control" name="idEstudiante" id="idEstudiante" required>
                        <option selected disabled value="">No especificado</option>
                        <?php foreach ($estudiantes as $estudiante) { ?>
                            <option value="<?= $estudiante[
                                "idEstudiante"
                            ] ?>"><?= $estudiante["estudiante"] ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">seleccione un elemento válido!</div>
                </div>
                <div class="col">
                    <label for="idFormaPago" class="form-label fw-bolder">Forma de Pago</label>
                    <select class="form-control" name="idFormaPago" id="idFormaPago" required>
                        <option selected disabled value="">No especificado</option>
                        <?php foreach ($formapagos as $formapago) { ?>
                            <option value="<?= $formapago[
                                "idFormaPago"
                            ] ?>"><?= $formapago["descripcion"] ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">seleccione un elemento válido!</div>
                </div>
            </div>
            <div class="col-md-11">
                <div class="float-none">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarConcepto">
                        <span class="fa fa-search"></span> Agregar concepto
                    </button>
                    <button type="submit" class="btn btn-success">
                        <span class="fa fa-print"></span> Imprimir factura
                    </button>
                </div>
            </div>
        </form>
        <div id="resultados" class='col-md-12' style="margin-top:10px"></div>

        <?php include "addModal.php"; ?>
    </div>

    <?php require_once ROOT_PATH . "footer.php"; ?>
    <script src="../../assets/js/addfactura.js"></script>
    <script>
        $(document).ready(function() {
            $('#idEstudiante').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder')
            });
        });
    </script>
</body>
</html>
