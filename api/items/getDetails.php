<?php
require_once('../db_connection.php');
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id_producto'])) {
    $id_producto = intval($data['id_producto']);

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
?>
