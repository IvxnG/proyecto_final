<?php
require_once('../db_connection.php');

$sql_total = "SELECT COUNT(*) AS total FROM productos";
$resultado_total = $conn->query($sql_total);
$total_productos = $resultado_total->fetch_assoc()['total'];


$paginas_totales = ceil($total_productos / 10); 

echo json_encode(array("paginas_totales" => $paginas_totales));

$conn->close();
?>
