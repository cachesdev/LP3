<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/semana5/tallermvcphp/routes.php";

class detalleFactura
{
    function __construct()
    {
    }

    public function getDetalles($sesion)
    {
        return json_decode(
            file_get_contents(
                VIEW_PATH . "factura/detalle/tmpdetallefacturas$sesion.json"
            ),
            true
        );
    }

    public function getDetalleById($id, $sesion)
    {
        $detalles = $this->getDetalles($sesion);
        foreach ($detalles as $detalle) {
            if ($detalle["id"] == $id) {
                return $detalle;
            }
        }
        return null;
    }

    public function createDetalleExist($data, $sesion)
    {
        $detalles = $this->getDetalles($sesion);
        $detalles[] = $data;
        $this->putJson($detalles, $sesion);
    }

    public function createDetalleNotExist($data, $sesion)
    {
        $detalles = [];
        $detalles[] = $data;
        $this->putJson($detalles, $sesion);
    }

    public function deleteDetalle($id, $sesion)
    {
        $detalles = $this->getDetalles($sesion);
        foreach ($detalles as $i => $detalle) {
            if ($detalle["idTmpDetalle"] == $id) {
                array_splice($detalles, $i, 1);
            }
        }
        $this->putJson($detalles, $sesion);
    }

    public function deleteAllDetalles($sesion)
    {
        $detalles = [];
        $this->putJson($detalles, $sesion);
    }

    public function putJson($detalles, $sesion)
    {
        file_put_contents(
            VIEW_PATH . "factura/detalle/tmpdetallefacturas$sesion.json",
            json_encode($detalles, JSON_PRETTY_PRINT)
        );
    }
}
?>
