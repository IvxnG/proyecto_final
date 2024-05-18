<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../../db_connection.php');

$idUsuario = $_GET['idUsuario'];

$sql = "SELECT * FROM usuarios WHERE id = '$idUsuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(array("existe" => true));
} else {
    echo json_encode(array("existe" => false));
}

$conn->close();

