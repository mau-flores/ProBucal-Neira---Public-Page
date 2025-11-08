-- Crear el esquema si aún no existe
CREATE SCHEMA IF NOT EXISTS "citas";

-- Tabla de pacientes
CREATE TABLE "citas"."pacientes" (
    "id_paciente" SERIAL PRIMARY KEY,
    "dni" VARCHAR(8) UNIQUE NOT NULL,
    "nombres" VARCHAR(100) NOT NULL,
    "apellidos" VARCHAR(100) NOT NULL,
    "fecha_registro" DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Tabla de odontólogos
CREATE TABLE "citas"."odontologos" (
    "id_odontologo" SERIAL PRIMARY KEY,
    "nombres" VARCHAR(100) NOT NULL,
    "apellidos" VARCHAR(100) NOT NULL,
    "especialidad" VARCHAR(100),
    "estado" VARCHAR(20) DEFAULT 'Activo'
);

-- Tabla de tratamientos
CREATE TABLE "citas"."tratamientos" (
    "id_tratamiento" SERIAL PRIMARY KEY,
    "nombre" VARCHAR(100) NOT NULL,
    "descripcion" TEXT,
    "duracion_estimada" INT DEFAULT 30,
    "precio_base" DECIMAL(10,2)
);

-- Tabla principal de citas
CREATE TABLE "citas"."citas" (
    "id_cita" SERIAL PRIMARY KEY,
    "id_paciente" INT NOT NULL REFERENCES citas.pacientes(id_paciente),
    "id_tratamiento" INT NOT NULL REFERENCES citas.tratamientos(id_tratamiento),
    "id_odontologo" INT NOT NULL REFERENCES citas.odontologos(id_odontologo),
    "id_consultorio" INT,
    "id_seguro" INT,
    "fecha" DATE NOT NULL,
    "hora" TIME(6) NOT NULL,
    "duracion_minutos" INT DEFAULT 30,
    "prioridad" VARCHAR(20) DEFAULT 'Normal',
    "fuente_cita" VARCHAR(50) DEFAULT 'Presencial',
    "alergias_observaciones" TEXT,
    "notas" TEXT
);

-- Datos de ejemplo para tratamientos
INSERT INTO citas.tratamientos (nombre, descripcion, duracion_estimada, precio_base) VALUES
('Limpieza Dental', 'Limpieza dental profesional', 30, 150.00),
('Extracción Simple', 'Extracción dental simple', 45, 200.00),
('Consulta General', 'Evaluación dental general', 30, 100.00);

-- Datos de ejemplo para odontólogos
INSERT INTO citas.odontologos (nombres, apellidos, especialidad) VALUES
('Juan', 'Pérez', 'Odontología General'),
('María', 'García', 'Ortodoncia'),
('Carlos', 'López', 'Cirugía Maxilofacial');