<?php
require_once '../config/db_config.php';
require_once '../classes/DniAPI.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['dni'])) {
        $dniAPI = new DniAPI();
        $resultado = $dniAPI->consultarDNI($data['dni']);

        // Convertir la respuesta de la API a array
        $datosPersona = json_decode($resultado, true);

        if (isset($datosPersona['success']) && $datosPersona['success']) {
            try {
                // Primero insertamos o actualizamos el paciente
                $stmtPaciente = $conn->prepare("
                    INSERT INTO citas.pacientes (dni, nombres, apellidos)
                    VALUES (:dni, :nombres, :apellidos)
                    ON CONFLICT (dni) DO UPDATE 
                    SET nombres = :nombres, apellidos = :apellidos
                    RETURNING id_paciente
                ");

                $stmtPaciente->execute([
                    ':dni' => $data['dni'],
                    ':nombres' => $datosPersona['data']['nombres'],
                    ':apellidos' => $datosPersona['data']['apellido_paterno'] . ' ' . $datosPersona['data']['apellido_materno']
                ]);

                $idPaciente = $stmtPaciente->fetchColumn();

                // Luego insertamos la cita online
                $stmt = $conn->prepare("
                    INSERT INTO citas.citas_online (
                        id_paciente, 
                        id_tratamiento, 
                        id_odontologo,
                        fecha,
                        hora,
                        duracion_minutos,
                        fuente_cita,
                        notas
                    ) VALUES (
                        :id_paciente,
                        :id_tratamiento,
                        :id_odontologo,
                        :fecha,
                        :hora,
                        :duracion,
                        'Web',
                        :notas
                    )
                ");

                $stmt->execute([
                    ':id_paciente' => $idPaciente,
                    ':id_tratamiento' => $data['id_tratamiento'],
                    ':id_odontologo' => $data['id_odontologo'],
                    ':fecha' => $data['fecha'],
                    ':hora' => $data['hora'],
                    ':duracion' => $data['duracion_minutos'] ?? 30,
                    ':notas' => $data['notas'] ?? null
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Reserva guardada exitosamente'
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al guardar la reserva: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo verificar el DNI'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'DNI no proporcionado'
        ]);
    }
}
?>