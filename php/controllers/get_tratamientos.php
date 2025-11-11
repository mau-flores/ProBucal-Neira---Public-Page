<?php
// Deshabilitar mostrar errores - solo queremos JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT id_tratamiento, nombre, descripcion
        FROM citas.tratamientos 
        ORDER BY nombre
    ");

    if ($stmt === false) {
        throw new PDOException($conn->errorInfo()[2]);
    }

    $tratamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tratamientos === false) {
        throw new PDOException("Error al obtener los datos");
    }

    echo json_encode([
        'success' => true,
        'data' => $tratamientos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener tratamientos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
}
?>