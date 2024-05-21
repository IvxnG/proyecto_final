<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../../db_connection.php');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (
    (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'http://127.0.0.1:5500/') === false) &&
    (!isset($_SERVER['HTTP_ORIGIN']) || strpos($_SERVER['HTTP_ORIGIN'], 'http://127.0.0.1:5500/') === false)
) {
    http_response_code(403);
    echo json_encode(array("mensaje" => "Acceso no autorizado."));
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['id_producto']) && isset($data['motivo'])){
    $idProducto = $data['id_producto'];
    $motivo = $data['motivo'];

    $sql = "INSERT INTO reportes (id_producto, motivo) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("is", $idProducto, $motivo);
        if ($stmt->execute()) {
            $response = ["success" => true, "message" => "El producto ha sido reportado correctamente."];
            echo json_encode($response);
        } else {
            $response = ["success" => false, "message" => "Error al reportar el producto."];
            echo json_encode($response);
        }
        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "Error al preparar la consulta."];
        echo json_encode($response);
    }
} else {
    $response = ["success" => false, "message" => "Datos incompletos o incorrectos recibidos."];
    echo json_encode($response);
}

$conn->close();
