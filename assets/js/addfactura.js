$(document).ready(function () {
    load(1);

    $("#addFactura").submit(function (e) {
        e.preventDefault();

        var idEstudiante = $("#idEstudiante").val();
        var idFormaPago = $("#idFormaPago").val();
        var fecha = $("#fecha").val();

        if (idEstudiante < 1) {
            alert("Debes seleccionar un estudiante!");
            $("#idEstudiante").focus();
            return false;
        }

        if (fecha.length < 0) {
            alert("Seleccionar fecha válida!");
            $("#fecha").focus();
            return false;
        }

        if (idFormaPago < 1) {
            alert("Debes seleccionar una forma de pago!");
            $("#idFormaPago").focus();
            return false;
        }

        WindowCenter(
            "pdf/factura_pdf.php?fecha=" +
                fecha +
                "&idEstudiante=" +
                idEstudiante +
                "&idFormaPago=" +
                idFormaPago,
            "Vista Previa de Factura",
            "",
            "1024",
            "768",
            "true",
        );

        //recargar pagina
        window.location.href = "index.php";
    });
});

function load(page) {
    $("#loader").fadeIn("slow");
    var parametros = {
        action: "ajax",
        page: page,
    };

    $.ajax({
        url: "searchModal.php",
        data: parametros,
        beforeSend: function (objeto) {
            $("#loader").html(
                '<img width="10%" heigth="10%" src="../../assets/images/loading.gif"> Cargando...',
            );
        },
        success: function (data) {
            $("#outer_div").html(data).fadeIn("slow");
            $("#loader").html("");
        },
    });
}

function agregar(id) {
    var importe = document.getElementById("importe_" + id).value;
    var cantidad = document.getElementById("cantidad_" + id).value;
    var concepto = document.getElementById("concepto_" + id).value;

    if (isNaN(importe)) {
        alert("Esto no es numero!");
        document.getElementById("importe_" + id).focus();
        return false;
    }

    if (isNaN(cantidad)) {
        alert("Esto no es numero!");
        document.getElementById("cantidad_" + id).focus();
        return false;
    }

    var parametros = {
        idConcepto: id,
        concepto: concepto,
        unitario: importe,
        cantidad: cantidad,
    };

    $.ajax({
        type: "POST",
        url: "detalle/storeupdate.php",
        data: parametros,
        beforeSend: function (objeto) {
            $("#resultados").html("Cargando...");
        },
        success: function (data) {
            $("#resultados").empty();
            $("#resultados").html(data);
        },
    });
}

function eliminar(id) {
    Swal.fire({
        title: "Eliminar este detalle?",
        text: "¡No podrás revertir esto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#E20202",
        cancelButtonColor: "#00990F",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "GET",
                url: "detalle/storeupdate.php",
                data: { id: id },
                beforeSend: function (objeto) {
                    $("#resultados").html("Cargando...");
                },
                success: function (data) {
                    $("#resultados").html(data);
                },
            });
        }
    });
}
