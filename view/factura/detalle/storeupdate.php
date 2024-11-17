<?php
session_start();
$sesion = $_SESSION["usuario"];

if (isset($_POST["idConcepto"])) {
    $idConcepto = $_POST["idConcepto"];
}
if (isset($_POST["concepto"])) {
    $concepto = $_POST["concepto"];
}
if (isset($_POST["cantidad"])) {
    $cantidad = $_POST["cantidad"];
}
if (isset($_POST["unitario"])) {
    $unitario = $_POST["unitario"];
}

require_once "insert.php";
$JSONdetalle = new detalleFactura();

$file = "tmpdetallefacturas" . $sesion . ".json";
$exist = is_file($file);

// Delete detail
if (isset($_GET["id"])) {
    $idtmpDetalle = intval($_GET["id"]);
    $JSONdetalle->deleteDetalle($idtmpDetalle, $sesion);
}

// Add detail
if (!empty($idConcepto) and !empty($cantidad) and !empty($unitario)) {
    if ($exist) {
        $arrDetalles = $JSONdetalle->getDetalles($sesion);
        $ultimoID = 0;
        foreach ($arrDetalles as $detalle) {
            $ultimoID++;
        }
        $ultimoID = $ultimoID + 1;
    } else {
        $ultimoID = 1;
    }

    $arregloDetalles = [
        "idTmpDetalle" => $ultimoID,
        "idConcepto" => $idConcepto,
        "concepto" => $concepto,
        "cantidad" => $cantidad,
        "unitario" => $unitario,
        "sesion" => $sesion,
    ];

    if ($exist) {
        $JSONdetalle->createDetalleExist($arregloDetalles, $sesion);
    } else {
        $JSONdetalle->createDetalleNotExist($arregloDetalles, $sesion);
    }
}
?>

<table class="table">
    <tr>
        <th class='text-center'>CODIGO</th>
        <th class='text-center'>CANTIDAD</th>
        <th>DESCRIPCION</th>
        <th class='text-right'>UNITARIO</th>
        <th class='text-right'>TOTAL</th>
        <th></th>
    </tr>
    <?php
    $sumador_total = 0;
    $total_detalle = 0;
    $arrDetalles = $JSONdetalle->getDetalles($sesion);

    foreach ($arrDetalles as $row) {

        $idTmpDetalle = $row["idTmpDetalle"];
        $idConcepto = $row["idConcepto"];
        $cantidad = $row["cantidad"];
        $descripcion = $row["concepto"];
        $precio_venta = $row["unitario"];
        $precio_venta_f = number_format($precio_venta, 2, ",", "."); // Format variables
        $precio_total = $precio_venta * $cantidad;
        $precio_total_f = number_format($precio_total, 2, ",", "."); // Format total price
        $sumador_total += $precio_venta; // Sum
        $total_detalle += $precio_total;
        ?>
        <tr>
            <td class='text-center'><?= $idConcepto ?></td>
            <td class='text-center'><?= $cantidad ?></td>
            <td><?= $descripcion ?></td>
            <td class='text-right'><?= $precio_venta_f ?></td>
            <td class='text-right'><?= $precio_total_f ?></td>
            <td class='text-center'>
                <a href="#" onclick="eliminar('<?= $idTmpDetalle ?>')">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
    <?php
    }
    $subtotal = number_format($sumador_total, 2, ",", ".");
    $total_iva = $total_detalle / 11;
    $total_iva = number_format($total_iva, 2, ",", ".");
    ?>
    <tr>
        <td class='text-right' colspan=4>SUBTOTAL ₲</td>
        <td class='text-right'><?= number_format(
            $total_detalle,
            2,
            ",",
            "."
        ) ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>IVA 10% </td>
        <td class='text-right'><?= $total_iva ?></td>
        <td></td>
    </tr>
    <tr>
        <td class='text-right' colspan=4>TOTAL ₲</td>
        <td class='text-right'><?= number_format(
            $total_detalle,
            2,
            ",",
            "."
        ) ?></td>
        <td></td>
    </tr>
</table>
