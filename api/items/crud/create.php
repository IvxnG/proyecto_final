<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}
if (
    (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'http://127.0.0.1:5500/') === false) &&
    (!isset($_SERVER['HTTP_ORIGIN']) || strpos($_SERVER['HTTP_ORIGIN'], 'http://127.0.0.1:5500/') === false)
) {
    http_response_code(403);
    echo json_encode(array("mensaje" => "Acceso no autorizado."));
    exit();
}
require_once('../../db_connection.php');


// Recibir datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$estado = $_POST['estado'];
$categoria = $_POST['categoria'];
$id_usuario = $_POST['id_usuario'];
$ubicacion = $_POST['ubicacion'];

// Recibir imagen y convertirla en base64
$imagen_base64 = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));

// Insertar datos en la base de datos
$sql = "INSERT INTO productos (nombre, descripcion, precio, estado, categoria, id_usuario, ubicacion, imagen, active) 
        VALUES ('$nombre', '$descripcion', '$precio', '$estado', '$categoria', '$id_usuario', '$ubicacion', '$imagen_base64', true)";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);
    echo json_encode(array("mensaje" => "Producto creado correctamente."));
} else {
    http_response_code(500);
    echo json_encode(array("mensaje" => "Error al crear el producto: " . $conn->error));
}

$conn->close();

