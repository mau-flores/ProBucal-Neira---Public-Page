<?php
require_once '../config/db_config.php';
require_once '../classes/DniAPI.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validaciones mínimas
    if (empty($data['dni']) || empty($data['fecha']) || empty($data['hora']) || empty($data['id_tratamiento']) || empty($data['id_odontologo'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios (dni, fecha, hora, tratamiento u odontólogo)']);
        exit;
    }

    try {
        // Construir nombre completo (desde campos autocompletados)
        $nombreCompleto = trim(($data['nombres'] ?? '') . ' ' . ($data['apellidos'] ?? ''));
        if (empty($nombreCompleto)) {
            // Intentar obtener desde la API por dni
            $dniAPI = new DniAPI();
            $resultado = $dniAPI->consultarDNI($data['dni']);
            $datosPersona = json_decode($resultado, true);
            if (isset($datosPersona['success']) && $datosPersona['success']) {
                $nombreCompleto = ($datosPersona['data']['nombres'] ?? '') . ' ' . ($datosPersona['data']['apellido_paterno'] ?? '') . ' ' . ($datosPersona['data']['apellido_materno'] ?? '');
                $nombreCompleto = trim($nombreCompleto);
            }
        }

        // Insertar paciente en pacientes_online
        $stmtP = $conn->prepare("INSERT INTO citas.pacientes_online (nombre_completo, edad, telefono, email) VALUES (:nombre_completo, :edad, :telefono, :email)");
        $stmtP->execute([
            ':nombre_completo' => $nombreCompleto ?: 'Paciente',
            ':edad' => !empty($data['edad']) ? $data['edad'] : null,
            ':telefono' => $data['telefono'] ?? null,
            ':email' => $data['email'] ?? null
        ]);

        // Obtener id_paciente (compatibilidad MySQL/Postgres)
        $idPaciente = $conn->lastInsertId();
        if (!$idPaciente) {
            // intentar buscar el último registro coincidente
            $stmtFind = $conn->prepare("SELECT id_paciente FROM citas.pacientes_online WHERE nombre_completo = :nombre_completo AND telefono = :telefono ORDER BY id_paciente DESC LIMIT 1");
            $stmtFind->execute([
                ':nombre_completo' => $nombreCompleto ?: 'Paciente',
                ':telefono' => $data['telefono'] ?? null
            ]);
            $idPaciente = $stmtFind->fetchColumn();
        }

        if (!$idPaciente) {
            throw new Exception('No se pudo obtener id de paciente');
        }

        // Insertar cita en citas_online
        $stmtC = $conn->prepare("INSERT INTO citas.citas_online (
            id_paciente, id_tratamiento, id_odontologo, fecha, hora, duracion_minutos, prioridad, fuente_cita, alergias_observaciones, notas
        ) VALUES (
            :id_paciente, :id_tratamiento, :id_odontologo, :fecha, :hora, :duracion_minutos, :prioridad, :fuente_cita, :alergias_observaciones, :notas
        )");

        $stmtC->execute([
            ':id_paciente' => $idPaciente,
            ':id_tratamiento' => $data['id_tratamiento'],
            ':id_odontologo' => $data['id_odontologo'],
            ':fecha' => $data['fecha'],
            ':hora' => $data['hora'],
            ':duracion_minutos' => $data['duracion_minutos'] ?? 30,
            ':prioridad' => $data['prioridad'] ?? 'Normal',
            ':fuente_cita' => 'Web',
            ':alergias_observaciones' => $data['alergias_observaciones'] ?? null,
            ':notas' => $data['notas'] ?? null
        ]);

        echo json_encode(['success' => true, 'message' => 'Reserva guardada exitosamente']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al procesar reserva: ' . $e->getMessage()]);
    }
}
?>