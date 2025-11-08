<?php
require_once '../config/db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT id_tratamiento, nombre, descripcion, duracion_estimada, precio_base 
        FROM citas.tratamientos 
        ORDER BY nombre
    ");

    $tratamientos = $stmt->fetchAll();
    echo json_encode([
        'success' => true,
        'data' => $tratamientos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener tratamientos: ' . $e->getMessage()
    ]);
}
?>