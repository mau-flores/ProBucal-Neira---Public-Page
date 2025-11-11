<?php
// Configuración de conexión a PostgreSQL (usar PDO)
// Valores proporcionados por el usuario (Navicat):
$servername = "64.23.164.10";
$username = "dev";
$password = "Dev_2025";
$dbname = "ProbucalNeiraBD";
$port = 5432;

// Mostrar errores en desarrollo; en producción desactivar o registrar en log
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Crear conexión PDO para PostgreSQL
    $dsn = "pgsql:host={$servername};port={$port};dbname={$dbname}";
    $conn = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Establecer el esquema por defecto (si tus tablas están en el esquema 'citas')
    $conn->exec("SET search_path TO citas");

} catch (PDOException $e) {
    // Registrar error y devolver JSON mínimo (para llamadas AJAX)
    error_log("Error de conexión a la BD: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}