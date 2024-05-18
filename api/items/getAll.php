<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}
require_once('../db_connection.php');

$lote = isset($_GET['lote']) ? intval($_GET['lote']) : 1;
$offset = max(0, ($lote - 1) * 6);


$sql = "SELECT * FROM productos WHERE active = true"; 
$condiciones = array();

if (isset($_GET['categoria'])) {
    $categoria = $conn->real_escape_string($_GET['categoria']);
    $condiciones[] = "categoria = '$categoria'";
}

if (isset($_GET['precio_max'])) {
    $precio_max = floatval($_GET['precio_max']);
    $condiciones[] = "precio <= $precio_max";
}

if (isset($_GET['precio_min'])) {
    $precio_min = floatval($_GET['precio_min']);
    $condiciones[] = "precio >= $precio_min";
}

if (isset($_GET['estado'])) {
    $estado = $conn->real_escape_string($_GET['estado']);
    $condiciones[] = "estado = '$estado'";
}

if (!empty($condiciones)) {
    $sql .= " AND " . implode(" AND ", $condiciones);
}

$sql .= " LIMIT $offset, 6";

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
    echo json_encode(array("mensaje" => "No se encontraron productos que coincidan con los filtros proporcionados."));
}

$conn->close();
