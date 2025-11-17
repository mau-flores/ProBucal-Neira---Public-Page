# ProBucal Neira - Sistema de Reserva de Citas

Aplicación web para reserva de citas odontológicas con integración a API de consulta de DNI y base de datos PostgreSQL.

## Requisitos

- Docker & Docker Compose (para despliegue en contenedores)
- PHP 8.1+ (si ejecutas localmente sin Docker)
- PostgreSQL 15 (incluido en docker-compose)
- Node.js (opcional, solo si deseas procesar CSS/JS)

## Despliegue Local (con Docker)

### Pasos rápidos

1. **Clonar y posicionarse en el proyecto:**
   ```bash
   git clone <tu-repo-url>
   cd PAGE_WEB
   ```

2. **Configurar variables de entorno:**
   ```bash
   # Editar php/config/api_config.php y reemplazar el token por tu token real
   # (Archivo ya presente en el proyecto, asegurar que no se suba al repo)
   ```

3. **Iniciar contenedores (desarrollo con DB local):**
   ```bash
   docker-compose up --build
   ```

4. **Acceder a la aplicación:**
   - URL: http://localhost/PAGE_WEB/index.html
   - Página de reservas: http://localhost/PAGE_WEB/reserva.html

5. **Detener contenedores:**
   ```bash
   docker-compose down
   ```

### Uso de base de datos remota (producción)

Si usas la base de datos remota en `64.23.164.10`:

1. **Editar php/config/db_config.php:**
   ```php
   $servername = "64.23.164.10";
   $username = "dev";
   $password = "Dev_2025";
   $dbname = "ProbucalNeiraBD";
   ```

2. **Iniciar solo el contenedor web:**
   ```bash
   docker-compose up web --build
   ```
   (Esto solo levanta el servicio `web`, sin crear un PostgreSQL local)

3. **O usar el archivo de compose para producción:**
   ```bash
   docker-compose -f docker-compose.prod.yml up --build
   ```

## Despliegue en Plataforma de Contenedores (Azure, AWS, etc.)

### Requisitos previos
- Archivo `Dockerfile` en la raíz del proyecto ✓
- Archivo `docker-compose.yml` (opcional, para referencia) ✓
- Credenciales de acceso a tu plataforma

### Pasos generales

1. **Preparar repositorio:**
   - Asegurar que `Dockerfile` está en la raíz
   - El `Dockerfile` debe referenciar la aplicación correctamente

2. **Configurar variables de entorno en la plataforma:**
   - Crear un archivo `.env` o configurar en el panel de la plataforma:
     ```
     DB_HOST=64.23.164.10
     DB_USER=dev
     DB_PASSWORD=Dev_2025
     DB_NAME=ProbucalNeiraBD
     API_TOKEN=<tu_token_aqui>
     ```

3. **Desplegar:**
   - Conectar el repositorio GitHub a tu plataforma (Azure Container Instances, AWS ECS, Heroku, etc.)
   - La plataforma detectará el `Dockerfile` y construirá/desplegará automáticamente
   - Configurar rutas y puertos según plataforma

### Ejemplo Azure Container Instances

```bash
# Build local
docker build -t probucal-web:latest .

# Tag para Azure Container Registry
docker tag probucal-web:latest <tu-registro>.azurecr.io/probucal-web:latest

# Push
docker push <tu-registro>.azurecr.io/probucal-web:latest

# Deploy (desde Azure CLI)
az container create \
  --resource-group <tu-grupo> \
  --name probucal-web \
  --image <tu-registro>.azurecr.io/probucal-web:latest \
  --ports 80 \
  --environment-variables DB_HOST=64.23.164.10
```

## Estructura del Proyecto

```
PAGE_WEB/
├── Dockerfile                    # Configuración del contenedor
├── docker-compose.yml            # Compose para desarrollo (incluye DB local)
├── docker-compose.prod.yml       # Compose para producción (sin DB local)
├── index.html                    # Página principal
├── reserva.html                  # Formulario de reserva
├── css/
│   ├── decode.css               # Estilos página principal
│   └── reserva.css              # Estilos formulario
├── js/
│   └── consulta-dni.js          # Lógica consulta DNI y reserva
├── php/
│   ├── config/
│   │   ├── db_config.php        # Configuración BD (editar con tus datos)
│   │   └── api_config.php       # Token API DNI (NO SUBIR al repo)
│   ├── classes/
│   │   └── DniAPI.php           # Clase para consultar API DNI
│   └── controllers/
│       ├── consultar_dni.php    # Endpoint consulta DNI
│       ├── get_tratamientos.php # Endpoint lista tratamientos
│       ├── get_odontologos.php  # Endpoint lista odontólogos
│       └── procesar_reserva.php # Endpoint procesar reserva
└── .gitignore                    # Excluir archivos sensibles
```

## Configuración Importante

### 1. Token API DNI
- Ubicación: `php/config/api_config.php`
- **NO subir al repositorio** (ya está en `.gitignore`)
- Editar localmente con tu token real

### 2. Conexión a Base de Datos
- Ubicación: `php/config/db_config.php`
- Credenciales (ya configuradas para tu servidor remoto)
- Si usas docker-compose local, cambia `$servername` a `"db"` (nombre del servicio)

### 3. Variables de entorno (producción)
- Crear `.env` o configura en la plataforma de despliegue

## Solución de Problemas

### Error: "could not find driver"
- **Causa:** PHP no tiene pdo_pgsql instalado
- **Solución:** Docker ya lo incluye en el Dockerfile. Si usas local, instalar extensión PostgreSQL en PHP

### Error: "Conexión rechazada a la BD"
- **Causa:** Host/puerto/credenciales incorrectos
- **Solución:** Verificar `db_config.php` y credenciales en 64.23.164.10:5432

### Puerto ya en uso
```bash
# Si el puerto 80 ya está en uso, cambiar en docker-compose.yml
ports:
  - "8080:80"  # Usa puerto 8080 en tu máquina
```

## Testing

### Test de conectividad a DB
- Abre: http://localhost/PAGE_WEB/php/controllers/db_test.php
- Si devuelve `{"success":true, "database":"ProbucalNeiraBD", "schema":"citas"}` → OK

### Test de endpoints
- Tratamientos: http://localhost/PAGE_WEB/php/controllers/get_tratamientos.php
- Odontólogos: http://localhost/PAGE_WEB/php/controllers/get_odontologos.php
- Consulta DNI: http://localhost/PAGE_WEB/php/controllers/consultar_dni.php?dni=12345678

## Mantenimiento

### Actualizar dependencias (Docker)
```bash
docker-compose build --no-cache
docker-compose up
```

### Ver logs
```bash
docker-compose logs -f web
docker-compose logs -f db
```

### Entrar al contenedor (para debugging)
```bash
docker-compose exec web bash
docker-compose exec db psql -U dev -d ProbucalNeiraBD
```

## Seguridad (Producción)

- [ ] Cambiar contraseña de PostgreSQL
- [ ] Configurar firewall para puerto 5432 (solo acceso local o de IPs específicas)
- [ ] Usar HTTPS (configurar en reverse proxy/load balancer)
- [ ] No publicar archivos `.env` o de configuración con credenciales
- [ ] Implementar validaciones y sanitización en formularios
- [ ] Usar variables de entorno para configuración sensible

## Licencia

ProBucal Neira © 2025

## Contacto

Para soporte o dudas, contactar al equipo de desarrollo.
