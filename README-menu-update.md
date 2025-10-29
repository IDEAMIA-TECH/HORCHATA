# 🍽️ Horchata Mexican Food - Actualización del Menú

## 📋 Descripción
Este conjunto de scripts actualiza la base de datos de Horchata Mexican Food con el nuevo menú proporcionado en formato CSV.

## 📁 Archivos Incluidos

### Scripts SQL:
- **`backup-before-update.sql`** - Crea respaldo de datos existentes
- **`update-menu-data.sql`** - Actualiza la base de datos con el nuevo menú
- **`verify-menu-update.sql`** - Verifica que la actualización fue exitosa

### Scripts de Ejecución:
- **`update-menu.sh`** - Script principal que ejecuta todo el proceso
- **`README.md`** - Este archivo con instrucciones

## 🚀 Instrucciones de Uso

### Opción 1: Ejecución Automática (Recomendada)
```bash
./update-menu.sh
```

### Opción 2: Ejecución Manual
```bash
# 1. Crear respaldo
mysql -u root -p horchata_db < backup-before-update.sql

# 2. Actualizar menú
mysql -u root -p horchata_db < update-menu-data.sql

# 3. Verificar actualización
mysql -u root -p horchata_db < verify-menu-update.sql
```

## 📊 Datos del Nuevo Menú

### Categorías Actualizadas:
1. **Breakfast Plates** (14 productos)
2. **Breakfast Burritos** (8 productos)
3. **Daily Specials** (8 productos)
4. **Seafood** (7 productos)
5. **Special Burritos** (17 productos)
6. **Combinations** (17 productos)
7. **Tacos & Quesadillas** (13 productos)
8. **Desserts** (6 productos)
9. **Nachos & Sides** (31 productos)
10. **Salads & Burgers** (12 productos)

### Estadísticas:
- **Total de productos:** 133
- **Productos destacados:** 8
- **Rango de precios:** $0.50 - $16.99
- **Precio promedio:** $9.85

## 🔒 Seguridad

### Respaldo Automático:
- Se crean tablas de respaldo antes de la actualización
- `products_backup` - Respaldo de productos
- `categories_backup` - Respaldo de categorías

### Restauración en Caso de Error:
```sql
-- Restaurar productos
DELETE FROM products;
INSERT INTO products SELECT * FROM products_backup;

-- Restaurar categorías
UPDATE categories SET 
    name_en = (SELECT name_en FROM categories_backup WHERE categories_backup.id = categories.id),
    name_es = (SELECT name_es FROM categories_backup WHERE categories_backup.id = categories.id),
    description_en = (SELECT description_en FROM categories_backup WHERE categories_backup.id = categories.id),
    description_es = (SELECT description_es FROM categories_backup WHERE categories_backup.id = categories.id)
WHERE EXISTS (SELECT 1 FROM categories_backup WHERE categories_backup.id = categories.id);

-- Limpiar respaldos
DROP TABLE products_backup;
DROP TABLE categories_backup;
```

## ✅ Verificación Post-Actualización

### Verificaciones Automáticas:
- ✅ Conteo de categorías activas
- ✅ Conteo de productos por categoría
- ✅ Verificación de productos destacados
- ✅ Verificación de productos disponibles
- ✅ Distribución por rango de precios
- ✅ Verificación de productos específicos

### Verificaciones Manuales Recomendadas:
1. **Frontend:** Verificar que `menu.php` muestre todos los productos
2. **Categorías:** Verificar que las categorías se muestren correctamente
3. **Precios:** Verificar que los precios sean correctos
4. **Productos destacados:** Verificar que se muestren en `index.php`
5. **Búsqueda:** Verificar que la búsqueda funcione correctamente

## 🐛 Solución de Problemas

### Error: "Table doesn't exist"
```bash
# Verificar que la base de datos existe
mysql -u root -p -e "SHOW DATABASES LIKE 'horchata_db';"
```

### Error: "Access denied"
```bash
# Verificar permisos de usuario
mysql -u root -p -e "SHOW GRANTS FOR 'root'@'localhost';"
```

### Error: "Syntax error"
```bash
# Verificar sintaxis SQL
mysql -u root -p --verbose < update-menu-data.sql
```

## 📞 Soporte

Si encuentras algún problema:
1. Revisa los logs de MySQL
2. Verifica que todos los archivos estén presentes
3. Ejecuta las verificaciones manuales
4. Restaura desde el respaldo si es necesario

## 🎯 Próximos Pasos

Después de la actualización exitosa:
1. **Probar el sitio web** - Verificar que todo funcione correctamente
2. **Actualizar imágenes** - Subir imágenes para productos sin foto
3. **Configurar extras** - Verificar que los extras funcionen correctamente
4. **Probar pedidos** - Hacer pedidos de prueba para verificar funcionalidad
5. **Limpiar respaldos** - Eliminar tablas de respaldo después de confirmar que todo funciona

---
**Fecha de creación:** $(date)
**Versión:** 1.0
**Autor:** Sistema de Actualización de Menú - Horchata Mexican Food
