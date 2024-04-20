<?php
require_once('../../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id'])) {
    $id_usuario = intval($data['id']);

    $conn->begin_transaction();

    $sql_eliminar_productos = "DELETE FROM productos WHERE id_usuario = $id_usuario";

    $sql_eliminar_usuario = "DELETE FROM usuarios WHERE id = $id_usuario";

    if ($conn->query($sql_eliminar_productos) === TRUE && $conn->query($sql_eliminar_usuario) === TRUE) {
        $conn->commit();
        
        http_response_code(200);
        echo json_encode(array("mensaje" => "Usuario y productos asociados eliminados correctamente."));
    } else {
        $conn->rollback();

        http_response_code(500);
        echo json_encode(array("mensaje" => "Error al eliminar el usuario y los productos asociados: " . $conn->error));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Por favor, proporcione el ID del usuario a borrar."));
}

$conn->close();
?>
