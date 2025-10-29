#!/bin/bash
# =============================================
# Horchata Mexican Food - Actualización del Menú
# Script principal para actualizar la base de datos
# =============================================

echo "🍽️  Horchata Mexican Food - Actualización del Menú"
echo "=================================================="
echo ""

# Configuración de la base de datos
DB_HOST="localhost"
DB_NAME="horchata_db"
DB_USER="root"
DB_PASS=""

echo "📋 Paso 1: Creando respaldo de datos existentes..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < backup-before-update.sql
if [ $? -eq 0 ]; then
    echo "✅ Respaldo creado exitosamente"
else
    echo "❌ Error al crear respaldo"
    exit 1
fi

echo ""
echo "📋 Paso 2: Actualizando menú con nuevos datos..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < update-menu-data.sql
if [ $? -eq 0 ]; then
    echo "✅ Menú actualizado exitosamente"
else
    echo "❌ Error al actualizar menú"
    echo "🔄 Restaurando datos anteriores..."
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME -e "DELETE FROM products; INSERT INTO products SELECT * FROM products_backup;"
    exit 1
fi

echo ""
echo "📋 Paso 3: Verificando actualización..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < verify-menu-update.sql

echo ""
echo "🎉 ¡Actualización del menú completada exitosamente!"
echo ""
echo "📊 Resumen de la actualización:"
echo "- Se procesaron todos los productos del CSV"
echo "- Se organizaron en 10 categorías"
echo "- Se marcaron productos destacados"
echo "- Se creó respaldo de seguridad"
echo ""
echo "🔍 Para verificar manualmente, ejecuta:"
echo "   mysql -u $DB_USER -p$DB_PASS $DB_NAME < verify-menu-update.sql"
echo ""
echo "🔄 Para restaurar datos anteriores si es necesario:"
echo "   1. DELETE FROM products;"
echo "   2. INSERT INTO products SELECT * FROM products_backup;"
echo "   3. DROP TABLE products_backup;"
echo "   4. DROP TABLE categories_backup;"
