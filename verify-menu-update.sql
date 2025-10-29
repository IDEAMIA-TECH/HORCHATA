-- =============================================
-- Horchata Mexican Food - Verificación del Menú
-- Script para verificar que la actualización del menú fue exitosa
-- =============================================

-- Verificar categorías
SELECT 'CATEGORÍAS' as 'SECCIÓN', COUNT(*) as 'TOTAL' FROM categories WHERE is_active = 1;

-- Verificar productos por categoría
SELECT 
    c.name_en as 'CATEGORÍA',
    COUNT(p.id) as 'PRODUCTOS',
    MIN(p.price) as 'PRECIO MÍN',
    MAX(p.price) as 'PRECIO MÁX',
    AVG(p.price) as 'PRECIO PROMEDIO'
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
WHERE c.is_active = 1
GROUP BY c.id, c.name_en
ORDER BY c.sort_order;

-- Verificar productos destacados
SELECT 'PRODUCTOS DESTACADOS' as 'SECCIÓN', COUNT(*) as 'TOTAL' FROM products WHERE is_featured = 1;

-- Verificar productos disponibles
SELECT 'PRODUCTOS DISPONIBLES' as 'SECCIÓN', COUNT(*) as 'TOTAL' FROM products WHERE is_available = 1;

-- Verificar productos por rango de precio
SELECT 
    CASE 
        WHEN price <= 5.00 THEN '$1.00 - $5.00'
        WHEN price <= 10.00 THEN '$5.01 - $10.00'
        WHEN price <= 15.00 THEN '$10.01 - $15.00'
        ELSE '$15.01+'
    END as 'RANGO DE PRECIO',
    COUNT(*) as 'PRODUCTOS'
FROM products 
WHERE is_available = 1
GROUP BY 
    CASE 
        WHEN price <= 5.00 THEN '$1.00 - $5.00'
        WHEN price <= 10.00 THEN '$5.01 - $10.00'
        WHEN price <= 15.00 THEN '$10.01 - $15.00'
        ELSE '$15.01+'
    END
ORDER BY MIN(price);

-- Verificar algunos productos específicos
SELECT 'PRODUCTOS ESPECÍFICOS' as 'SECCIÓN', '' as 'VERIFICACIÓN';
SELECT 
    p.name_en as 'PRODUCTO',
    c.name_en as 'CATEGORÍA',
    p.price as 'PRECIO',
    CASE WHEN p.is_featured = 1 THEN 'SÍ' ELSE 'NO' END as 'DESTACADO'
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.name_en IN (
    'Chorizo Plate',
    'California Burrito',
    'Carne Asada Plate',
    'Steak & Eggs Plate',
    'Fajita Plate Chicken or Beef'
)
ORDER BY p.name_en;
