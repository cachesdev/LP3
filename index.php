<!doctype html>
<html lang="es">

<head>
    <title>Mini Sistema de Facturación</title>
    <meta charset="utf-8">

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/datatables.min.css" rel="stylesheet" />

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/datatables.min.js"></script>
</head>

<body>
    <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">CRUD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <class="nav-item">
                        <a class="nav-link active" aria-current="page"
                            href="/php/facturas/facturas.php">Facturación</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="php/categorias/">Categorías</a></li>
                                <li><a class="dropdown-item" href="php/clientes/">Clientes</a></li>
                                <li><a class="dropdown-item" href="php/productos/">Productos</a></li>
                            </ul>
                        </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
        require("php/conexion.php");
        $con = retornarConexion();
        $consulta = mysqli_query(
            $con,
            "select
            fact.codigo as codigo,
            date_format(fecha,'%d/%m/%Y') as fecha,
            nombre,
            round(sum(deta.precio*deta.cantidad),2) as importefactura
            from facturas as fact
            join clientes as cli on cli.codigo=fact.codigocliente
            join detallefactura as deta on deta.codigofactura=fact.codigo
            group by deta.codigofactura
            order by codigo desc"
        ) or die(mysqli_error($con));

        $filas = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
        ?>
        <h1>Facturas emitidas</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th class="text-right">Importe</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($filas as $fila) {
                    ?>
                    <tr>
                        <td><?php echo $fila['codigo'] ?></td>
                        <td><?php echo $fila['nombre'] ?></td>
                        <td><?php echo $fila['fecha'] ?></td>
                        <td class="text-right"><?php echo '$' . number_format($fila['importefactura'], 2, ',', '.'); ?></td>
                        <td class="text-right">
                            <a class="btn btn-primary btn-sm botonimprimir" role="button"
                                href="php/facturas/facturapdf.php?codigofactura=<?php echo $fila['codigo']; ?>"
                                target="_blank" data-codigo="<?php echo $fila['codigo']; ?>">Imprime?</a>
                            <a class="btn btn-danger btn-sm botonborrar" role="button"
                                href="php/facturas/borrarfactura.php?codigofactura=<?php echo $fila['codigo']; ?>"
                                data-codigo="<?php echo $fila['codigo']; ?>">Borra?</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <button type="button" id="btnNuevaFactura" class="btn btn-success"
            onclick="window.location.href='/php/facturas/facturas.php'">Emitir factura</button>
        <!--<button type="button" id="btnNuevaFactura" class="btn btn-success">Emitir factura</button> -->
    </div>

    <!-- ModalConfirmarBorrar -->
    <div class="modal fade" id="ModalConfirmarBorrar" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="max-width: 600px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h1>¿Realmente quiere borrar la factura?</h1>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmarBorrado" class="btn btn-success">Confirmar</button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-success">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            $('#btnNuevaFactura').click(function () {
                window.location = '/php/facturas/facturas.php';
            });

            var codigofactura;

            $('.botonborrar').click(function (event) {
                event.preventDefault();
                codigofactura = $(this).data('codigo');
                $('#ModalConfirmarBorrar').modal('show'); // Show the modal
            });

            $('#btnConfirmarBorrado').click(function () {
                window.location.href = 'php/facturas/borrarfactura.php?codigofactura=' + codigofactura;
            });


            $('.botonimprimir').click(function (event) {
                event.preventDefault();
                window.open('php/facturas/facturapdf.php?codigofactura=' + $(this).data('codigo'), '_blank');
            });

        });
    </script>

</body>

</html>