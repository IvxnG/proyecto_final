<?php
require_once('../../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    if (isset($data['nombre']) && isset($data['descripcion']) && isset($data['precio']) && isset($data['estado']) && isset($data['categoria']) && isset($data['id_usuario']) && isset($_FILES['imagen'])) {
        $nombre = $conn->real_escape_string($data['nombre']);
        $descripcion = $conn->real_escape_string($data['descripcion']);
        $precio = $conn->real_escape_string($data['precio']);
        $estado = $conn->real_escape_string($data['estado']);
        $categoria = $conn->real_escape_string($data['categoria']);
        $id_usuario = $conn->real_escape_string($data['id_usuario']);
        $ubicacion = isset($data['ubicacion']) ? $conn->real_escape_string($data['ubicacion']) : null;
        
        // Procesar la imagen
        $imagen = $_FILES['imagen'];
        $nombreImagen = uniqid() . '_' . $imagen['name'];
        $rutaImagen = '../../assets/images/' . $nombreImagen;
        
        // Mover la imagen al directorio de imágenes
        if (move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
            // Insertar la ruta de la imagen en la base de datos
            $sql_insert_product = "INSERT INTO productos (nombre, descripcion, precio, estado, categoria, id_usuario, ubicacion, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert_product);
            $stmt->bind_param("sssssssb", $nombre, $descripcion, $precio, $estado, $categoria, $id_usuario, $ubicacion, $nombreImagen);
            
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
    http_response_code(400);
    echo json_encode(array("mensaje" => "No se enviaron datos."));
}

$conn->close();
?>
