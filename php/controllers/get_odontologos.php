<?php
require_once '../config/db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT id_odontologo, nombres, apellidos, especialidad 
        FROM citas.odontologos 
        WHERE estado = 'Activo' 
        ORDER BY apellidos, nombres
    ");

    $odontologos = $stmt->fetchAll();
    echo json_encode([
        'success' => true,
        'data' => $odontologos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener odontólogos: ' . $e->getMessage()
    ]);
}
?>