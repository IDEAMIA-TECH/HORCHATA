-- =============================================
-- Horchata Mexican Food - Actualización del Menú
-- Script para actualizar la base de datos con el nuevo menú
-- =============================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- =============================================
-- 1. ACTUALIZAR CATEGORÍAS EXISTENTES
-- =============================================

-- Actualizar categorías existentes para que coincidan con el nuevo menú
UPDATE `categories` SET 
    `name_en` = 'Breakfast Plates', 
    `name_es` = 'Platos de Desayuno',
    `description_en` = 'Traditional Mexican breakfast plates',
    `description_es` = 'Platos de desayuno tradicionales mexicanos'
WHERE `id` = 1;

UPDATE `categories` SET 
    `name_en` = 'Breakfast Burritos', 
    `name_es` = 'Burritos de Desayuno',
    `description_en` = 'Hearty breakfast burritos',
    `description_es` = 'Burritos de desayuno sustanciosos'
WHERE `id` = 2;

UPDATE `categories` SET 
    `name_en` = 'Daily Specials', 
    `name_es` = 'Especiales del Día',
    `description_en` = 'Chef\'s daily specials',
    `description_es` = 'Especiales del chef del día'
WHERE `id` = 3;

UPDATE `categories` SET 
    `name_en` = 'Seafood', 
    `name_es` = 'Mariscos',
    `description_en` = 'Fresh seafood dishes',
    `description_es` = 'Platos de mariscos frescos'
WHERE `id` = 4;

UPDATE `categories` SET 
    `name_en` = 'Special Burritos', 
    `name_es` = 'Burritos Especiales',
    `description_en` = 'Our signature burritos',
    `description_es` = 'Nuestros burritos especiales'
WHERE `id` = 5;

UPDATE `categories` SET 
    `name_en` = 'Combinations', 
    `name_es` = 'Combinaciones',
    `description_en` = 'Perfect meal combinations',
    `description_es` = 'Combinaciones perfectas de comida'
WHERE `id` = 6;

UPDATE `categories` SET 
    `name_en` = 'Tacos & Quesadillas', 
    `name_es` = 'Tacos y Quesadillas',
    `description_en` = 'Traditional tacos and quesadillas',
    `description_es` = 'Tacos y quesadillas tradicionales'
WHERE `id` = 7;

UPDATE `categories` SET 
    `name_en` = 'Desserts', 
    `name_es` = 'Postres',
    `description_en` = 'Sweet endings to your meal',
    `description_es` = 'Finales dulces para tu comida'
WHERE `id` = 8;

UPDATE `categories` SET 
    `name_en` = 'Nachos & Sides', 
    `name_es` = 'Nachos y Acompañamientos',
    `description_en` = 'Appetizers and side dishes',
    `description_es` = 'Aperitivos y acompañamientos'
WHERE `id` = 9;

UPDATE `categories` SET 
    `name_en` = 'Salads & Burgers', 
    `name_es` = 'Ensaladas y Hamburguesas',
    `description_en` = 'Fresh salads and burgers',
    `description_es` = 'Ensaladas frescas y hamburguesas'
WHERE `id` = 10;

-- =============================================
-- 2. LIMPIAR PRODUCTOS EXISTENTES
-- =============================================

-- Eliminar productos existentes para empezar limpio
DELETE FROM `products`;

-- =============================================
-- 3. INSERTAR NUEVOS PRODUCTOS
-- =============================================

