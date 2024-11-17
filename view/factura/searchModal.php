<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "facturaController.php";

$object = new facturaController();
$rows = $object->listcursos();
?>

<div class="table-responsive">
    <table id="tablaDetalles" class="table mb-0">
        <thead style="background-color: #002d72;">
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Descripción</th>
                <th scope="col">Cantidad</th>
                <th scope="col">Importe</th>
                <th scope="col" class="text-center" style="width: 36px;">Agregar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) {

                $idConcepto = $row["idCurso"];
                $descripcion = $row["nombre"];
                $importeUnitario = $row["importe"];
                ?>
                <tr>
                    <td><?= $idConcepto ?></td>
                    <td>
                        <?= $descripcion ?>
                        <input type="hidden" id="concepto_<?= $idConcepto ?>" value="<?= $descripcion ?>">
                    </td>
                    <td class='col-xs-1'>
                        <div class="float-right">
                            <input type="number" class="form-control" style="text-align:right"
                                id="cantidad_<?= $idConcepto ?>" value="1">
                        </div>
                    </td>
                    <td class='col-xs-2'>
                        <div class="float-right">
                            <input type="number" class="form-control" style="text-align:right"
                                id="importe_<?= $idConcepto ?>" value="<?= $importeUnitario ?>">
                        </div>
                    </td>
                    <td class='text-center'>
                        <a class='btn btn-success' href="#" onclick="agregar('<?= $idConcepto ?>')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>
    </table>
    <div id="loader" style="position: absolute; text-align: center; top: 55px; width: 100%; display:none;">
    </div><!-- Carga gif animado -->
    <div id="outer_div"></div><!-- Datos ajax Final -->
</div>

<script>
$(document).ready(function() {
    var table = new DataTable('#tablaDetalles', {
        language: {
            url: '../../assets/js/es-ES.json',
        },
        'paging': true,
        lengthMenu: [
            [5, 10, 25, -1],
            [5, 10, 25, 'Todos']
        ],
        columnDefs: [{
            orderable: false,
            targets: [0,1,2,3,4]
        }]
    });
});
</script>
