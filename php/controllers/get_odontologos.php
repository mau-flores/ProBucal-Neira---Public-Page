<?php
// Deshabilitar mostrar errores - solo queremos JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT id_odontologo, nombre_completo, especialidad
        FROM citas.odontologos 
        ORDER BY nombre_completo
    ");

    if ($stmt === false) {
        throw new PDOException($conn->errorInfo()[2]);
    }

    $odontologos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($odontologos === false) {
        throw new PDOException("Error al obtener los datos");
    }

    echo json_encode([
        'success' => true,
        'data' => $odontologos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener odontólogos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
}
?>