-- BREAKFAST PLATES (Category ID: 1)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(1, 'Chorizo Plate', 'Plato de Chorizo', 'Chorizo Scrambled with Eggs', 'Chorizo revuelto con huevos', 11.99, 1, 0, 1),
(1, 'Huevos Rancheros Plate', 'Plato de Huevos Rancheros', 'Eggs with Sauce', 'Huevos con salsa', 11.99, 1, 0, 2),
(1, 'Machaca Plate', 'Plato de Machaca', 'Machaca Scrambled with Eggs', 'Machaca revuelto con huevos', 13.50, 1, 0, 3),
(1, 'Chilaquiles Plate', 'Plato de Chilaquiles', 'Chilaquiles with Rice & Beans (No Tortillas)', 'Chilaquiles con arroz y frijoles (sin tortillas)', 12.50, 1, 0, 4),
(1, 'Huevos a La Mexicana Plate', 'Plato de Huevos a La Mexicana', 'Eggs Mixed with Tomatoes, Peppers & Onions', 'Huevos mezclados con tomates, pimientos y cebollas', 11.99, 1, 0, 5),
(1, 'Steak & Eggs Plate', 'Plato de Bistec y Huevos', 'Steak & 2 Eggs (Any Style)', 'Bistec y 2 huevos (cualquier estilo)', 16.99, 1, 1, 6),
(1, '2 Eggs', '2 Huevos', 'Eggs (Any Style) Rice, Beans & Tortillas', 'Huevos (cualquier estilo) arroz, frijoles y tortillas', 11.50, 1, 0, 7),
(1, '2 Eggs Any Style Plate', 'Plato de 2 Huevos Cualquier Estilo', 'Served with Potatoes & Toast', 'Servido con papas y pan tostado', 11.50, 1, 0, 8),
(1, 'Plain Breakfast Eggs Plate', 'Plato de Huevos de Desayuno Simple', '2 Eggs. Choice of Bacon, Ham or Sausage', '2 huevos. Elección de tocino, jamón o salchicha', 11.50, 1, 0, 9),
(1, 'Deluxe Breakfast Plate', 'Plato de Desayuno Deluxe', '3 Eggs (Any Style) with Bacon, Ham & Sausage. Served with Potatoes & Toast', '3 huevos (cualquier estilo) con tocino, jamón y salchicha. Servido con papas y pan tostado', 13.50, 1, 0, 10),
(1, 'Horchata\'s Veggie Omelet Plate', 'Plato de Omelet Vegetariano de Horchata', 'Bell Peppers, Onion, Tomatoes with & Cheese on Top. Served with Potatoes & Toast', 'Pimientos, cebolla, tomates con queso encima. Servido con papas y pan tostado', 13.50, 1, 0, 11),
(1, 'Horchata\'s Signature Omelet Plate', 'Plato de Omelet Especial de Horchata', 'Eggs, Sausage, Peppers, Onion, Tomatoes & Topped with Cheese. Meat Options: Bacon, Sausage or Ham. Served with Potatoes & Toast', 'Huevos, salchicha, pimientos, cebolla, tomates y cubierto con queso. Opciones de carne: tocino, salchicha o jamón. Servido con papas y pan tostado', 14.99, 1, 1, 12),
(1, 'Croissant Egg Sandwich (Plain)', 'Sandwich de Huevo en Croissant (Simple)', 'Egg, Cheese, Avocado & Sriracha Sauce & Choice of Turkey, Ham or Sausage Patty', 'Huevo, queso, aguacate y salsa sriracha y elección de pavo, jamón o salchicha', 10.50, 1, 0, 13),
(1, 'Brioche Sandwich (Plain)', 'Sandwich de Brioche (Simple)', 'Egg, Cheese, Avocado & Sriracha Sauce & Choice of Turkey, Ham or Sausage Patty', 'Huevo, queso, aguacate y salsa sriracha y elección de pavo, jamón o salchicha', 10.50, 1, 0, 14);

-- BREAKFAST BURRITOS (Category ID: 2)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(2, 'Chapis Burritos', 'Burritos Chapis', '2 Eggs, Potatoes, Cheese & Spanish Sauce, Choice of Protein, Bacon, Sausage or Ham. - Asada or Grilled Chicken extra $2.50', '2 huevos, papas, queso y salsa española, elección de proteína, tocino, salchicha o jamón. - Asada o pollo a la parrilla extra $2.50', 10.50, 1, 0, 1),
(2, 'Chorizo Burrito', 'Burrito de Chorizo', '2 Eggs, Chorizo & Beans', '2 huevos, chorizo y frijoles', 10.99, 1, 0, 2),
(2, 'Machaca Burrito', 'Burrito de Machaca', '2 Eggs, Shredded Beef, Veggie & Beans', '2 huevos, carne deshebrada, verduras y frijoles', 11.99, 1, 0, 3),
(2, 'Egg & Meat Burrito', 'Burrito de Huevo y Carne', 'Bacon, Sausage or Ham', 'Tocino, salchicha o jamón', 7.99, 1, 0, 4),
(2, 'Egg, Rice & Beans Burrito', 'Burrito de Huevo, Arroz y Frijoles', 'Egg, Rice & Beans Burrito', 'Burrito de huevo, arroz y frijoles', 8.50, 1, 0, 5),
(2, 'Egg & Potato Burrito', 'Burrito de Huevo y Papa', 'Egg & Potato Burrito', 'Burrito de huevo y papa', 7.99, 1, 0, 6),
(2, 'Egg, Beans & Cheese Burrito', 'Burrito de Huevo, Frijoles y Queso', 'Egg, Beans & Cheese Burrito', 'Burrito de huevo, frijoles y queso', 8.99, 1, 0, 7),
(2, 'Breakfast Burrito', 'Burrito de Desayuno', 'Eggs, Cheese, Avocado, & Tater Tots, Choice of Turkey, Ham or Sausage Patty', 'Huevos, queso, aguacate y papas fritas, elección de pavo, jamón o salchicha', 12.50, 1, 1, 8);

