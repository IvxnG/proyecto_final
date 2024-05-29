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
    if ($header['authorization'])
        $jwt = str_replace('Bearer ', '', $header['authorization']);

    $data = json_decode(file_get_contents("php://input"), true);

    $key = 'ARAMCO33';
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    $id_producto = intval($data['id_producto']);
    $id_usuario_comprador = intval($data['id_usuario']);

    $sql1 = "INSERT INTO sells (id_producto, id_usuario) VALUES ($id_producto, $id_usuario_comprador)";
    $sql2 = "UPDATE productos SET active = false WHERE id_producto = $id_producto";

    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
        http_response_code(200);
        echo json_encode(array("mensaje" => $header, "jwt" => $decoded));
        exit();
    } else {
        if ($conn->query($sql1) !== TRUE) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Error sql1", "error" => $e->getMessage()));
            exit();
        }
        if ($conn->query($sql2) !== TRUE) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Error sql2", "error" => $e->getMessage()));
            exit();
        }
    }

    http_response_code(200);
    echo json_encode(array("mensaje" => $header, "jwt" => $jwt));
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => $headers));
    exit();
}


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->close();
