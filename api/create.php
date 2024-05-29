<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With,Content-Type, Authorization");
header("Allow: GET, POST, PUT, DELETE, OPTIONS");

require '../../db_connection.php';
require_once('../../../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $header = apache_request_headers();
    if ($header['authorization']){
        $jwt = str_replace('Bearer ', '', $header['authorization']);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $key = 'ARAMCO33';
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $estado = $_POST['estado'];
    $categoria = $_POST['categoria'];
    $ubicacion = $_POST['ubicacion'];

    // Recibir imagen y convertirla en base64
    $imagen_base64 = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));

    // Insertar datos en la base de datos
    $sql = "INSERT INTO productos (nombre, descripcion, precio, estado, categoria, id_usuario, ubicacion, imagen) 
            VALUES ('$nombre', '$descripcion', '$precio', '$estado', '$categoria', '$id_usuario_token', '$ubicacion', '$imagen_base64')";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200);
        echo json_encode(array("mensaje" => $header, "jwt" => $decoded));
        exit();
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "Error al crear", "error" => $e->getMessage()));
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => $headers));
}

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->close();
