<?php
// Parámetros de conexión a la base de datos
$servername = "localhost";
$username = "compraventa";
$password = "compraventa";
$database = "compraventa";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