-- MENUDO (Special - Category ID: 1)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(1, 'Menudo (Saturday & Sunday Only)', 'Menudo (Solo Sábados y Domingos)', 'Served with Corn or Flour Tortillas', 'Servido con tortillas de maíz o harina', 14.50, 1, 0, 15);

-- TACO TUESDAY (Special - Category ID: 7)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(7, '3 Street Tacos Served with Rice & Beans', '3 Tacos Callejeros Servidos con Arroz y Frijoles', 'Served with Rice & Beans with Choice of Protein: Chicken, Al Pastor & Carnitas', 'Servido con arroz y frijoles con elección de proteína: pollo, al pastor y carnitas', 9.00, 1, 0, 1);

-- SIDE ORDERS (Category ID: 9)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'Chile Toreado', 'Chile Toreado', 'Jalapeños Pepper', 'Chile jalapeño', 0.50, 1, 0, 1),
(9, 'Side of Beans (8 oz)', 'Acompañamiento de Frijoles (8 oz)', 'Beans', 'Frijoles', 5.75, 1, 0, 2),
(9, 'Side of Beans (24 oz)', 'Acompañamiento de Frijoles (24 oz)', 'Beans', 'Frijoles', 13.75, 1, 0, 3),
(9, 'Side of Rice (8 oz)', 'Acompañamiento de Arroz (8 oz)', 'Rice', 'Arroz', 5.75, 1, 0, 4),
(9, 'Side of Rice (24 oz)', 'Acompañamiento de Arroz (24 oz)', 'Rice', 'Arroz', 13.75, 1, 0, 5),
(9, 'Cup of Guacamole (4 oz)', 'Taza de Guacamole (4 oz)', 'Guacamole', 'Guacamole', 4.50, 1, 0, 6),
(9, 'Cup of Guacamole (8 oz)', 'Taza de Guacamole (8 oz)', 'Guacamole', 'Guacamole', 9.00, 1, 0, 7),
(9, 'Cup of Sour Cream (4 oz)', 'Taza de Crema Agria (4 oz)', 'Sour Cream', 'Crema agria', 3.75, 1, 0, 8),
(9, 'Cup of Sour Cream (8 oz)', 'Taza de Crema Agria (8 oz)', 'Sour Cream', 'Crema agria', 8.00, 1, 0, 9),
(9, 'Cup of Salsa (8 oz)', 'Taza de Salsa (8 oz)', 'Salsa', 'Salsa', 3.75, 1, 0, 10),
(9, 'Cup of Salsa (16 oz)', 'Taza de Salsa (16 oz)', 'Salsa', 'Salsa', 10.00, 1, 0, 11),
(9, 'Cup of Salsa (24 oz)', 'Taza de Salsa (24 oz)', 'Salsa', 'Salsa', 11.00, 1, 0, 12),
(9, 'Bag of Chips (Small)', 'Bolsa de Chips (Pequeña)', 'Corn Chips', 'Chips de maíz', 3.00, 1, 0, 13),
(9, 'Bag of Chips (Medium)', 'Bolsa de Chips (Mediana)', 'Corn Chips', 'Chips de maíz', 4.00, 1, 0, 14),
(9, 'Bag of Chips (Large)', 'Bolsa de Chips (Grande)', 'Corn Chips', 'Chips de maíz', 7.00, 1, 0, 15),
(9, 'Make Your Burrito Wet', 'Hacer tu Burrito Mojado', 'with Cheese & Choice of Spanish or Enchilada Sauce', 'con queso y elección de salsa española o enchilada', 4.50, 1, 0, 16);

