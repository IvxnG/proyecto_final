<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}
require '../../../vendor/autoload.php';
require_once('../../db_connection.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Obtener el token del encabezado de la solicitud
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(array("mensaje" => "Token no proporcionado"));
    exit();
}

$authHeader = $headers['Authorization'];
$jwt = str_replace('Bearer ', '', $authHeader);

$data = json_decode(file_get_contents("php://input"), true);
try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($data['id_producto'])) {
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
    
            $sql = "UPDATE productos SET active = false WHERE id_producto = $id_producto";
    
            if ($conn->query($sql) === TRUE) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Producto desactivado correctamente."));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al desactivar el producto: " . $conn->error));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "El ID del producto no se proporcionó o es inválido."));
        }
    }
}catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("mensaje" => "Token inválido", "error" => $e->getMessage()));
    exit();
}


$conn->close();

