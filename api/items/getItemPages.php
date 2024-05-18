<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../db_connection.php');

$sql_total = "SELECT COUNT(*) AS total FROM productos";
$resultado_total = $conn->query($sql_total);
$total_productos = $resultado_total->fetch_assoc()['total'];


$paginas_totales = ceil($total_productos / 6); 

echo json_encode(array("paginas_totales" => $paginas_totales));

$conn->close();

