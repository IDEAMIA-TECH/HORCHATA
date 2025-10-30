-- Script para preparar la base de datos antes de migrar a producción
-- Ejecutar DESPUÉS de copiar la BD de dev a producción

-- ============================================
-- 1. ACTUALIZAR CONFIGURACIONES DE URL
-- ============================================
UPDATE settings 
SET setting_value = 'https://horchatamexfood.com' 
WHERE setting_key = 'site_url';

UPDATE settings 
SET setting_value = 'https://horchatamexfood.com' 
WHERE setting_key = 'restaurant_website';

-- ============================================
-- 2. LIMPIAR DATOS DE PRUEBA (OPCIONAL)
-- ============================================
-- Descomentar las siguientes líneas si quieres eliminar datos de prueba:

-- Eliminar órdenes de prueba (deja solo órdenes reales)
-- DELETE FROM orders WHERE payment_status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- Eliminar reseñas de prueba
-- DELETE FROM reviews WHERE is_approved = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Eliminar usuarios de prueba (si los hay)
-- DELETE FROM users WHERE username LIKE '%test%' OR username LIKE '%demo%';

-- ============================================
-- 3. VERIFICAR Y ACTUALIZAR CONFIGURACIONES
-- ============================================
-- Verificar que las URLs estén correctas
SELECT setting_key, setting_value 
FROM settings 
WHERE setting_key IN ('site_url', 'restaurant_website', 'restaurant_email');

-- ============================================
-- 4. VERIFICAR INTEGRIDAD DE DATOS
-- ============================================
-- Contar registros por tabla
SELECT 'categories' as tabla, COUNT(*) as total FROM categories
UNION ALL
SELECT 'products', COUNT(*) FROM products
UNION ALL
SELECT 'orders', COUNT(*) FROM orders
UNION ALL
SELECT 'order_items', COUNT(*) FROM order_items
UNION ALL
SELECT 'reviews', COUNT(*) FROM reviews
UNION ALL
SELECT 'users', COUNT(*) FROM users
UNION ALL
SELECT 'contact_messages', COUNT(*) FROM contact_messages;

