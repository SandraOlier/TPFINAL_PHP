<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With");



require 'db.php';
require 'Peliculas.php';


if (!isset($_SERVER['REQUEST_METHOD'])) {
    echo json_encode(["message" => "No es una solicitud HTTP"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    case 'PUT':
        handlePutRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no soportado"]);
        break;
}


function handleGetRequest() {
    global $conn;

    // Verifica si se ha pasado el parámetro 'id'
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM peliculas WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Película no encontrada"]);
        }
    } else {
        $sql = "SELECT * FROM peliculas";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function handlePostRequest() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $pelicula = Pelicula::fromArray($data);

    $sql = "INSERT INTO peliculas (titulo, genero, fecha_lanzamiento, duracion, director, reparto, sinopsis) 
            VALUES (:titulo, :genero, :fecha_lanzamiento, :duracion, :director, :reparto, :sinopsis)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':titulo' => $pelicula->titulo,
        ':genero' => $pelicula->genero,
        ':fecha_lanzamiento' => $pelicula->fecha_lanzamiento,
        ':duracion' => $pelicula->duracion,
        ':director' => $pelicula->director,
        ':reparto' => $pelicula->reparto,
        ':sinopsis' => $pelicula->sinopsis
    ]);

    $pelicula->id = $conn->lastInsertId();
    echo json_encode($pelicula->toArray());
}

function handlePutRequest() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $pelicula = Pelicula::fromArray($data);

    if (!$pelicula->id) {
        http_response_code(400);
        echo json_encode(["message" => "ID requerido para actualizar"]);
        return;
    }

    $sql = "UPDATE peliculas SET 
                titulo = :titulo,
                genero = :genero,
                fecha_lanzamiento = :fecha_lanzamiento,
                duracion = :duracion,
                director = :director,
                reparto = :reparto,
                sinopsis = :sinopsis
            WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':titulo' => $pelicula->titulo,
        ':genero' => $pelicula->genero,
        ':fecha_lanzamiento' => $pelicula->fecha_lanzamiento,
        ':duracion' => $pelicula->duracion,
        ':director' => $pelicula->director,
        ':reparto' => $pelicula->reparto,
        ':sinopsis' => $pelicula->sinopsis,
        ':id' => $pelicula->id
    ]);

    echo json_encode($pelicula->toArray());
}

function handleDeleteRequest() {
    global $conn;
    
    // Verifica si el ID está en la URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $id = intval($data['id']);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID requerido para eliminar"]);
            return;
        }
    }

    $sql = "DELETE FROM peliculas WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(["message" => "Película eliminada con éxito"]);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Película no encontrada"]);
    }
}

?>
