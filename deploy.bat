@echo off
REM Script de despliegue para ProBucal Neira (Windows)

setlocal enabledelayedexpansion

echo.
echo 🐳 ProBucal Neira - Docker Deploy Helper
echo =========================================
echo.

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker no está instalado. Instálalo desde https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Compose no está instalado.
    pause
    exit /b 1
)

echo ✅ Docker y Docker Compose detectados
echo.

echo Selecciona una opción:
echo 1. Desarrollo local (con DB local)
echo 2. Producción (DB remota 64.23.164.10)
echo 3. Detener contenedores
echo 4. Ver logs
echo 5. Limpiar (eliminar contenedores y volúmenes)
echo.

set /p option="Opción [1-5]: "

if "%option%"=="1" (
    echo 🚀 Iniciando entorno de desarrollo...
    docker-compose up --build
) else if "%option%"=="2" (
    echo 🚀 Iniciando entorno de producción...
    docker-compose -f docker-compose.prod.yml up --build
) else if "%option%"=="3" (
    echo ⏹️  Deteniendo contenedores...
    docker-compose down
) else if "%option%"=="4" (
    echo 📋 Mostrando logs...
    docker-compose logs -f
) else if "%option%"=="5" (
    echo 🗑️  Limpiando (esto eliminará contenedores y volúmenes)...
    set /p confirm="¿Estás seguro? (s/n): "
    if "!confirm!"=="s" (
        docker-compose down -v
        echo ✅ Limpieza completada
    )
) else (
    echo ❌ Opción inválida
    exit /b 1
)

pause
