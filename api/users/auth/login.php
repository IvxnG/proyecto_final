<?php
//Añadir las cabeceras CORS al inicio del archivo PHP
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitud OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require '../../../vendor/autoload.php';
require_once('../../db_connection.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    if (isset($data['nombre_usuario']) && isset($data['contrasena'])) {
        $nombre_usuario = $conn->real_escape_string($data['nombre_usuario']);
        $contrasena = $conn->real_escape_string($data['contrasena']);
        
        $sql_get_user = "SELECT id, contrasena FROM usuarios WHERE nombre_usuario = '$nombre_usuario'";
        $result_get_user = $conn->query($sql_get_user);
        
        if ($result_get_user->num_rows == 1) {
            $row = $result_get_user->fetch_assoc();
            if (password_verify($contrasena, $row['contrasena'])) {
            
                $key = 'ARAMCO33'; 
                $issuedAt = time();
                $expirationTime = $issuedAt + 7200; 
                $payload = [
                    'iss' => 'https://easymarketivan.000webhostapp.com/',
                    'aud' => 'https://easymarketivan.000webhostapp.com/',
                    'iat' => $issuedAt, 
                    'exp' => $expirationTime, 
                    'data' => [
                        'id' => $row['id'],
                        'nombre_usuario' => $nombre_usuario
                    ]
                ];
                $jwt = JWT::encode($payload, $key, 'HS256');

                http_response_code(200);
                echo json_encode(array("token" => $jwt, "expiracion" => $expirationTime, "id_usuario" => $row['id']));
            } else {
                http_response_code(401);
                echo json_encode(array("mensaje" => "Credenciales incorrectas."));
            }
            
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "Usuario no encontrado."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "Por favor, proporcione nombre de usuario y contraseña."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "No se enviaron datos."));
}

$conn->close();



