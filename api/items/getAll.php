<?php
require_once('../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

$lote = isset($data['lote']) ? intval($data['lote']) : 1;

$offset = ($lote - 1) * 10;

$sql = "SELECT * FROM productos";

$condiciones = array();

if (isset($data['filtros'])) {
    $filtros = $data['filtros'];

    if (isset($filtros['categoria'])) {
        $categoria = $conn->real_escape_string($filtros['categoria']);
        $condiciones[] = "categoria = '$categoria'";
    }

    if (isset($filtros['precio_max'])) {
        $precio_max = floatval($filtros['precio_max']);
        $condiciones[] = "precio <= $precio_max";
    }

    if (isset($filtros['precio_min'])) {
        $precio_min = floatval($filtros['precio_min']);
        $condiciones[] = "precio >= $precio_min";
    }

    if (isset($filtros['estado'])) {
        $estado = $conn->real_escape_string($filtros['estado']);
        $condiciones[] = "estado = '$estado'";
    }
}

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " LIMIT $offset, 10";

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
?>
