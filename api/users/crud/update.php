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
        if (isset($data['id_usuario']) && (isset($data['nombre_completo']) || isset($data['nombre_usuario']) || isset($data['email']) || isset($data['contrasena']))) {
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
            
            $id_usuario = $conn->real_escape_string($data['id_usuario']);
            $nombre_completo = isset($data['nombre_completo']) ? $conn->real_escape_string($data['nombre_completo']) : null;
            $nombre_usuario = isset($data['nombre_usuario']) ? $conn->real_escape_string($data['nombre_usuario']) : null;
            $email = isset($data['email']) ? $conn->real_escape_string($data['email']) : null;
            $contrasena = isset($data['contrasena']) ? $conn->real_escape_string($data['contrasena']) : null;

            // Si se proporciona un nuevo nombre de usuario, verificar su disponibilidad
            if ($nombre_usuario !== null) {
                $sql_check_username = "SELECT id FROM usuarios WHERE nombre_usuario = '$nombre_usuario' AND id != '$id_usuario'";
                $result_check_username = $conn->query($sql_check_username);
                if ($result_check_username->num_rows > 0) {
                    http_response_code(400);
                    echo json_encode(array("mensaje" => "El nombre de usuario ya está en uso."));
                }
            }

            $sql_update_user = "UPDATE usuarios SET";
            $update_fields = array();

            if ($nombre_completo !== null) {
                $update_fields[] = "nombre_completo = '$nombre_completo'";
            }
            if ($nombre_usuario !== null) {
                $update_fields[] = "nombre_usuario = '$nombre_usuario'";
            }
            if ($email !== null) {
                $update_fields[] = "email = '$email'";
            }
            if ($contrasena !== null) {
                $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $update_fields[] = "contrasena = '$contrasena_hash'";
            }

            $sql_update_user .= " " . implode(", ", $update_fields) . " WHERE id = '$id_usuario'";

            if ($conn->query($sql_update_user) === TRUE) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Datos del usuario actualizados correctamente."));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al actualizar los datos del usuario: " . $conn->error));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Por favor, proporcione el ID del usuario y al menos uno de los campos para modificar."));
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
