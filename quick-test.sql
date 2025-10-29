-- =============================================
-- Horchata Mexican Food - Prueba Rápida del Menú
-- Script para verificar que la actualización fue exitosa
-- =============================================

-- Verificar que las categorías existen
SELECT 'VERIFICACIÓN DE CATEGORÍAS' as 'PRUEBA';
SELECT 
    id,
    name_en,
    name_es,
    is_active
FROM categories 
WHERE is_active = 1
ORDER BY sort_order;

-- Verificar que los productos se insertaron correctamente
SELECT 'VERIFICACIÓN DE PRODUCTOS' as 'PRUEBA';
SELECT 
    COUNT(*) as 'TOTAL_PRODUCTOS',
    COUNT(CASE WHEN is_featured = 1 THEN 1 END) as 'PRODUCTOS_DESTACADOS',
    COUNT(CASE WHEN is_available = 1 THEN 1 END) as 'PRODUCTOS_DISPONIBLES',
    MIN(price) as 'PRECIO_MINIMO',
    MAX(price) as 'PRECIO_MAXIMO',
    ROUND(AVG(price), 2) as 'PRECIO_PROMEDIO'
FROM products;

-- Verificar productos por categoría
SELECT 'PRODUCTOS POR CATEGORÍA' as 'PRUEBA';
SELECT 
    c.name_en as 'CATEGORIA',
    COUNT(p.id) as 'PRODUCTOS',
    ROUND(AVG(p.price), 2) as 'PRECIO_PROMEDIO'
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
WHERE c.is_active = 1
GROUP BY c.id, c.name_en
ORDER BY c.sort_order;

-- Verificar algunos productos específicos del menú
SELECT 'PRODUCTOS ESPECÍFICOS DEL MENÚ' as 'PRUEBA';
SELECT 
    p.name_en as 'PRODUCTO',
    c.name_en as 'CATEGORIA',
    p.price as 'PRECIO',
    CASE WHEN p.is_featured = 1 THEN 'DESTACADO' ELSE 'NORMAL' END as 'ESTADO'
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.name_en IN (
    'Chorizo Plate',
    'Huevos Rancheros Plate',
    'California Burrito',
    'Carne Asada Plate',
    'Steak & Eggs Plate',
    'Fajita Plate Chicken or Beef',
    'Macho Burrito',
    '3 Soft Tacos',
    'Shrimp Cocktail',
    'Hamburger'
)
ORDER BY p.name_en;

-- Verificar que no hay productos duplicados
SELECT 'VERIFICACIÓN DE DUPLICADOS' as 'PRUEBA';
SELECT 
    name_en,
    COUNT(*) as 'REPETICIONES'
FROM products
GROUP BY name_en
HAVING COUNT(*) > 1;

-- Verificar que todos los productos tienen categoría válida
SELECT 'VERIFICACIÓN DE CATEGORÍAS VÁLIDAS' as 'PRUEBA';
SELECT 
    COUNT(*) as 'PRODUCTOS_SIN_CATEGORIA'
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE c.id IS NULL;

-- Verificar que los precios son válidos
SELECT 'VERIFICACIÓN DE PRECIOS' as 'PRUEBA';
SELECT 
    COUNT(*) as 'PRODUCTOS_PRECIO_INVALIDO'
FROM products
WHERE price <= 0 OR price IS NULL;

-- Resumen final
SELECT 'RESUMEN FINAL' as 'PRUEBA';
SELECT 
    'CATEGORÍAS ACTIVAS' as 'ITEM',
    COUNT(*) as 'CANTIDAD'
FROM categories WHERE is_active = 1
UNION ALL
SELECT 
    'PRODUCTOS TOTALES' as 'ITEM',
    COUNT(*) as 'CANTIDAD'
FROM products
UNION ALL
SELECT 
    'PRODUCTOS DESTACADOS' as 'ITEM',
    COUNT(*) as 'CANTIDAD'
FROM products WHERE is_featured = 1
UNION ALL
SELECT 
    'PRODUCTOS DISPONIBLES' as 'ITEM',
    COUNT(*) as 'CANTIDAD'
FROM products WHERE is_available = 1;
