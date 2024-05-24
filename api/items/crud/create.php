<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');
//añadido
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require '../../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once('../../db_connection.php');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el token del encabezado de la solicitud
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(array("mensaje" => "Token no proporcionado"));
    exit();
}

$authHeader = $headers['Authorization'];
$jwt = str_replace('Bearer ', '', $authHeader);

try {
    $key = 'ARAMCO33';
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    $decoded_array = (array) $decoded;

    // Verificar si el token está expirado
    if ($decoded_array['exp'] < time()) {
        http_response_code(401);
        echo json_encode(array("mensaje" => "Token expirado"));
        exit();
    }

    // Verificar los datos del usuario en el token
    $id_usuario_token = $decoded_array['data']->id;

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
        echo json_encode(array("mensaje" => "Producto creado correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al crear el producto: " . $conn->error));
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("mensaje" => "Token inválido", "error" => $e->getMessage()));
    exit();
}

$conn->close();
