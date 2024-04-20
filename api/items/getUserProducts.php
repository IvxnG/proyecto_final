<?php
require_once('../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se proporcionó el ID del usuario
if (!empty($data['id_usuario'])) {

    $id_usuario = $data['id_usuario'];

    $sql = "SELECT * FROM productos WHERE id_usuario = '$id_usuario'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {

        $productos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        http_response_code(200);
        echo json_encode($productos);
    } else {
        http_response_code(404);
        echo json_encode(array("mensaje" => "No se encontraron productos para el usuario con ID $id_usuario."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "Por favor, proporcione el ID del usuario."));
}

$conn->close();
?>
