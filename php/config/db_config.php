<?php
$servername = "64.23.164.10";
$username = "dev";
$password = "Dev_2025";
$dbname = "ProbucalNeiraBD";
$port = 5432;

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $dsn = "pgsql:host={$servername};port={$port};dbname={$dbname}";
    $conn = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $conn->exec("SET search_path TO citas");

} catch (PDOException $e) {
    error_log("Error de conexión a la BD: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}