-- DAILY SPECIALS (Category ID: 3)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(3, '3 Soft Tacos', '3 Tacos Suaves', 'Choice of: Al Pastor, Carnitas or Chicken', 'Elección de: al pastor, carnitas o pollo', 14.50, 1, 0, 1),
(3, 'Fajita Plate Chicken or Beef', 'Plato de Fajitas de Pollo o Res', 'Rice, Beans, Salad & Choice of Tortilla', 'Arroz, frijoles, ensalada y elección de tortilla', 16.99, 1, 1, 2),
(3, '3 Tostadas De Ceviche', '3 Tostadas de Ceviche', 'Tostadas de Ceviche', 'Tostadas de ceviche', 16.50, 1, 0, 3),
(3, 'Tostada Supreme', 'Tostada Suprema', 'Meat Choice: Chicken or Beef', 'Elección de carne: pollo o res', 12.99, 1, 0, 4),
(3, 'Macho Burrito', 'Burrito Macho', 'Choice of: Chicken, Carnitas, Al Pastor', 'Elección de: pollo, carnitas, al pastor', 15.50, 1, 1, 5),
(3, '3 Hard Shell Tacos', '3 Tacos de Concha Dura', 'Meat Choice: Chicken, Shredded Beef or Ground Beef', 'Elección de carne: pollo, carne deshebrada o carne molida', 14.50, 1, 0, 6),
(3, '2 Sopes Plate', 'Plato de 2 Sopes', 'Choice of: Chicken, Carnitas or Al Pastor', 'Elección de: pollo, carnitas o al pastor', 15.50, 1, 0, 7),
(3, '3 Fish Or Shrimp Tacos', '3 Tacos de Pescado o Camarón', 'Cabbage, Pico de Gallo & Serrano Sauce', 'Repollo, pico de gallo y salsa serrano', 14.50, 1, 0, 8);

-- SEAFOOD (Category ID: 4)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(4, 'Shrimp Cocktail', 'Cóctel de Camarones', 'Shrimp, Avocado, Pico', 'Camarones, aguacate, pico', 12.50, 1, 0, 1),
(4, 'Fish n\' Chips', 'Pescado y Papas', 'Breaded Fish & Fries', 'Pescado empanizado y papas fritas', 12.50, 1, 0, 2),
(4, 'Pescado a La Plancha', 'Pescado a La Plancha', 'Served with Rice, Salad & Choice of Tortillas', 'Servido con arroz, ensalada y elección de tortillas', 14.50, 1, 0, 3),
(4, 'Taco Fish or Shrimp', 'Taco de Pescado o Camarón', 'Cabbage, Pico de Gallo & Serrano Sauce', 'Repollo, pico de gallo y salsa serrano', 4.75, 1, 0, 4),
(4, 'Camarones Rancheros', 'Camarones Rancheros', 'With Rice, Bean & Salad. Choice of Tortillas', 'Con arroz, frijoles y ensalada. Elección de tortillas', 15.50, 1, 0, 5),
(4, 'Shrimp Fajita Plate', 'Plato de Fajitas de Camarón', 'Mixed with Bell Pepper & Onions. Served with Rice & Beans, & Choice of Tortillas', 'Mezclado con pimiento y cebollas. Servido con arroz y frijoles, y elección de tortillas', 15.50, 1, 0, 6),
(4, 'Tostada de Ceviche', 'Tostada de Ceviche', 'A flat Hardshell Tortilla Topped with Lemon Cured Shrimp & Mixed with Imitation Crab', 'Una tortilla plana dura cubierta con camarones curados en limón y mezclada con cangrejo imitación', 5.50, 1, 0, 7);

