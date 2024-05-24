<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}
require_once('../../db_connection.php');


$data = json_decode(file_get_contents("php://input"), true);
try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($data['id_producto'])) {

            $id_producto = intval($data['id_producto']);
            $id_usuario_comprador = intval($data['id_usuario']);

            $sql1 = "INSERT INTO sells (id_producto, id_usuario) VALUES ($id_producto, $id_usuario_comprador)";
            $conn->query($sql1);
            $sql = "UPDATE productos SET active = false WHERE id_producto = $id_producto";
    
            if ($conn->query($sql) === TRUE) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Producto desactivado correctamente."));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al desactivar el producto: " . $conn->error));
            }
        } else {
            http_response_code(200);
            echo json_encode(array("mensaje" => "El ID del producto no se proporcionó o es inválido."));
        }
    }
}catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array("mensaje" => "..", "error" => $e->getMessage()));
    exit();
}


$conn->close();

