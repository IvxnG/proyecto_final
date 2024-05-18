<?php
// Añadir las cabeceras CORS al inicio del archivo PHP
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitud OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once('../../db_connection.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    if (isset($data['nombre_completo']) && isset($data['nombre_usuario']) && isset($data['email']) && isset($data['contrasena'])) {
        $nombre_completo = $conn->real_escape_string($data['nombre_completo']);
        $nombre_usuario = $conn->real_escape_string($data['nombre_usuario']);
        $email = $conn->real_escape_string($data['email']);
        $contrasena = $conn->real_escape_string($data['contrasena']);
        
        // Verificar si el nombre de usuario ya está en uso
        $sql_check_username = "SELECT id FROM usuarios WHERE nombre_usuario = '$nombre_usuario'";
        $result_check_username = $conn->query($sql_check_username);
        
        if ($result_check_username->num_rows > 0) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "El nombre de usuario ya está en uso."));
        } else {
            // Generar un ID único para el usuario
            $id_uniq = generateUniqueID($conn);
            
            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
            
            $sql_insert_user = "INSERT INTO usuarios (id, nombre_completo, nombre_usuario, email, contrasena) VALUES ('$id_uniq', '$nombre_completo', '$nombre_usuario', '$email', '$hashed_password')";
            
            if ($conn->query($sql_insert_user) === TRUE) {
                http_response_code(201);
                echo json_encode(array("mensaje" => "Usuario registrado correctamente.", "id" => $id_uniq));
            } else {
                http_response_code(500);
                echo json_encode(array("mensaje" => "Error al registrar el usuario: " . $conn->error));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "Por favor, proporcione nombre completo, nombre de usuario, email y contraseña."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "No se enviaron datos."));
}

$conn->close();

function generateUniqueID($conn) {
    $id_length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id_uniq = '';
    $max_attempts = 10;
    $attempt = 0;

    do {
        $id_uniq = '';
        for ($i = 0; $i < $id_length; $i++) {
            $id_uniq .= $characters[rand(0, strlen($characters) - 1)];
        }
        $sql_check_id = "SELECT id FROM usuarios WHERE id = '$id_uniq'";
        $result_check_id = $conn->query($sql_check_id);
        $attempt++;
    } while ($result_check_id->num_rows > 0 && $attempt < $max_attempts); 

    if ($attempt == $max_attempts) {
        throw new Exception("No se pudo generar un ID único después de $max_attempts intentos.");
    }

    return $id_uniq;
}
