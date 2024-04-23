<?php
require_once('../../db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['precio']) && isset($_POST['estado']) && isset($_POST['categoria']) && isset($_POST['id_usuario']) && isset($_FILES['imagen'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $estado = $_POST['estado'];
        $categoria = $_POST['categoria'];
        $id_usuario = $_POST['id_usuario'];
        $ubicacion = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : null;

        $nombreImagen = uniqid() . '_' . $_FILES['imagen']['name'];
        $rutaImagen = '../../../assets/images/' . $nombreImagen;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            $sql_insert_product = "INSERT INTO productos (nombre, descripcion, precio, estado, categoria, id_usuario, ubicacion, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert_product);
            $stmt->bind_param("ssssssss", $nombre, $descripcion, $precio, $estado, $categoria, $id_usuario, $ubicacion, $nombreImagen);
            // Ejecutar la consulta SQL
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(array("mensaje" => "Producto creado correctamente."));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al crear el producto: " . $conn->error));
            }
        } else {
            http_response_code(500);
            echo json_encode(array("mensaje" => "Error al subir la imagen."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "Por favor, proporcione todos los datos necesarios (nombre, descripción, precio, estado, categoría, ID de usuario y una imagen)."));
    }
} else {
    http_response_code(405); // Método no permitido
    echo json_encode(array("mensaje" => "Método no permitido."));
}

// Cerrar la conexión a la base de datos
$conn->close();

