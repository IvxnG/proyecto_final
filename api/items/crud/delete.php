<?php
require_once('../../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id_producto'])) {
    $id_producto = intval($data['id_producto']);

    $sql = "DELETE FROM productos WHERE id_producto = $id_producto";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200);
        echo json_encode(array("mensaje" => "Producto eliminado correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al eliminar el producto: " . $conn->error));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Por favor, proporcione el ID del producto a borrar."));
}

$conn->close();
?>
