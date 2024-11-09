<?php

function retornarConexion()
{
    $server = "localhost";
    $usuario = "root";
    $clave = "";
    $base = "mini24";

    // Connect to the database
    $con = mysqli_connect($server, $usuario, $clave, $base) or die("Connection failed");

    // Set the character encoding
    mysqli_set_charset($con, 'utf8');

    return $con;
}