$("#formLogin").submit(function (e) {
    e.preventDefault();

    var usuario = $.trim($("#usuario").val());
    var clave = $.trim($("#clave").val());

    if (usuario == "" || clave == "") {
        Swal.fire({
            type: "warning",
            title: "Ingrese un usuario y/o contraseña",
        });
        return false;
    } else {
        $.ajax({
            url: "validate.php",
            type: "post",
            datatype: "json",
            data: { usuario: usuario, clave: clave },
            success: function (data) {
                console.log(data);
                if (data == "null") {
                    Swal.fire({
                        type: "error",
                        title: "Usuario y/o clave incorrecta",
                    });
                    return false;
                } else {
                    Swal.fire({
                        type: "success",
                        title: "¡Conexión exitosa!",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ingresar",
                    }).then((result) => {
                        if (result.value) {
                            window.location.href = "/semana5/tallermvcphp/";
                        }
                    });
                }
            },
        });
    }
});
