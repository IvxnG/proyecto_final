<?php
//Añadir las cabeceras CORS al inicio del archivo PHP
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitud OPTIONS
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

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    if (isset($data['nombre_usuario']) && isset($data['contrasena'])) {
        $nombre_usuario = $conn->real_escape_string($data['nombre_usuario']);
        $contrasena = $conn->real_escape_string($data['contrasena']);
        
        $sql_get_user = "SELECT id, contrasena FROM usuarios WHERE nombre_usuario = '$nombre_usuario'";
        $result_get_user = $conn->query($sql_get_user);
        
        if ($result_get_user->num_rows == 1) {
            $row = $result_get_user->fetch_assoc();
            if (password_verify($contrasena, $row['contrasena'])) {
                //JWT
                // $key = 'ARAMCO';
                // $now = strtolower("now");
                // $payload = [
                //     'exp' => $now + 3600,
                //     'data' => $nombre_usuario,
                // ];
                // $jwt = JWT::encode($payload, $key, 'HS256');

                $token = uniqid();
                $expiracion = time() + (60 * 60); 
                $id_usuario = $row['id'];
                http_response_code(200);
                echo json_encode(array("token" => $token, "expiracion" => $expiracion, "id_usuario" => $id_usuario));
            } else {
                http_response_code(401);
                echo json_encode(array("mensaje" => "Credenciales incorrectas."));
            }
            
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "Usuario no encontrado."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "Por favor, proporcione nombre de usuario y contraseña."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("mensaje" => "No se enviaron datos."));
}

$conn->close();



