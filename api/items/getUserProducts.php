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
require_once('../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se proporcionó el ID del usuario
if (!empty($data['id_usuario'])) {

    $id_usuario = $data['id_usuario'];

    $sql = "SELECT * FROM productos WHERE id_usuario = '$id_usuario' AND active=true ";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {

        $productos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        http_response_code(200);
        echo json_encode($productos);
    } else {
        http_response_code(201);
        echo json_encode(array("mensaje" => "No se encontraron productos para el usuario con ID $id_usuario."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Por favor, proporcione el ID del usuario."));
}

$conn->close();

