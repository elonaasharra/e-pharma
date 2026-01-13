<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "epharma";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die(
        "Error: Unable to connect to MySQL. " .
        "Errno: " . mysqli_connect_errno() . " | " .
        "Error: " . mysqli_connect_error()
    );
}
