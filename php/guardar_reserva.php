<?php

header('Content-Type: application/json');

// ======== INCLUIR CONFIGURACIÓN DE BASE DE DATOS =========
$config = require __DIR__ . '/config/api_config.php';
$host = $config['host'];
$db = $config['db'];
$user = $config['user'];
$pass = $config['pass'];
$port = $config['port'];

// ======== VALIDAR MÉTODO =========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit;
}

// ======== LEER JSON DEL FETCH =========
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'error' => 'Formato inválido (send JSON)'
    ]);
    exit;
}

$dni = $input['dni'] ?? '';
$nombres = $input['nombres'] ?? '';
$apellidos = $input['apellidos'] ?? '';
$fecha = $input['fecha'] ?? '';
$motivo = $input['motivo'] ?? '';
$otros = $input['otros'] ?? '';

// ======== VALIDACIONES =========
if (!$dni || !$nombres || !$apellidos || !$fecha || !$motivo) {
    echo json_encode([
        'success' => false,
        'error' => 'Faltan datos obligatorios'
    ]);
    exit;
}

// ======== GUARDAR EN BD =========
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO citas.reservas (dni, nombres, apellidos, fecha, motivo, otros)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([$dni, $nombres, $apellidos, $fecha, $motivo, $otros]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>