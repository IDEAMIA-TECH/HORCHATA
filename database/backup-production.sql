-- Script de ejemplo para hacer backup de la base de datos de producción
-- Ejecutar ANTES de importar datos de desarrollo

-- INSTRUCCIONES:
-- 1. Conectar al servidor de producción
-- 2. Ejecutar este comando desde la terminal:
--
-- mysqldump -h [HOST_PRODUCTION] -u [USER] -p [DATABASE_NAME] > backup-production-$(date +%Y%m%d-%H%M%S).sql
--
-- 3. Guardar el backup en un lugar seguro
--
-- EJEMPLO:
-- mysqldump -h 173.231.22.109 -u horchatamexfood_user -p horchatamexfood_db > backup-production-20250127.sql

-- IMPORTANTE: 
-- - Reemplazar [HOST_PRODUCTION], [USER], y [DATABASE_NAME] con los valores reales
-- - El backup incluirá estructura y datos
-- - Guardar el backup antes de importar datos de desarrollo

