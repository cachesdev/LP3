<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
session_start();
ob_start();

// Company constants
const NOMBRE_EMPRESA = "LPTRES 2024";
const DIRECCION_EMPRESA = "CAAGUAZU (colectora sur) Km 180";
const TELEFONO_EMPRESA = "0522 44444";
const EMAIL_EMPRESA = "lptres@gmail.com";

// Get REQUEST variables
$fecha = $_REQUEST["fecha"];
$fecha = date("Y-d-m", strtotime($fecha));
$idEstudiante = $_REQUEST["idEstudiante"];
$idFormaPago = $_REQUEST["idFormaPago"];
$idUsuario = $_SESSION["idUsuario"];

// Database operations - FACTURAS table
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";
require_once CONTROLLER_PATH . "facturaController.php";

$object = new facturaController();
$estudiante = $object->listestudiantes($idEstudiante);
$numero = $object->insert($fecha, $idEstudiante, $idFormaPago, $idUsuario);

// Database operations - JSON auxiliary table
require_once "../detalle/insert.php";

$JSONdetalle = new detalleFactura();
$sesion = $_SESSION["usuario"];
$arrDetalles = $JSONdetalle->getDetalles($sesion);
$count = 0;

foreach ($arrDetalles as $detalle) {
    $count++;
}

if ($count == 0) {
    echo "<script>alert('No hay articulos agregados a la factura')</script>";
    echo "<script>window.close();</script>";
    exit();
}

require_once ROOT_PATH . "vendor/autoload.php";
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

// Get the HTML/PHP
include "doc/factura_html.php";
$content = ob_get_clean();

// HTML2PDF library implementation
try {
    $html2pdf = new Html2Pdf("P", "A4", "es", true, "UTF-8", [0, 0, 0, 0]);

    $html2pdf->pdf->setDisplayMode("real");

    $html2pdf->writeHTML($content, isset($_GET["vuehtml"]));

    $html2pdf->output(
        "factura_" . $sesion . "_" . $_COOKIE["PHPSESSID"] . ".pdf"
    );
} catch (Html2PdfException $e) {
    echo $e;
    exit();
}
?>