-- SPECIAL BURRITOS (Category ID: 5)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(5, 'Macho Burrito', 'Burrito Macho', 'Choice of Meat, Rice, Beans, Lettuce, Tomatoes, Sour Cream, Guacamole. Topped with Spanish Sauce or Enchilada & Cheese', 'Elección de carne, arroz, frijoles, lechuga, tomates, crema agria, guacamole. Cubierto con salsa española o enchilada y queso', 14.50, 1, 1, 1),
(5, 'Super Deluxe Wet Burrito', 'Burrito Mojado Super Deluxe', 'Shredded Chicken, Rice, Beans & Guacamole', 'Pollo deshebrado, arroz, frijoles y guacamole', 12.25, 1, 0, 2),
(5, 'Chicken Fajita Burrito', 'Burrito de Fajitas de Pollo', 'Chicken Fajita Burrito', 'Burrito de fajitas de pollo', 12.50, 1, 0, 3),
(5, 'Beef Fajita Burrito', 'Burrito de Fajitas de Res', 'Beef Fajita Burrito', 'Burrito de fajitas de res', 13.50, 1, 0, 4),
(5, 'Shrimp Fajita Burrito', 'Burrito de Fajitas de Camarón', 'Shrimp Fajita Burrito', 'Burrito de fajitas de camarón', 13.50, 1, 0, 5),
(5, 'Horchata\'s Special', 'Especial de Horchata', 'Chile Colorado, Beans, Lettuce, Guacamole & Cheese', 'Chile colorado, frijoles, lechuga, guacamole y queso', 11.25, 1, 0, 6),
(5, 'Horchata\'s Special All Meat', 'Especial de Horchata Solo Carne', 'Chile Colorado, Lettuce, Guacamole & Cheese', 'Chile colorado, lechuga, guacamole y queso', 11.99, 1, 0, 7),
(5, 'California Burrito', 'Burrito California', 'Steak, Fries, Pico, Guacamole & Cheese', 'Bistec, papas fritas, pico, guacamole y queso', 12.99, 1, 1, 8),
(5, 'Grilled Chicken Burrito', 'Burrito de Pollo a la Parrilla', 'Rice, Beans, Lettuce, Pico & Guacamole', 'Arroz, frijoles, lechuga, pico y guacamole', 13.99, 1, 0, 9),
(5, 'Fish or Shrimp Burrito', 'Burrito de Pescado o Camarón', 'Rice, Cabbage, Pico & Serrano Sauce', 'Arroz, repollo, pico y salsa serrano', 12.50, 1, 0, 10),
(5, 'Famous Avocado Burrito', 'Burrito de Aguacate Famoso', 'Guacamole, Beans, Lettuce & Cheese', 'Guacamole, frijoles, lechuga y queso', 11.99, 1, 0, 11),
(5, 'Sour Cream Burrito', 'Burrito de Crema Agria', 'Sour Cream, Beans, Lettuce & Cheese', 'Crema agria, frijoles, lechuga y queso', 10.50, 1, 0, 12),
(5, 'BRC Burrito (Bean, Rice & Cheese)', 'Burrito BRC (Frijoles, Arroz y Queso)', 'Bean, Rice & Cheese', 'Frijoles, arroz y queso', 8.99, 1, 0, 13),
(5, 'Beef, Bean & Cheese Burrito', 'Burrito de Res, Frijoles y Queso', 'Beef, Bean & Cheese', 'Res, frijoles y queso', 11.99, 1, 0, 14),
(5, 'Beans & Rice Burrito', 'Burrito de Frijoles y Arroz', 'Beans & Rice', 'Frijoles y arroz', 8.75, 1, 0, 15),
(5, 'Beans & Cheese Burrito', 'Burrito de Frijoles y Queso', 'Beans & Cheese', 'Frijoles y queso', 8.75, 1, 0, 16),
(5, 'All Meat Burrito', 'Burrito Solo Carne', 'Asada, Chicken, Al Pastor or Carnitas (No Shrimp)', 'Asada, pollo, al pastor o carnitas (sin camarón)', 13.99, 1, 0, 17);

-- REGULAR BURRITOS (Category ID: 5)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(5, 'Regular Burrito with Rice & Beans', 'Burrito Regular con Arroz y Frijoles', 'With Rice & Beans Inside', 'Con arroz y frijoles adentro', 11.99, 1, 0, 18);

