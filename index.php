<?php
session_start();

if (isset($_SESSION["usuario"])) {
    header("location: ./main.php");
} else {
    header("location: ./view/usuario/login.php");
}
?>
