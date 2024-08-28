<?php
require 'db.php';
header('Content-Type: application/json');

// Obtener el método HTTP y la ruta
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Manejar las diferentes rutas
switch ($path) {
    case '/contactos':
        if ($method == 'GET') {
            listarContactos($pdo);
        } elseif ($method == 'POST') {
            crearContacto($pdo);
        }
        break;
    case '/contactos/buscar':
        if ($method == 'GET') {
            buscarContactos($pdo);
        }
        break;
    case '/contactos/update':
        if ($method == 'PUT') {
            actualizarContacto($pdo);
        }
        break;
    case '/contactos/delete':
        if ($method == 'DELETE') {
            eliminarContacto($pdo);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
        break;
}

// Funciones CRUD para MySQL

function listarContactos($pdo) {
    $stmt = $pdo->query("SELECT * FROM contactos");
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($contactos);
}

function crearContacto($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, apellido, edad, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['nombre'], $data['apellido'], $data['edad'], $data['email']]);
    echo json_encode(["message" => "Contacto creado exitosamente"]);
}

function buscarContactos($pdo) {
    $query = $_GET['q'];
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ?");
    $stmt->execute(["%$query%", "%$query%", "%$query%"]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultados);
}

function actualizarContacto($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE contactos SET nombre = ?, apellido = ?, edad = ?, email = ? WHERE id = ?");
    $stmt->execute([$data['nombre'], $data['apellido'], $data['edad'], $data['email'], $data['id']]);
    echo json_encode(["message" => "Contacto actualizado exitosamente"]);
}

function eliminarContacto($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(["message" => "Contacto eliminado exitosamente"]);
}
?>
