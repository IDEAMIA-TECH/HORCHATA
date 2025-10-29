-- =============================================
-- Horchata Mexican Food - Respaldo de Datos
-- Script para crear respaldo antes de la actualización del menú
-- =============================================

-- Crear tabla temporal para respaldo de productos
CREATE TABLE IF NOT EXISTS products_backup AS 
SELECT * FROM products;

-- Crear tabla temporal para respaldo de categorías
CREATE TABLE IF NOT EXISTS categories_backup AS 
SELECT * FROM categories;

-- Mostrar información del respaldo
SELECT 'RESPALDO CREADO' as 'ESTADO', COUNT(*) as 'PRODUCTOS RESPALDADOS' FROM products_backup;
SELECT 'RESPALDO CREADO' as 'ESTADO', COUNT(*) as 'CATEGORÍAS RESPALDADAS' FROM categories_backup;

-- =============================================
-- INSTRUCCIONES PARA RESTAURAR:
-- =============================================
-- Si necesitas restaurar los datos anteriores, ejecuta:
-- 
-- 1. Eliminar productos actuales:
--    DELETE FROM products;
-- 
-- 2. Restaurar productos del respaldo:
--    INSERT INTO products SELECT * FROM products_backup;
-- 
-- 3. Restaurar categorías del respaldo:
--    UPDATE categories SET 
--        name_en = (SELECT name_en FROM categories_backup WHERE categories_backup.id = categories.id),
--        name_es = (SELECT name_es FROM categories_backup WHERE categories_backup.id = categories.id),
--        description_en = (SELECT description_en FROM categories_backup WHERE categories_backup.id = categories.id),
--        description_es = (SELECT description_es FROM categories_backup WHERE categories_backup.id = categories.id)
--    WHERE EXISTS (SELECT 1 FROM categories_backup WHERE categories_backup.id = categories.id);
-- 
-- 4. Eliminar tablas de respaldo:
--    DROP TABLE products_backup;
--    DROP TABLE categories_backup;
-- =============================================
