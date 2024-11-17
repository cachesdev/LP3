<!-- Style sheet - format -->
<?php include_once "style.php"; ?>

<?php
if (!isset($numero)) {
    $numero = 0;
}
if (!isset($estudiante)) {
    $estudiante = [
        "razonsocial" => "",
        "cin" => "",
        "ciudad" => "",
    ];
}
if (!isset($JSONdetalle)) {
    class tempJSON
    {
        public function getDetalles($s)
        {
            return [];
        }
        public function deleteAllDetalles($s)
        {
            return;
        }
    }
    $JSONdetalle = new tempJSON();
}
if (!isset($sesion)) {
    $sesion = "";
}
if (!isset($object)) {
    class tempObject
    {
        public function insertdetail($n, $i, $c, $imp)
        {
            return true;
        }
    }
    $object = new tempObject();
}
if (!isset($fecha)) {
    $fecha = date("Y-m-d");
}
?>

<!-- Invoice page -->
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: arial">
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <!-- Logo section commented out
            <td style="width: 25%; color: #444444;">
                <img style="width: 20%;" src="../../images/bg.png" alt="Logo"><br>
            </td> -->
            <td style="width: 50%; color: #34495e; font-size:16px; text-align:center">
                <span style="color: #34495e; font-size:24px; font-weight:bold"><?= NOMBRE_EMPRESA ?></span><br>
                <?= DIRECCION_EMPRESA ?><br>
                Teléfono: <?= TELEFONO_EMPRESA ?><br>
                Email: <?= EMAIL_EMPRESA ?>
            </td>
            <td style="width: 25%; text-align:right; font-size:16px;">
                FACTURA Nº <?= $numero ?>
            </td>
        </tr>
    </table><br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width:100%;" class='midnight-blue'>RAZON SOCIAL</td>
        </tr>
        <tr>
            <td style="width:50%;">
                <?php
                $cadenaRazonSocial = "";
                $cadenaRazonSocial .=
                    "
                <pre><b>CLIENTE: </b>" .
                    $estudiante["razonsocial"] .
                    "<br><b>CI / RUC: </b>" .
                    $estudiante["cin"] .
                    "<br><b>DOMICILIO: </b>" .
                    $estudiante["ciudad"] .
                    "</pre>";
                echo $cadenaRazonSocial;
                ?>
            </td>
        </tr>
    </table><br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width:35%;" class='midnight-blue'>USUARIO</td>
            <td style="width:25%;" class='midnight-blue'>FECHA</td>
            <td style="width:40%;" class='midnight-blue'>COMPROBANTE</td>
        </tr>
        <tr>
            <td style="width:35%;"><?= $_SESSION["usuario"] ?></td>
            <?php $fecha = date("d/m/Y", strtotime($fecha)); ?>
            <td style="width:25%;"><?= $fecha ?></td>
            <td style="width:40%;">Sin Valor Fiscal</td>
        </tr>
    </table><br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt; font-family:freeserif;">
        <tr>
            <th style="width: 10%; text-align:center" class='midnight-blue'>CANT.</th>
            <th style="width: 60%" class='midnight-blue'>DESCRIPCION</th>
            <th style="width: 15%; text-align: right" class='midnight-blue'>UNITARIO</th>
            <th style="width: 15%; text-align: right" class='midnight-blue'>SUBTOTAL</th>
        </tr>

        <?php
        $nums = 1;
        $sumador_total = 0;
        $impuesto = 11;
        $arrDetalles = $JSONdetalle->getDetalles($sesion);

        foreach ($arrDetalles as $fila) {

            $idCurso = $fila["idConcepto"];
            $cantidad = $fila["cantidad"];
            $concepto = $fila["concepto"];
            $importe = $precio_unitario = $fila["unitario"];

            $insertdetail = $object->insertdetail(
                $numero,
                $idCurso,
                $cantidad,
                $importe
            );

            $precio_unitario_f = number_format($precio_unitario, 0);
            $precio_unitario_r = str_replace(",", "", $precio_unitario_f);
            $precio_total = $precio_unitario_r * $cantidad;
            $precio_total_f = number_format($precio_total, 0);
            $precio_total_r = str_replace(",", "", $precio_total_f);
            $sumador_total += $precio_total_r;

            $nums % 2 == 0 ? ($clase = "clouds") : ($clase = "silver");
            ?>
            <tr>
                <td class='<?= $clase ?>' style="width: 10%; text-align: center"><?= $cantidad ?></td>
                <td class='<?= $clase ?>' style="width: 60%; text-align: left"><?= $concepto ?></td>
                <td class='<?= $clase ?>' style="width: 15%; text-align: right"><?= $precio_unitario_f ?></td>
                <td class='<?= $clase ?>' style="width: 15%; text-align: right"><?= $precio_total_f ?></td>
            </tr>
            <?php $nums++;
        }

        $subtotal = number_format($sumador_total, 0, ".", "");
        $total_iva = $subtotal / $impuesto;
        $total_iva = number_format($total_iva, 0, ".", "");
        $total_factura = $subtotal;
        ?>
    </table><br><br><br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 12pt; font-family:freeserif;">
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">SUBTOTAL &#x20B2;</td>
            <td style="width: 15%; text-align: right;"><?= number_format(
                $subtotal,
                0
            ) ?></td>
        </tr>
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">IVA 10%</td>
            <td style="width: 15%; text-align: right;"><?= number_format(
                $total_iva,
                0
            ) ?></td>
        </tr>
        <tr>
            <td colspan="3" style="width: 85%; text-align: right;">TOTAL &#x20B2;</td>
            <td style="width: 15%; text-align: right;"><?= number_format(
                $total_factura,
                0
            ) ?></td>
        </tr>
    </table><br><br><br>

    <div style="font-size:11pt; text-align:center; font-weight:bold">Factura Sin Valor Fiscal</div>
</page>

<?php $JSONdetalle->deleteAllDetalles($sesion);
?>
