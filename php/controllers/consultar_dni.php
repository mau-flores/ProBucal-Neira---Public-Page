<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer cabeceras CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Asegurarse de que el archivo existe
if (!file_exists(__DIR__ . '/../classes/DniAPI.php')) {
    echo json_encode(['success' => false, 'message' => 'Error: DniAPI.php no encontrado']);
    exit;
}

require_once __DIR__ . '/../classes/DniAPI.php';

if (!isset($_GET['dni'])) {
    echo json_encode(['success' => false, 'message' => 'DNI no proporcionado']);
    exit;
}

$dni = $_GET['dni'];

try {
    $dniAPI = new DniAPI();
    $resultado = $dniAPI->consultarDNI($dni);

    // Verificar si hay error en la respuesta
    if ($resultado === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta a la API',
            'raw_response' => $resultado
        ]);
        exit;
    }

    $datos = json_decode($resultado, true);

    // Depuración de la respuesta
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al decodificar JSON: ' . json_last_error_msg(),
            'raw_response' => $resultado
        ]);
        exit;
    }

    if ($datos && isset($datos['data']) && $datos['success']) {
        echo json_encode([
            'success' => true,
            'nombres' => $datos['data']['nombres'],
            'apellidos' => $datos['data']['apellido_paterno'] . ' ' . $datos['data']['apellido_materno']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos para el DNI proporcionado',
            'api_response' => $datos
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}