<!doctype html>
<html>

<head>
    <title>Facturación</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/css/datatables.min.css" rel="stylesheet" />

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/datatables.min.js"></script>

</head>

<body>

    <?php
    # PARA DETERMINAR PROXIMO NUMERO DE FACTURA
    require("../conexion.php");
    $con = retornarConexion();
    $consulta = mysqli_query($con, "insert into facturas() values ()") or
        die(mysqli_error($con));
    $codigofactura = mysqli_insert_id($con);
    # FIN DETERMINAR PROXIMO NUMERO DE FACTURA
    ?>

    <div class="container">
        <div class="row mt-4">
            <div class="col-md">
                <div class="form-group row">
                    <label for="CodigoFactura" class="col-lg-3 col-form-label">Número de factura:
                    </label>
                    <div class="col-lg-3">
                        <input
                        type="text"
                        disabled class="form-control"
                        id="CodigoFactura"
                        value="<?php echo $codigofactura; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Fecha" class="col-lg-3 col-form-label">Fecha de emisión:</label>
                    <div class="col-lg-3">
                        <input type="date" class="form-control" id="Fecha">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="CodigoCliente" class="col-lg-3 col-form-label">Nombre de cliente:</label>
                    <div class="col-lg-3">
                        <select class="form-control" id="CodigoCliente">
                            <?php
                            # RECUPERAR LISTA DE CLIENTES
                            $consulta = mysqli_query($con, "select codigo, nombre from clientes")
                                or die(mysqli_error($con));

                            $clientes = mysqli_fetch_all($consulta, MYSQLI_ASSOC);

                            echo "<option value='0'>Seleccionar cliente</option>";
                            foreach ($clientes as $cli) {
                                echo "<option value='" . $cli['codigo'] . "'>" . $cli['nombre'] . "</option>";
                            }
                            # FIN RECUPERAR LISTA DE CLIENTES
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código de Artículo</th>
                            <th>Descripción</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio unitario</th>
                            <th class="text-right">Total</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody id="DetalleFactura"></tbody>
                </table>
                <button type="button" id="btnAgregarProducto" class="btn btn-success">Agregar Producto</button>
                <button type="button" id="btnTerminarFactura" class="btn btn-danger">Terminar Factura</button>
            </div>
        </div>
    </div>

    <!-- ModalProducto(Agregar) -->
    <div class="modal fade" id="ModalProducto" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Registro detalle de factura</h2>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Producto:</label>
                        <select class="form-control" id="CodigoProducto">
                            <?php
                            # RECUPERAR LISTA DE PRODUCTOS
                            $consulta = mysqli_query($con, "select codigo, descripcion, precio from productos")
                                or die(mysqli_error($con));

                            $productos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
                            foreach ($productos as $pro) {
                                echo "<option value='" . $pro['codigo'] . "'>" . $pro['descripcion'] . '($' . $pro['precio'] . ")</option>";
                            }
                            # FIN RECUPERAR LISTA DE PRODUCTOS
                            ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Cantidad:</label>
                            <input type="number" id="Cantidad" class="form-control" placeholder="" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmarAgregarProducto" class="btn btn-primary">Agregar a la factura</button>
                    <button type="button" id="btnCancelarAgregarProducto" class="btn btn-warning">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ModalFinFactura -->
    <div class="modal fade" id="ModalFinFactura" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="max-width: 600px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Acciones sobre factura</h2>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmarFactura" class="btn btn-success">Confirmar factura</button>
                    <button type="button" id="btnConfirmarImprimirFactura" class="btn btn-warning">Confirmar e imprimir factura</button>
                    <button type="button" id="btnConfirmarDescartarFactura" class="btn btn-danger">Descartar factura</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ModalConfirmarBorrar -->
    <div class="modal fade" id="ModalConfirmarBorrar" tabindex="-1" role="dialog">
        <div class="modal-dialog" style="max-width: 600px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>¿Realmente quiere borrarlo?</h2>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmarBorrado" class="btn btn-danger">Confirmar</button>
                    <button type="button" id="btnCancelarBorrado" class="btn btn-warning">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var producto;
            var cliente;

            document.getElementById('Fecha').valueAsDate = new Date();

            //Boton que muestra el diálogo de agregar producto
            $('#btnAgregarProducto').click(function() {
                LimpiarFormulario();
                $("#Cantidad").val("1");
                $("#ModalProducto").modal("show");
            });

            $("#btnCancelarBorrado").click(function() {
                $("#ModalConfirmarBorrar").modal("hide");
            });

            $("#btnCancelarAgregarProducto").click(function() {
                $("#ModalProducto").modal("hide");
            });

            //Boton que agrega el producto al detalle
            $('#btnConfirmarAgregarProducto').click(function() {
                RecolectarDatosFormulario();
                $("#ModalProducto").modal('hide');
                if ($("#Cantidad").val() == "") { //Controlamos que no esté vacío la cantidad de
                    productos
                    alert('No puede estar vacío la cantidad de productos.');
                    return;
                }
                EnviarInformacionProducto("agregar");
            });

            //Boton terminar factura
            $('#btnTerminarFactura').click(function() {
                $("#ModalFinFactura").modal("show");
            });

            //Boton confirmar factura
            $('#btnConfirmarFactura').click(function() {
                if ($('#CodigoCliente').val() == 0) {
                    alert('Debe seleccionar un cliente');
                    return;
                }
                RecolectarDatosCliente();
                EnviarInformacionFactura("confirmarfactura");
            });

            //Boton que descarta la factura generada borrando tanto en la tabla de facturas como detallefactura
            $('#btnConfirmarDescartarFactura').click(function() {
                RecolectarDatosCliente();
                EnviarInformacionFactura("confirmardescartarfactura");
            });

            //Boton confirmar factura y ademas genera pdf
            $('#btnConfirmarImprimirFactura').click(function() {
                if ($('#CodigoCliente').val() == 0) {
                    alert('Debe seleccionar un cliente');
                    return;
                }
                RecolectarDatosCliente();
                EnviarInformacionFacturaImprimir("confirmarfactura");
                window.open('facturapdf.php?codigofactura=' + <?php echo $codigofactura ?>, '_blank')
            });

            function RecolectarDatosFormulario() {
                producto = {
                    codigoproducto: $("#CodigoProducto").val(),
                    cantidad: $("#Cantidad").val()
                };
            }

            function RecolectarDatosCliente() {
                cliente = {
                    codigocliente: $('#CodigoCliente').val(),
                    fecha: $('#Fecha').val()
                };
            }

            //Funciones AJAX para enviar y recuperar datos del servidor
            //*******************************************************
            function EnviarInformacionProducto(accion) {
                $.ajax({
                    type: 'POST',
                    url: 'operaciones.php?accion=' + accion + '&codigofactura=' + <?php echo $codigofactura ?>,
                    data: producto,
                    success: function(msg) {
                        RecuperarDetalle();
                    },
                    error: function(request, error) {
                        console.log(arguments);
                        alert("Hay un error EnviarInformacionProducto");
                    }
                });
            }

            function EnviarInformacionFactura(accion) {
                $.ajax({
                    type: 'POST',
                    url: 'operaciones.php?accion=' + accion + '&codigofactura=' + <?php echo $codigofactura ?>,
                    data: cliente,
                    success: function(msg) {
                        window.location = '../../';
                    },
                    error: function() {
                        alert("Hay un error EnviarInformacionFactura");
                    }
                });
            }

            function EnviarInformacionFacturaImprimir(accion) {
                $.ajax({
                    type: 'POST',
                    url: 'operaciones.php?accion=' + accion + '&codigofactura=' + <?php echo $codigofactura ?>,
                    data: cliente,
                    success: function(msg) {
                        window.open('facturapdf.php?' + '&codigofactura=' + <?php echo $codigofactura ?>, '_blank');
                        window.location = '../../';
                    },
                    error: function() {
                        alert("Hay un error EnviarInformacionFacturaImprimir");
                    }
                });
            }

            function LimpiarFormulario() {
                $('#Cantidad').val('1');
            }

        });

        //Se ejecuta cuando se presiona un boton de borrar un item del detalle
        var cod;

        function borrarItem(coddetalle) {
            cod = coddetalle;
            $("#ModalConfirmarBorrar").modal("show");
        }
        $('#btnConfirmarBorrado').click(function() {
            $("#ModalConfirmarBorrar").modal('hide');
            $.ajax({
                type: 'POST',
                url: 'borrarproductodetalle.php?codigo=' + cod,
                success: function(msg) {
                    RecuperarDetalle();
                },
                error: function() {
                    alert("Hay un error borrarItem");
                }
            });
        });

        function RecuperarDetalle() {
            $.ajax({
                type: 'GET',
                url: 'recuperardetalle.php?codigofactura=' + <?php echo $codigofactura ?>,
                success: function(datos) {
                    document.getElementById('DetalleFactura').innerHTML = datos;
                },
                error: function() {
                    alert("Hay un error RecuperarDetalle");
                }

            });

        }
    </script>
</body>

</html>