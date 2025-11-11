#!/bin/bash
# Script de despliegue para ProBucal Neira

set -e

echo "🐳 ProBucal Neira - Docker Deploy Helper"
echo "========================================="
echo ""

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Instálalo desde https://www.docker.com/products/docker-desktop"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado."
    exit 1
fi

echo "✅ Docker y Docker Compose detectados"
echo ""

# Menú de opciones
echo "Selecciona una opción:"
echo "1. Desarrollo local (con DB local)"
echo "2. Producción (DB remota 64.23.164.10)"
echo "3. Detener contenedores"
echo "4. Ver logs"
echo "5. Limpiar (eliminar contenedores y volúmenes)"
echo ""

read -p "Opción [1-5]: " option

case $option in
    1)
        echo "🚀 Iniciando entorno de desarrollo..."
        docker-compose up --build
        ;;
    2)
        echo "🚀 Iniciando entorno de producción..."
        docker-compose -f docker-compose.prod.yml up --build
        ;;
    3)
        echo "⏹️  Deteniendo contenedores..."
        docker-compose down
        ;;
    4)
        echo "📋 Mostrando logs..."
        docker-compose logs -f
        ;;
    5)
        echo "🗑️  Limpiando (esto eliminará contenedores y volúmenes)..."
        read -p "¿Estás seguro? (s/n): " confirm
        if [ "$confirm" = "s" ]; then
            docker-compose down -v
            echo "✅ Limpieza completada"
        fi
        ;;
    *)
        echo "❌ Opción inválida"
        exit 1
        ;;
esac
