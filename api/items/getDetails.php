<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../db_connection.php');

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    $sql = "SELECT * FROM productos WHERE id_producto = $id_producto";

    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        http_response_code(200);
        echo json_encode($producto);
    } else {
        http_response_code(404);
        echo json_encode(array("mensaje" => "Producto no encontrado."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Por favor, proporcione el ID del producto."));
}

$conn->close();
