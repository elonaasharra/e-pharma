<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "epharma_new";

$conn = mysqli_connect($host, $user, $password, $database);
//funksion i php
if (!$conn) {
    die(
        "Error: Unable to connect to MySQL. " .
        "Errno: " . mysqli_connect_errno() . " | " .
        "Error: " . mysqli_connect_error()
    );
}
//errno-> kodin e gabimit nese db deshton
//error->mesazhi  e gabimit te tekstit