<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
session_start();

if (!isset($_SESSION["usuario"])) {
    header("location: ../usuario/login.php");
}

$Usuario = $_SESSION["usuario"];

require_once CONTROLLER_PATH . "matriculaController.php";
$object = new matriculaController();
$estudiantes = $object->combolistEstudiantes();
$usuarios = $object->combolistUsuario($Usuario);
$cursos = $object->combolistCursos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <title>Matriculas</title>
</head>
<body>
    <?php require_once VIEW_PATH . "navbar/navbar.php"; ?>

    <div class="container">
        <div class="mb-3">
            <h2>Agregando nuevo registro</h2>
        </div>
        <form id="formPersona" action="store.php" method="post" class="g-3 needs-validation" novalidate>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <span class="col-md-1 col-md-offset-2 text-center">
                    <i class="fa fa-pencil-square-o bigicon"></i>
                </span>
                <input type="date" class="form-control" id="fecha" name="fecha" autofocus required>
                <div class="invalid-feedback">ingrese o seleccione fecha válida!</div>
            </div>

            <div class="mb-3">
                <label for="idEstudiante" class="form-label">Estudiante</label>
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

            <div class="mb-3">
                <label for="idUsuario" class="form-label">Usuario</label>
                <select class="form-control" name="idUsuario" id="idUsuario">
                    <?php foreach ($usuarios as $user) { ?>
                        <option selected value="<?= $user[
                            "idUsuario"
                        ] ?>"><?= $user["idUsuario"] .
    "-" .
    $user["alias"] ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">seleccione un elemento válido!</div>
            </div>

            <div class="mb-3">
                <label for="idCurso" class="form-label">Curso</label>
                <select class="form-control" name="idCurso" id="idCurso" required>
                    <option selected disabled value="">No especificado</option>
                    <?php foreach ($cursos as $curso) { ?>
                        <option value="<?= $curso["idCurso"] ?>"><?= $curso[
    "nombre"
] ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">seleccione un elemento válido!</div>
            </div>
        </form>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/validate.js"></script>
</body>
</html>
