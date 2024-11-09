<?php
require('../../fpdf/fpdf.php');
require("../conexion.php");
$conexion = retornarConexion();

$fpdf = new FPDF('P', 'mm', 'letter');//, true);
$fpdf->AddPage('portrait', 'letter');
$fpdf->SetMargins(10, 30, 20);//, 20);
cabecera($fpdf, $conexion);
piedepagina($fpdf);

titulosdetalle($fpdf);
imprimirdetalle($fpdf, $conexion);

$fpdf->OutPut();

function cabecera($fpdf, $conexion)
{
    $fpdf->SetFillColor(116, 92, 151);
    $fpdf->Rect(0, 0, 220, 50, 'F');
    $fpdf->SetFont('Arial', 'B', 15);
    $fpdf->SetTextColor(255, 255, 255);
    $fpdf->Image('../../img/logo.png', 10, 1);
    $datos = mysqli_query($conexion, "select nombre,
    date_format(fecha,'%d/%m/%Y') as fecha
    from facturas as fact
    join clientes as cli on
    cli.codigo=fact.codigocliente
    where fact.codigo=$_GET[codigofactura]") or die(mysqli_error($conexion));
    $resultado = mysqli_fetch_array($datos);
    $fpdf->SetFont('Arial', 'B', 28);
    $fpdf->SetY(5);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 5, "FACTURA");
    $fpdf->SetFont('Arial', 'B', 14);
    $fpdf->SetY(20);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 5, "Cliente : " . $resultado['nombre'], 0, 0, 'L', 1);
    $fpdf->SetY(30);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 5, "Fecha de emisi" . iconv('UTF-8', 'windows-1252', 'ó') . "n : " . $resultado['fecha'], 0, 0, 'L', 1);
}

function piedepagina($fpdf)
{
    $fpdf->SetFillColor(116, 92, 151);
    $fpdf->Rect(0, 250, 220, 50, 'F');
    $fpdf->SetY(-28);
    $fpdf->SetFont('Arial', '', 12);
    $fpdf->SetTextColor(0, 0, 0);
    $fpdf->SetX(120);
    $fpdf->Write(5, 'Gracias por su compra.!');
}

function titulosdetalle($fpdf)
{
    $fpdf->SetY(60);
    $fpdf->SetTextColor(255, 255, 255);
    $fpdf->SetFillColor(79, 78, 77);
    $fpdf->Cell(30, 10, 'C' . iconv('UTF-8', 'windows-1252', 'ó') . 'digo', 0, 0, 'C', 1);
    $fpdf->Cell(70, 10, 'Descripci' . iconv('UTF-8', 'windows-1252', 'ó') . 'n', 0, 0, 'L', 1);
    $fpdf->Cell(20, 10, 'Cantidad', 0, 0, 'C', 1);
    $fpdf->Cell(40, 10, 'Precio', 0, 0, 'R', 1);
    $fpdf->Cell(30, 10, 'Total', 0, 0, 'R', 1);
}

function imprimirdetalle($fpdf, $conexion)
{
    $datos = mysqli_query($conexion, "select pro.codigo as codigo,
    descripcion,
    round(deta.precio,2) as precio,
    cantidad,
    round(deta.precio*cantidad,2) as preciototal,
    deta.codigo as coddetalle
    from detallefactura as deta
    join productos as pro on
    pro.codigo=deta.codigoproducto
    where codigofactura=$_GET[codigofactura]") or die(mysqli_error($conexion));

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    $fpdf->SetTextColor(0, 0, 0);
    $fpdf->SetFillColor(255, 255, 255);
    $fpdf->SetFont('times', '', 12);
    $fpdf->SetY(70);
    $fpdf->SetLineWidth(0.2);
    $pago = 0;
    $item = 0;

    foreach ($resultado as $fila) {
        $fpdf->Cell(30, 10, $fila['codigo'], 1, 0, 'C', 1);
        $fpdf->Cell(70, 10, $fila['descripcion'], 1, 0, 'L', 1);
        $fpdf->Cell(20, 10, $fila['cantidad'], 1, 0, 'R', 1);
        $fpdf->Cell(40, 10, number_format($fila['precio'], 0, ',', '.'), 1, 0, 'R', 1);
        $fpdf->Cell(30, 10, number_format($fila['preciototal'], 0, ',', '.'), 1, 0, 'R', 1);
        $fpdf->Ln();
        $pago = $pago + $fila['preciototal'];
        $item++;
        if ($item == 16) {
            $fpdf->AddPage('portrait', 'letter');
            $fpdf->SetMargins(10, 30, 20, 20);
            cabecera($fpdf, $conexion);
            piedepagina($fpdf);
            titulosdetalle($fpdf);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->SetFillColor(255, 255, 255);
            $fpdf->SetFont('Arial', '', 12);
            $fpdf->SetY(70);
            $fpdf->SetLineWidth(0.2);
            $item = 0;
        }
    }
    $fpdf->SetFont('Arial', 'B', 15);
    $fpdf->Cell(190, 20, "Importe Total : G. " . number_format($pago, 0, ',', '.'), 1, 0, 'R', 1);
}
