<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";

require_once CONTROLLER_PATH . "facturaController.php";
$object = new facturaController();
$rows = $object->select();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once ROOT_PATH . "header.php"; ?>
    <title>Facturas</title>
</head>
<body>
    <?php require_once VIEW_PATH . "navbar/navbar.php"; ?>

    <section class="intro">
        <div class="container">
            <div class="mb-3"></div>
            <div class="mb-3">
                <a href="create.php" class="btn btn-primary">Agregar</a>
                <a href="#" target="_blank" class="btn btn-info">Imprimir</a>
            </div>
            <div class="table-responsive table-scroll" data-mdb-perfect-scrollbar="true" style="position: relative; height=700px;">
                <table id="myTabla" class="table table-striped mb-0">
                    <thead style="background-color: #002d72;">
                        <tr>
                            <th scope="col">NUMERO</th>
                            <th scope="col">FECHA</th>
                            <th scope="col">CLIENTE</th>
                            <th scope="col">OPERACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ((array) $rows as $row) { ?>
                            <tr>
                                <td><?= $row["numero"] ?></td>
                                <td><?= $row["fecha"] ?></td>
                                <td><?= $row["cliente"] ?></td>
                                <td>
                                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#idver<?= $row[
                                        "numero"
                                    ] ?>">Visualizar</a>
                                    <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#iddel<?= $row[
                                        "numero"
                                    ] ?>">Anular</a>

                                    <?php
                                    include "viewModal.php";
                                    include "deleteModal.php";
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Print area -->
    <div class="container" id="ventana" style="display:none;">
        <div class="mb-3">
            <h2 style="font-size: 3.00rem;">Listado de facturas</h2>
        </div>
        <div class="table-responsive table-scroll" data-mdb-perfect-scrollbar="true" style="position: relative; height=700px;">
            <table class="table table-striped mb-0" style="font-size: 2.00rem;">
                <thead>
                    <tr>
                        <th colspan="1" scope="col">NUMERO</th>
                        <th colspan="3" scope="col">FECHA</th>
                        <th colspan="3" scope="col">CLIENTE</th>
                        <th colspan="3" scope="col">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td colspan="1"><?= $row["numero"] ?></td>
                            <td colspan="3"><?= $row["fecha"] ?></td>
                            <td colspan="3"><?= $row["cliente"] ?></td>
                            <td colspan="3"><?= $row["total"] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include_once ROOT_PATH . "footer.php"; ?>

    <script>
        $(document).ready(function() {
            var table = new DataTable('#myTabla', {
                language: {
                    url: '../../assets/js/es-ES.json',
                },
                'paging': true,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, 'Todos']
                ],
                order: [[1, 'desc']]
            });
        });
    </script>
</body>
</html>
