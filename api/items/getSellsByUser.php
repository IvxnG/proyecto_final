<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../db_connection.php');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

try {
    // Obtener el id_usuario de los parámetros de la URL
    $id_usuario = $_GET['id_usuario'] ?? null;

    if (!$id_usuario) {
        http_response_code(400);
        echo json_encode(array("mensaje" => "ID de usuario no proporcionado"));
        exit();
    }

    // Consultar las ventas del usuario en la base de datos
    $sql = "SELECT id_producto FROM sells WHERE id_usuario = '$id_usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $ventas = array();
        while($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        http_response_code(200);
        echo json_encode($ventas);
    } else {
        http_response_code(404);
        echo json_encode(array("mensaje" => "No se encontraron ventas para este usuario"));
    }
} catch (Exception $e) {
    // Debug: Mostrar el mensaje de error
    error_log("Error: " . $e->getMessage());

    http_response_code(401);
    echo json_encode(array("mensaje" => "Token inválido o error en el procesamiento", "error" => $e->getMessage()));
    exit();
}

$conn->close();

