<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("location: ./view/usuario/login.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once "./header.php"; ?>
    <title>Estudiantes</title>
</head>
<body>
    <?php
    require_once "view/navbar/navbar.php";
    require_once "view/carousel/carousel.php";
    ?>
</body>
<?php include_once "./footer.php"; ?>
</html