-- COMBINATIONS (Category ID: 6)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(6, '1 Taco', '1 Taco', 'Choice of: Chicken, Shredded or Ground Beef', 'Elección de: pollo, carne deshebrada o carne molida', 10.50, 1, 0, 1),
(6, '1 Enchilada', '1 Enchilada', 'Chicken, Shredded or Ground Beef or Cheese', 'Pollo, carne deshebrada o carne molida o queso', 10.50, 1, 0, 2),
(6, 'Taco & Enchilada Plate', 'Plato de Taco y Enchilada', 'Choice of: Chicken, Shredded or Ground Beef', 'Elección de: pollo, carne deshebrada o carne molida', 13.50, 1, 0, 3),
(6, '2 Hardshell or Softshell Tacos', '2 Tacos de Concha Dura o Suave', 'Choice of Protein. (Shrimp or Fish Extra)', 'Elección de proteína. (Camarón o pescado extra)', 13.99, 1, 0, 4),
(6, '2 Enchiladas', '2 Enchiladas', 'Choice of: Cheese, Shredded Beef, Shredded Chicken, or Ground Beef', 'Elección de: queso, carne deshebrada, pollo deshebrado o carne molida', 13.99, 1, 0, 5),
(6, 'Tamal & Enchilada', 'Tamal y Enchilada', 'Tamal Choices: Chicken, Beef or Pork Enchilada Choice of: Cheese, Shredded Beef, Shredded Chicken, or Ground Beef', 'Opciones de tamal: pollo, res o cerdo. Opciones de enchilada: queso, carne deshebrada, pollo deshebrado o carne molida', 13.99, 1, 0, 6),
(6, 'Chile Relleno & Enchilada', 'Chile Relleno y Enchilada', 'Cheese Stuffed Pepper & Enchilada With Choice of: Ground Beef, Shredded Beef, Shredded Chicken or Cheese', 'Pimiento relleno de queso y enchilada con elección de: carne molida, carne deshebrada, pollo deshebrado o queso', 13.99, 1, 0, 7),
(6, 'Chile Relleno & Tamal Plate', 'Plato de Chile Relleno y Tamal', '(Chicken, Beef or Pork)', '(Pollo, res o cerdo)', 13.99, 1, 0, 8),
(6, 'Chile Colorado (Beef Chunks)', 'Chile Colorado (Trozos de Res)', 'With Corn or Flour Tortillas', 'Con tortillas de maíz o harina', 13.99, 1, 0, 9),
(6, 'Chile Verde (Pork Chunks)', 'Chile Verde (Trozos de Cerdo)', 'With Corn or Flour Tortillas', 'Con tortillas de maíz o harina', 13.99, 1, 0, 10),
(6, '3 Taquitos', '3 Taquitos', 'Beef or Chicken Taquitos with Guacamole', 'Taquitos de res o pollo con guacamole', 13.50, 1, 0, 11),
(6, 'Carnitas Plate', 'Plato de Carnitas', 'Deep Fried Pork, with Salad & Choice of Tortillas', 'Cerdo frito, con ensalada y elección de tortillas', 13.99, 1, 0, 12),
(6, '1 Chile Relleno (Cheese Only)', '1 Chile Relleno (Solo Queso)', 'Chile Relleno & Choice of Tortillas', 'Chile relleno y elección de tortillas', 13.50, 1, 0, 13),
(6, 'Carne Asada Plate', 'Plato de Carne Asada', 'Carne Asada, with Salad & Choice of Tortillas', 'Carne asada, con ensalada y elección de tortillas', 16.99, 1, 1, 14),
(6, 'Chicken Fajita Plate', 'Plato de Fajitas de Pollo', 'Chicken, Bell Pepper & Onion. Served With Salad & Choice of Tortillas. (Asada or Shrimp $2)', 'Pollo, pimiento y cebolla. Servido con ensalada y elección de tortillas. (Asada o camarón $2)', 14.99, 1, 0, 15),
(6, 'Beef Fajita Plate', 'Plato de Fajitas de Res', 'Beef, Pepper, Onion & Salad. Choice of Tortillas', 'Res, pimiento, cebolla y ensalada. Elección de tortillas', 16.99, 1, 1, 16),
(6, 'Grilled Chicken Plate', 'Plato de Pollo a la Parrilla', 'Chicken Breast with Grilled Onions. Served with Salad & Choice of Tortillas', 'Pechuga de pollo con cebollas a la parrilla. Servido con ensalada y elección de tortillas', 14.99, 1, 0, 17);

-- TACOS (Category ID: 7)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(7, 'Soft Taco', 'Taco Suave', 'Choice of Meat Asada, Chicken, Al Pastor, Carnitas, Served with Onions, Cilantro & Salsa', 'Elección de carne asada, pollo, al pastor, carnitas, servido con cebollas, cilantro y salsa', 4.75, 1, 0, 2),
(7, 'Shredded Chicken or Ground Beef Soft Taco', 'Taco Suave de Pollo Deshebrado o Carne Molida', 'Soft Taco, Lettuce, Cheese & Tomatoes Salsa', 'Taco suave, lechuga, queso y tomates salsa', 4.75, 1, 0, 3),
(7, 'Hard Shell Taco', 'Taco de Concha Dura', 'Choice of: Chicken, Shredded or Ground Beef', 'Elección de: pollo, carne deshebrada o carne molida', 4.75, 1, 0, 4),
(7, 'Veggie Taco', 'Taco Vegetariano', 'Black Beans, Lettuce, Cheese & Pico', 'Frijoles negros, lechuga, queso y pico', 4.75, 1, 0, 5);

-- QUESADILLAS (Category ID: 7)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(7, 'Plain Quesadilla (Flour Tortilla)', 'Quesadilla Simple (Tortilla de Harina)', 'Flour Tortilla and Cheese', 'Tortilla de harina y queso', 6.00, 1, 0, 6),
(7, 'Quesadilla Nortena', 'Quesadilla Norteña', 'Corn Tortilla, Cheese, Pico & Guacamole', 'Tortilla de maíz, queso, pico y guacamole', 7.99, 1, 0, 7),
(7, 'Quesadilla Mexicana (Corn Tortilla)', 'Quesadilla Mexicana (Tortilla de Maíz)', 'Meat, Cheese, Pico & Guac. Choice of: Chicken, Carnitas, Al Pastor or Asada', 'Carne, queso, pico y guac. Elección de: pollo, carnitas, al pastor o asada', 8.50, 1, 0, 8),
(7, 'Quesadilla Supreme', 'Quesadilla Suprema', 'Choice of: Chicken, Carnitas, Al Pastor or Asada', 'Elección de: pollo, carnitas, al pastor o asada', 11.50, 1, 0, 9);

