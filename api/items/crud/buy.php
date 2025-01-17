<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}
require_once('../../db_connection.php');
require '../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el token del encabezado de la solicitud
$headers = getallheaders();

// if (!isset($headers['Authorization'])) {
//     http_response_code(401);
//     echo json_encode(array("mensaje" => "Token no proporcionado"));
//     exit();
// }

$jwt = str_replace('Bearer ', '', $headers['Authorization']);

$data = json_decode(file_get_contents("php://input"), true);

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

    $id_producto = intval($data['id_producto']);
    $id_usuario_comprador = intval($data['id_usuario']);

    $sql1 = "INSERT INTO sells (id_producto, id_usuario) VALUES (?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param('ii', $id_producto, $id_usuario_comprador);

    $sql2 = "UPDATE productos SET active = false WHERE id_producto = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param('i', $id_producto);

    if ($stmt1->execute() && $stmt2->execute()) {
        http_response_code(200);
        echo json_encode(array("mensaje" => "Producto desactivado correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al desactivar el producto: " . $conn->error));
    }

    $stmt1->close();
    $stmt2->close();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Error procesando la solicitud", "error" => $e->getMessage()));
    exit();
}

$conn->close();
