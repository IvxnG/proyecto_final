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
     // Verificar si el token está expirado
     if ($decoded_array['exp'] < time()) {
        http_response_code(401);
        echo json_encode(array("mensaje" => "Token expirado"));
        exit();
    }
    $data = json_decode(file_get_contents("php://input"), true);

    $key = 'ARAMCO33';
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    $decoded_array = (array) $decoded;
    $id_usuario_token = $decoded_array['data']->id;
    $idProducto = $data['id_producto'];
    $motivo = $data['motivo'];

    $sql = "INSERT INTO reportes (id_producto, motivo) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("is", $idProducto, $motivo);
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "El producto ha sido reportado correctamente."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Error en el reporte"));
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Error en el reporte"));
    }
}

$conn->close();