-- TOSTADAS (Category ID: 7)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(7, 'Bean Tostada', 'Tostada de Frijoles', 'Beans, Lettuce, Cheese, Fresh Tomatoes & Salsa', 'Frijoles, lechuga, queso, tomates frescos y salsa', 7.99, 1, 0, 10),
(7, 'Tostada with Meat', 'Tostada con Carne', 'Beans, Lettuce, Cheese, Fresh Tomatoes & Salsa, Choice of Meat Chicken, Carnitas, Asada or Al Pastor', 'Frijoles, lechuga, queso, tomates frescos y salsa, elección de carne pollo, carnitas, asada o al pastor', 10.25, 1, 0, 11),
(7, 'Supreme Tostada (Steak or Chicken)', 'Tostada Suprema (Bistec o Pollo)', 'Whole Beans, Lettuce, Tomatoes, Cheese, Guacamole & Sour Cream', 'Frijoles enteros, lechuga, tomates, queso, guacamole y crema agria', 11.99, 1, 0, 12);

-- DESSERTS (Category ID: 8)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(8, 'Churro (1)', 'Churro (1)', 'Churro', 'Churro', 2.25, 1, 0, 1),
(8, 'Churros (4)', 'Churros (4)', '4 Churros', '4 churros', 8.00, 1, 0, 2),
(8, 'Paleta', 'Paleta', 'Paleta', 'Paleta', 3.75, 1, 0, 3),
(8, 'Flan (Slice)', 'Flan (Rebanada)', 'Flan Slice', 'Rebanada de flan', 4.50, 1, 0, 4),
(8, 'Cheesecake (Slice)', 'Cheesecake (Rebanada)', 'Cheesecake Slice', 'Rebanada de cheesecake', 5.25, 1, 0, 5),
(8, 'Chocoflan (Slice)', 'Chocoflan (Rebanada)', 'Chocoflan Slice', 'Rebanada de chocoflan', 5.25, 1, 0, 6);

-- NACHOS (Category ID: 9)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'Nachos (Enchilada Sauce & Cheese)', 'Nachos (Salsa Enchilada y Queso)', 'Nachos (Enchilada Sauce & Cheese)', 'Nachos (salsa enchilada y queso)', 5.99, 1, 0, 17),
(9, 'Super Nachos', 'Super Nachos', 'Tortilla Chips, Beans, Enchilada Sauce, Cheese, Sour Cream, Pico & Guac', 'Chips de tortilla, frijoles, salsa enchilada, queso, crema agria, pico y guac', 10.99, 1, 0, 18),
(9, 'Super Nachos with Asada', 'Super Nachos con Asada', 'Tortilla Chips, Beans, Enchilada Sauce, Sour Cream, Pico & Guac. Choice of Meat Asada, Chicken, Al Pastor or Carnitas', 'Chips de tortilla, frijoles, salsa enchilada, crema agria, pico y guac. Elección de carne asada, pollo, al pastor o carnitas', 13.99, 1, 0, 19);

-- A LA CARTA (Category ID: 9)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'Chile Relleno (Cheese Only)', 'Chile Relleno (Solo Queso)', 'Cheese Only', 'Solo queso', 5.99, 1, 0, 20),
(9, 'Taquitos (Chicken or Beef with Guacamole)', 'Taquitos (Pollo o Res con Guacamole)', 'Chicken or Beef with Guacamole', 'Pollo o res con guacamole', 7.99, 1, 0, 21),
(9, 'Tamale (Beef, Pork or Chicken)', 'Tamal (Res, Cerdo o Pollo)', 'Beef, Pork or Chicken', 'Res, cerdo o pollo', 4.99, 1, 0, 22),
(9, 'Sope (Asada, Al Pastor, Carnitas, Chicken)', 'Sope (Asada, Al Pastor, Carnitas, Pollo)', 'Asada, Al Pastor, Carnitas, Chicken', 'Asada, al pastor, carnitas, pollo', 5.50, 1, 0, 23);

-- BEVERAGES (Category ID: 9)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'Agua Fresca', 'Agua Fresca', 'Horchata, Jamaica or Tamarindo', 'Horchata, jamaica o tamarindo', 3.50, 1, 0, 24),
(9, 'Fountain Drink', 'Bebida de Fuente', 'Fountain Drink', 'Bebida de fuente', 3.50, 1, 0, 25),
(9, 'Mexican Coke or Fanta', 'Coca Mexicana o Fanta', 'Coke or Fanta', 'Coca o Fanta', 4.50, 1, 0, 26),
(9, 'Monster', 'Monster', 'Monster Energy Drink', 'Bebida energética Monster', 3.50, 1, 0, 27),
(9, 'Water Bottle', 'Botella de Agua', 'Water Bottle', 'Botella de agua', 1.50, 1, 0, 28),
(9, 'Juice', 'Jugo', 'Juice', 'Jugo', 3.00, 1, 0, 29);

