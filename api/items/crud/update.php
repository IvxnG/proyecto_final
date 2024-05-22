<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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

$data = json_decode(file_get_contents("php://input"), true);

try {
    if (!empty($data)) {
        if (isset($data['id_producto'])) {
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
            
            $id_producto = $conn->real_escape_string($data['id_producto']);
            $nombre = isset($data['nombre']) ? $conn->real_escape_string($data['nombre']) : null;
            $descripcion = isset($data['descripcion']) ? $conn->real_escape_string($data['descripcion']) : null;
            $precio = isset($data['precio']) ? $conn->real_escape_string($data['precio']) : null;
            $estado = isset($data['estado']) ? $conn->real_escape_string($data['estado']) : null;
            $categoria = isset($data['categoria']) ? $conn->real_escape_string($data['categoria']) : null;

            $sql_update_product = "UPDATE productos SET";
            $update_fields = array();

            if ($nombre !== null) {
                $update_fields[] = "nombre = '$nombre'";
            }
            if ($descripcion !== null) {
                $update_fields[] = "descripcion = '$descripcion'";
            }
            if ($precio !== null) {
                $update_fields[] = "precio = '$precio'";
            }
            if ($estado !== null) {
                $update_fields[] = "estado = '$estado'";
            }
            if ($categoria !== null) {
                $update_fields[] = "categoria = '$categoria'";
            }
            if ($ubicacion !== null) {
                $update_fields[] = "ubicacion = '$ubicacion'";
            }

            $sql_update_product .= " " . implode(", ", $update_fields) . " WHERE id_producto = '$id_producto'";

            if ($conn->query($sql_update_product) === TRUE) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Datos del producto actualizados correctamente."));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al actualizar los datos del producto: " . $conn->error));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Por favor, proporcione el ID del producto."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se enviaron datos."));
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array("mensaje" => "Token inválido", "error" => $e->getMessage()));
    exit();
}


$conn->close();
