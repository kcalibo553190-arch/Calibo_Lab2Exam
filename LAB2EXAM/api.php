<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "laravel_crud";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($db);
$conn->query("CREATE TABLE IF NOT EXISTS `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        echo json_encode($item ?: []);
        exit;
    }

    $result = $conn->query("SELECT * FROM items ORDER BY id DESC");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        $data = $_POST;
    }

    $name  = $data['name'] ?? '';
    $desc  = $data['description'] ?? '';
    $price = floatval($data['price'] ?? 0);

    if (empty($name)) {
        http_response_code(422);
        echo json_encode(["error" => "Name is required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $desc, $price);
    $stmt->execute();
    $newId = $conn->insert_id;
    $stmt->close();

    echo json_encode(["success" => true, "id" => $newId, "message" => "Item saved directly to XAMPP database!"]);
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id    = intval($data['id'] ?? 0);
    $name  = $data['name'] ?? '';
    $desc  = $data['description'] ?? '';
    $price = floatval($data['price'] ?? 0);

    $stmt = $conn->prepare("UPDATE items SET name=?, description=?, price=? WHERE id=?");
    $stmt->bind_param("ssdi", $name, $desc, $price, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true, "message" => "Item updated in XAMPP database!"]);
    exit;
}

if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
    }

    $stmt = $conn->prepare("DELETE FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true, "message" => "Item deleted from XAMPP database!"]);
    exit;
}