-- SALADS (Category ID: 10)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(10, 'Green Salad', 'Ensalada Verde', 'Lettuce, Tomatoes, Onions, Cheese. Choice of Dressing: Ranch, Thousand Island, Caesar, Italian', 'Lechuga, tomates, cebollas, queso. Elección de aderezo: ranch, mil islas, caesar, italiano', 6.99, 1, 0, 1),
(10, 'Grilled Chicken Salad', 'Ensalada de Pollo a la Parrilla', 'Lettuce, Tomatoes, Onions, Cheese. Choice of Dressing: Ranch, Thousand Island, Caesar, Italian', 'Lechuga, tomates, cebollas, queso. Elección de aderezo: ranch, mil islas, caesar, italiano', 11.99, 1, 0, 2),
(10, 'Steak or Shrimp Salad', 'Ensalada de Bistec o Camarón', 'Lettuce, Tomatoes, Onions, Cheese. Choice of Dressing: Ranch, Thousand Island, Caesar, Italian', 'Lechuga, tomates, cebollas, queso. Elección de aderezo: ranch, mil islas, caesar, italiano', 14.99, 1, 0, 3);

-- BURGERS (Category ID: 10)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(10, 'Hamburger', 'Hamburguesa', 'Thousand Island, Tomato, Onions, Pickles & Lettuce', 'Mil islas, tomate, cebollas, pepinillos y lechuga', 7.00, 1, 0, 4),
(10, 'Cheeseburger', 'Hamburguesa con Queso', 'Thousand Island, Tomato, Onions, Pickles & Lettuce', 'Mil islas, tomate, cebollas, pepinillos y lechuga', 7.50, 1, 0, 5),
(10, 'Double Cheeseburger', 'Hamburguesa Doble con Queso', 'Thousand Island, Tomato, Onions, Pickles & Lettuce', 'Mil islas, tomate, cebollas, pepinillos y lechuga', 9.99, 1, 1, 6),
(10, 'Avocado Burger', 'Hamburguesa de Aguacate', 'Avocado Burger Pickles, Tomatoes & Lettuce. Mayo, Onions', 'Hamburguesa de aguacate pepinillos, tomates y lechuga. Mayonesa, cebollas', 8.99, 1, 0, 7),
(10, 'Chili Cheeseburger', 'Hamburguesa con Chili y Queso', 'Chili, Onions, Tomatoes & Lettuce', 'Chili, cebollas, tomates y lechuga', 9.99, 1, 0, 8),
(10, 'Chili Cheese Fries', 'Papas con Chili y Queso', 'Chili Cheese Fries', 'Papas con chili y queso', 9.00, 1, 0, 9),
(10, 'Carne Asada Fries', 'Papas con Carne Asada', 'Enchilada Sauce, Cheese, Sour Cream & Guac', 'Salsa enchilada, queso, crema agria y guac', 13.99, 1, 1, 10);

-- COMBO OPTIONS (Category ID: 10)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(10, 'Make It A Combo', 'Hazlo Combo', 'Fountain Drink & Fries Add', 'Bebida de fuente y papas fritas agregar', 6.99, 1, 0, 11);

-- TORTAS (Category ID: 10)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(10, 'Torta', 'Torta', 'Beans, Lettuce, Tomatoes, Onions, Sour Cream & Guacamole (Choice of Meat Chicken, Carnitas, Al Pastor or Asada)', 'Frijoles, lechuga, tomates, cebollas, crema agria y guacamole (elección de carne pollo, carnitas, al pastor o asada)', 13.99, 1, 0, 12);

-- ADD ON'S (Category ID: 9)
INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'ADD ON\'S - Sour Cream OR Guacamole OR Cheese', 'AGREGAR - Crema Agria O Guacamole O Queso', 'Sour cream OR Guacamole OR Cheese - Tacos $0.75, Burritos $2.00', 'Crema agria O guacamole O queso - Tacos $0.75, Burritos $2.00', 0.75, 1, 0, 30),
(9, 'ADD ON\'S - Pico de Gallo OR Lettuce OR Cabbage OR Tomato', 'AGREGAR - Pico de Gallo O Lechuga O Repollo O Tomate', 'Pico de gallo OR Lettuce OR Cabbage OR Tomato - Tacos $0.75, Burritos $2.00', 'Pico de gallo O lechuga O repollo O tomate - Tacos $0.75, Burritos $2.00', 0.75, 1, 0, 31);

COMMIT;
