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
require_once('../../db_connection.php');

$idUsuario = $_GET['idUsuario'];

$sql = "SELECT * FROM usuarios WHERE id = '$idUsuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    echo json_encode($userData);
} else {
    echo json_encode(array("error" => "Usuario no encontrado"));
}

$conn->close();
