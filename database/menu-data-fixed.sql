-- =============================================
-- Horchata Mexican Food - Datos del Menú (CORREGIDO)
-- Poblar base de datos con productos reales
-- =============================================

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar datos existentes
DELETE FROM order_items;
DELETE FROM orders;
DELETE FROM reviews;
DELETE FROM review_tokens;
DELETE FROM products;
DELETE FROM categories;

-- Rehabilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- 1. INSERTAR CATEGORÍAS PRIMERO
-- =============================================

INSERT INTO `categories` (`id`, `name_en`, `name_es`, `description_en`, `description_es`, `sort_order`, `is_active`) VALUES
(1, 'Breakfast Plates', 'Platos de Desayuno', 'Traditional Mexican breakfast plates', 'Platos de desayuno tradicionales mexicanos', 1, 1),
(2, 'Breakfast Burritos', 'Burritos de Desayuno', 'Hearty breakfast burritos', 'Burritos de desayuno sustanciosos', 2, 1),
(3, 'Daily Specials', 'Especiales del Día', 'Chef\'s daily specials', 'Especiales del chef del día', 3, 1),
(4, 'Seafood', 'Mariscos', 'Fresh seafood dishes', 'Platos de mariscos frescos', 4, 1),
(5, 'Special Burritos', 'Burritos Especiales', 'Our signature burritos', 'Nuestros burritos especiales', 5, 1),
(6, 'Combinations', 'Combinaciones', 'Perfect meal combinations', 'Combinaciones perfectas de comida', 6, 1),
(7, 'Tacos & Quesadillas', 'Tacos y Quesadillas', 'Traditional tacos and quesadillas', 'Tacos y quesadillas tradicionales', 7, 1),
(8, 'Desserts', 'Postres', 'Sweet endings to your meal', 'Finales dulces para tu comida', 8, 1),
(9, 'Nachos & Sides', 'Nachos y Acompañamientos', 'Appetizers and side dishes', 'Aperitivos y acompañamientos', 9, 1),
(10, 'Salads & Burgers', 'Ensaladas y Hamburguesas', 'Fresh salads and burgers', 'Ensaladas frescas y hamburguesas', 10, 1);

-- =============================================
-- 2. BREAKFAST PLATES (category_id = 1)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(1, 'Chorizo Plate', 'Plato de Chorizo', 'Chorizo scrambled with eggs', 'Chorizo revuelto con huevos', 11.99, 1, 0, 1),
(1, 'Huevos Rancheros Plate', 'Plato de Huevos Rancheros', 'Eggs with sauce', 'Huevos con salsa', 11.99, 1, 1, 2),
(1, 'Machaca Plate', 'Plato de Machaca', 'Shredded beef scrambled with eggs', 'Carne deshebrada revuelta con huevos', 13.50, 1, 0, 3),
(1, 'Chilaquiles Plate', 'Plato de Chilaquiles', 'With rice & beans (no tortillas)', 'Con arroz y frijoles (sin tortillas)', 12.50, 1, 1, 4),
(1, 'Huevos a la Mexicana', 'Huevos a la Mexicana', 'Eggs with tomato, peppers & onion', 'Huevos con tomate, chiles y cebolla', 11.99, 1, 0, 5),
(1, 'Steak & Eggs Plate', 'Plato de Bistec y Huevos', 'Steak with 2 eggs any style', 'Bistec con 2 huevos al gusto', 16.99, 1, 1, 6),
(1, '2 Eggs Plate', 'Plato de 2 Huevos', 'Eggs any style, rice, beans & tortillas', 'Huevos al gusto, arroz, frijoles y tortillas', 11.50, 1, 0, 7),
(1, '2 Eggs any Style Plate', 'Plato de 2 Huevos al Gusto', 'With potatoes & toast', 'Con papas y pan tostado', 11.50, 1, 0, 8),
(1, 'Plain Breakfast Eggs', 'Huevos de Desayuno Simple', '2 eggs + bacon, ham or sausage', '2 huevos + tocino, jamón o salchicha', 11.50, 1, 0, 9),
(1, 'Deluxe Breakfast', 'Desayuno de Lujo', '3 eggs + bacon, ham & sausage', '3 huevos + tocino, jamón y salchicha', 13.50, 1, 0, 10),
(1, 'Veggie Omelet', 'Omelet Vegetariano', 'Peppers, onions, tomatoes & cheese', 'Chiles, cebollas, tomates y queso', 13.50, 1, 0, 11),
(1, 'Signature Omelet', 'Omelet Especial', 'Eggs, sausage, peppers, onion, tomatoes & cheese', 'Huevos, salchicha, chiles, cebolla, tomates y queso', 14.99, 1, 1, 12),
(1, 'Croissant Egg Sandwich', 'Sandwich de Huevo en Croissant', 'Egg, cheese, avocado, sriracha + protein', 'Huevo, queso, aguacate, sriracha + proteína', 10.50, 1, 0, 13),
(1, 'Brioche Sandwich', 'Sandwich de Brioche', 'Egg, cheese, avocado, sriracha + protein', 'Huevo, queso, aguacate, sriracha + proteína', 10.50, 1, 0, 14);

-- =============================================
-- 3. BREAKFAST BURRITOS (category_id = 2)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(2, 'Chapis Burrito', 'Burrito Chapis', '2 eggs, potatoes, cheese, Spanish sauce + protein', '2 huevos, papas, queso, salsa española + proteína', 11.50, 1, 1, 1),
(2, 'Chorizo Burrito', 'Burrito de Chorizo', '2 eggs, chorizo & beans', '2 huevos, chorizo y frijoles', 10.99, 1, 0, 2),
(2, 'Machaca Burrito', 'Burrito de Machaca', '2 eggs, shredded beef, veggies & beans', '2 huevos, carne deshebrada, verduras y frijoles', 11.99, 1, 0, 3),
(2, 'Egg & Meat Burrito', 'Burrito de Huevo y Carne', 'Bacon, sausage or ham', 'Tocino, salchicha o jamón', 7.99, 1, 0, 4),
(2, 'Egg, Rice & Beans', 'Huevo, Arroz y Frijoles', 'Traditional combination', 'Combinación tradicional', 8.50, 1, 0, 5),
(2, 'Egg & Potato', 'Huevo y Papa', 'Simple and delicious', 'Simple y delicioso', 7.99, 1, 0, 6),
(2, 'Egg, Beans & Cheese', 'Huevo, Frijoles y Queso', 'Classic combination', 'Combinación clásica', 8.99, 1, 0, 7),
(2, 'Breakfast Burrito', 'Burrito de Desayuno', 'Eggs, cheese, avocado & tater tots', 'Huevos, queso, aguacate y papas fritas', 12.50, 1, 1, 8),
(2, 'Menudo (Sat–Sun only)', 'Menudo (Sáb–Dom únicamente)', 'Served with tortillas', 'Servido con tortillas', 14.50, 1, 0, 9);

-- =============================================
-- 4. DAILY SPECIALS (category_id = 3)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(3, '3 Soft Tacos', '3 Tacos Suaves', 'Al Pastor, Carnitas, Chicken', 'Al Pastor, Carnitas, Pollo', 14.50, 1, 1, 1),
(3, 'Fajita Plate (Chicken/Beef)', 'Plato de Fajitas (Pollo/Res)', 'Rice, beans, salad & tortillas', 'Arroz, frijoles, ensalada y tortillas', 16.99, 1, 1, 2),
(3, '3 Tostadas de Ceviche', '3 Tostadas de Ceviche', 'Fresh seafood tostadas', 'Tostadas de mariscos frescos', 16.50, 1, 0, 3),
(3, 'Tostada Supreme', 'Tostada Suprema', 'Chicken or Beef', 'Pollo o Res', 12.99, 1, 0, 4),
(3, 'Macho Burrito', 'Burrito Macho', 'Chicken, Carnitas, Al Pastor', 'Pollo, Carnitas, Al Pastor', 15.50, 1, 1, 5),
(3, '3 Hard Shell Tacos', '3 Tacos Duros', 'Chicken, Shredded Beef, Ground Beef', 'Pollo, Carne Deshebrada, Carne Molida', 14.50, 1, 0, 6),
(3, '2 Sopes Plate', 'Plato de 2 Sopes', 'Chicken, Carnitas, Al Pastor', 'Pollo, Carnitas, Al Pastor', 15.50, 1, 0, 7),
(3, '3 Fish/Shrimp Tacos', '3 Tacos de Pescado/Camarón', 'With cabbage, pico, serrano', 'Con repollo, pico, serrano', 14.50, 1, 0, 8);

-- =============================================
-- 5. SEAFOOD (category_id = 4)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(4, 'Shrimp Cocktail', 'Cóctel de Camarón', 'Shrimp, avocado, pico', 'Camarón, aguacate, pico', 12.50, 1, 0, 1),
(4, 'Fish n\' Chips', 'Pescado y Papas', 'Breaded fish & fries', 'Pescado empanizado y papas fritas', 12.50, 1, 0, 2),
(4, 'Pescado a la Plancha', 'Pescado a la Plancha', 'With rice, salad & tortillas', 'Con arroz, ensalada y tortillas', 14.50, 1, 1, 3),
(4, 'Taco Fish/Shrimp', 'Taco de Pescado/Camarón', 'With cabbage, pico, serrano', 'Con repollo, pico, serrano', 4.75, 1, 0, 4),
(4, 'Camarones Rancheros', 'Camarones Rancheros', 'With rice, beans & salad', 'Con arroz, frijoles y ensalada', 15.50, 1, 0, 5),
(4, 'Shrimp Fajita Plate', 'Plato de Fajitas de Camarón', 'With peppers, onions, rice, beans', 'Con chiles, cebollas, arroz, frijoles', 15.50, 1, 0, 6),
(4, 'Tostada de Ceviche', 'Tostada de Ceviche', 'Lemon-cured shrimp & imitation crab', 'Camarón curado con limón e imitación de cangrejo', 5.50, 1, 0, 7);

-- =============================================
-- 6. SPECIAL BURRITOS (category_id = 5)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(5, 'Macho Burrito', 'Burrito Macho', 'Choice of meat, rice, beans, lettuce, guac', 'Elección de carne, arroz, frijoles, lechuga, guac', 14.50, 1, 1, 1),
(5, 'Super Deluxe Wet Burrito', 'Burrito Mojado Super Deluxe', 'Shredded chicken, rice, beans & guac', 'Pollo deshebrado, arroz, frijoles y guac', 12.25, 1, 0, 2),
(5, 'Chicken Fajita Burrito', 'Burrito de Fajitas de Pollo', 'Grilled chicken with vegetables', 'Pollo a la parrilla con verduras', 12.50, 1, 0, 3),
(5, 'Beef Fajita Burrito', 'Burrito de Fajitas de Res', 'Grilled beef with vegetables', 'Res a la parrilla con verduras', 13.50, 1, 0, 4),
(5, 'Shrimp Fajita Burrito', 'Burrito de Fajitas de Camarón', 'Grilled shrimp with vegetables', 'Camarón a la parrilla con verduras', 13.50, 1, 0, 5),
(5, 'Horchata\'s Special', 'Especial de Horchata', 'Chile colorado, beans, lettuce, guac, cheese', 'Chile colorado, frijoles, lechuga, guac, queso', 11.25, 1, 1, 6),
(5, 'Horchata\'s Special (All Meats)', 'Especial de Horchata (Todas las Carnes)', 'Chile colorado, lettuce, guac, cheese', 'Chile colorado, lechuga, guac, queso', 11.99, 1, 0, 7),
(5, 'California Burrito', 'Burrito California', 'Steak, fries, pico, guac, cheese', 'Bistec, papas fritas, pico, guac, queso', 12.99, 1, 1, 8),
(5, 'Grilled Chicken Burrito', 'Burrito de Pollo a la Parrilla', 'Rice, beans, lettuce, pico, guac', 'Arroz, frijoles, lechuga, pico, guac', 13.99, 1, 0, 9),
(5, 'Fish/Shrimp Burrito', 'Burrito de Pescado/Camarón', 'Rice, cabbage, pico, serrano sauce', 'Arroz, repollo, pico, salsa serrano', 12.50, 1, 0, 10),
(5, 'Avocado Burrito', 'Burrito de Aguacate', 'Guacamole, beans, lettuce, cheese', 'Guacamole, frijoles, lechuga, queso', 11.99, 1, 0, 11),
(5, 'Sour Cream Burrito', 'Burrito de Crema Agria', 'Sour cream, beans, lettuce, cheese', 'Crema agria, frijoles, lechuga, queso', 10.50, 1, 0, 12),
(5, 'BRC Burrito', 'Burrito BRC', 'Bean, rice & cheese', 'Frijoles, arroz y queso', 8.99, 1, 0, 13),
(5, 'Beef, Bean & Cheese', 'Res, Frijoles y Queso', 'Classic combination', 'Combinación clásica', 11.99, 1, 0, 14),
(5, 'Beans & Rice Burrito', 'Burrito de Frijoles y Arroz', 'Simple and delicious', 'Simple y delicioso', 8.75, 1, 0, 15),
(5, 'Beans & Cheese Burrito', 'Burrito de Frijoles y Queso', 'Vegetarian option', 'Opción vegetariana', 8.75, 1, 0, 16),
(5, 'All Meat Burrito', 'Burrito de Pura Carne', 'Asada, Chicken, Al Pastor or Carnitas', 'Asada, Pollo, Al Pastor o Carnitas', 13.99, 1, 0, 17);

-- =============================================
-- 7. COMBINATIONS (category_id = 6)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(6, '1 Taco', '1 Taco', 'Chicken, Shredded or Ground Beef', 'Pollo, Carne Deshebrada o Molida', 10.50, 1, 0, 1),
(6, '1 Enchilada', '1 Enchilada', 'Chicken, Beef, Cheese', 'Pollo, Res, Queso', 10.50, 1, 0, 2),
(6, 'Taco & Enchilada', 'Taco y Enchilada', 'Combo plate', 'Plato combinado', 13.50, 1, 1, 3),
(6, '2 Tacos', '2 Tacos', 'Soft or Hard Shell', 'Suave o Duro', 13.99, 1, 0, 4),
(6, '2 Enchiladas', '2 Enchiladas', 'Cheese, Beef, Chicken', 'Queso, Res, Pollo', 13.99, 1, 0, 5),
(6, 'Tamal & Enchilada', 'Tamal y Enchilada', 'Chicken, Beef, Pork', 'Pollo, Res, Cerdo', 13.99, 1, 0, 6),
(6, 'Chile Relleno & Enchilada', 'Chile Relleno y Enchilada', 'Cheese-stuffed pepper + enchilada', 'Chile relleno de queso + enchilada', 13.99, 1, 1, 7),
(6, 'Chile Relleno & Tamal', 'Chile Relleno y Tamal', 'Traditional combination', 'Combinación tradicional', 13.99, 1, 0, 8),
(6, 'Chile Colorado', 'Chile Colorado', 'Beef chunks, tortillas', 'Trozos de res, tortillas', 13.99, 1, 0, 9),
(6, 'Chile Verde', 'Chile Verde', 'Pork chunks, tortillas', 'Trozos de cerdo, tortillas', 13.99, 1, 0, 10),
(6, '3 Taquitos', '3 Taquitos', 'Chicken or beef + guacamole', 'Pollo o res + guacamole', 13.50, 1, 0, 11),
(6, 'Carnitas Plate', 'Plato de Carnitas', 'Deep fried pork + salad + tortillas', 'Cerdo frito + ensalada + tortillas', 13.99, 1, 1, 12),
(6, '1 Chile Relleno', '1 Chile Relleno', 'Cheese only + tortillas', 'Solo queso + tortillas', 13.50, 1, 0, 13),
(6, 'Carne Asada Plate', 'Plato de Carne Asada', 'Salad & tortillas', 'Ensalada y tortillas', 16.99, 1, 1, 14),
(6, 'Chicken Fajita Plate', 'Plato de Fajitas de Pollo', 'Peppers & onions + salad', 'Chiles y cebollas + ensalada', 14.99, 1, 0, 15),
(6, 'Beef Fajita Plate', 'Plato de Fajitas de Res', 'Peppers, onions + salad', 'Chiles, cebollas + ensalada', 16.99, 1, 0, 16),
(6, 'Grilled Chicken Plate', 'Plato de Pollo a la Parrilla', 'Grilled onions + salad', 'Cebollas a la parrilla + ensalada', 14.99, 1, 0, 17);

-- =============================================
-- 8. TACOS & QUESADILLAS (category_id = 7)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(7, 'Taco (Soft/Hard/Veggie)', 'Taco (Suave/Duro/Vegetariano)', 'Carne o vegetariano', 'Meat or vegetarian', 4.75, 1, 0, 1),
(7, 'Plain Quesadilla', 'Quesadilla Simple', 'Flour tortilla, cheese', 'Tortilla de harina, queso', 6.00, 1, 0, 2),
(7, 'Quesadilla Norteña', 'Quesadilla Norteña', 'Corn tortilla, cheese, pico, guac', 'Tortilla de maíz, queso, pico, guac', 7.99, 1, 0, 3),
(7, 'Quesadilla Mexicana', 'Quesadilla Mexicana', 'Meat, cheese, pico, guac', 'Carne, queso, pico, guac', 8.50, 1, 1, 4),
(7, 'Quesadilla Supreme', 'Quesadilla Suprema', 'Choice of meat', 'Elección de carne', 11.50, 1, 0, 5),
(7, 'Bean Tostada', 'Tostada de Frijoles', 'Lettuce, cheese, tomato', 'Lechuga, queso, tomate', 7.99, 1, 0, 6),
(7, 'Meat Tostada', 'Tostada de Carne', 'With chicken, carnitas, asada or pastor', 'Con pollo, carnitas, asada o pastor', 10.25, 1, 0, 7),
(7, 'Supreme Tostada', 'Tostada Suprema', 'Steak or chicken + guac & sour cream', 'Bistec o pollo + guac y crema agria', 11.99, 1, 1, 8);

-- =============================================
-- 9. DESSERTS (category_id = 8)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(8, 'Churro (1)', 'Churro (1)', 'Traditional Mexican churro', 'Churro mexicano tradicional', 2.25, 1, 0, 1),
(8, 'Churros (4)', 'Churros (4)', 'Four traditional churros', 'Cuatro churros tradicionales', 8.00, 1, 1, 2),
(8, 'Paleta', 'Paleta', 'Mexican ice pop', 'Paleta mexicana', 3.75, 1, 0, 3),
(8, 'Flan', 'Flan', 'Traditional Mexican flan', 'Flan mexicano tradicional', 4.50, 1, 1, 4),
(8, 'Cheesecake', 'Cheesecake', 'Creamy cheesecake', 'Cheesecake cremoso', 5.25, 1, 0, 5),
(8, 'Chocoflan', 'Chocoflan', 'Chocolate flan', 'Flan de chocolate', 5.25, 1, 1, 6);

-- =============================================
-- 10. NACHOS & SIDES (category_id = 9)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(9, 'Nachos', 'Nachos', 'Crispy tortilla chips with cheese', 'Totopos crujientes con queso', 5.99, 1, 0, 1),
(9, 'Super Nachos', 'Super Nachos', 'Loaded with toppings', 'Cargados con ingredientes', 10.99, 1, 1, 2),
(9, 'Super Nachos c/ Carne', 'Super Nachos con Carne', 'Loaded with meat and toppings', 'Cargados con carne e ingredientes', 13.99, 1, 0, 3),
(9, 'Chile Relleno (Ala Carta)', 'Chile Relleno (A la Carta)', 'Cheese-stuffed pepper', 'Chile relleno de queso', 5.99, 1, 0, 4),
(9, 'Taquitos (c/ Guac)', 'Taquitos (con Guac)', 'Rolled tacos with guacamole', 'Taquitos enrollados con guacamole', 7.99, 1, 0, 5),
(9, 'Tamale', 'Tamal', 'Traditional Mexican tamale', 'Tamal mexicano tradicional', 4.99, 1, 1, 6),
(9, 'Sope', 'Sope', 'Traditional Mexican sope', 'Sope mexicano tradicional', 5.50, 1, 0, 7),
(9, 'Side Beans 8oz', 'Frijoles 8oz', 'Refried beans side', 'Frijoles refritos', 5.75, 1, 0, 8),
(9, 'Side Rice 8oz', 'Arroz 8oz', 'Mexican rice side', 'Arroz mexicano', 5.75, 1, 0, 9),
(9, 'Guacamole 8oz', 'Guacamole 8oz', 'Fresh guacamole', 'Guacamole fresco', 9.00, 1, 1, 10),
(9, 'Sour Cream 8oz', 'Crema Agria 8oz', 'Fresh sour cream', 'Crema agria fresca', 8.00, 1, 0, 11),
(9, 'Salsa 8oz', 'Salsa 8oz', 'Fresh salsa', 'Salsa fresca', 5.00, 1, 0, 12),
(9, 'Chips Bag', 'Bolsa de Totopos', 'Crispy tortilla chips', 'Totopos crujientes', 3.00, 1, 0, 13),
(9, 'Add Cheese/Sauce', 'Agregar Queso/Salsa', 'Extra cheese or sauce', 'Queso o salsa extra', 4.50, 1, 0, 14);

-- =============================================
-- 11. SALADS & BURGERS (category_id = 10)
-- =============================================

INSERT INTO `products` (`category_id`, `name_en`, `name_es`, `description_en`, `description_es`, `price`, `is_available`, `is_featured`, `sort_order`) VALUES
(10, 'Green Salad', 'Ensalada Verde', 'Fresh mixed greens', 'Mezcla de verduras frescas', 6.99, 1, 0, 1),
(10, 'Grilled Chicken Salad', 'Ensalada de Pollo a la Parrilla', 'Grilled chicken with mixed greens', 'Pollo a la parrilla con verduras mixtas', 11.99, 1, 1, 2),
(10, 'Steak or Shrimp Salad', 'Ensalada de Bistec o Camarón', 'Your choice of protein', 'Tu elección de proteína', 14.99, 1, 0, 3),
(10, 'Hamburger', 'Hamburguesa', 'Classic hamburger', 'Hamburguesa clásica', 7.00, 1, 0, 4),
(10, 'Cheeseburger', 'Hamburguesa con Queso', 'Hamburger with cheese', 'Hamburguesa con queso', 7.50, 1, 0, 5),
(10, 'Double Cheeseburger', 'Hamburguesa Doble con Queso', 'Double patty with cheese', 'Doble carne con queso', 9.99, 1, 1, 6),
(10, 'Avocado Burger', 'Hamburguesa de Aguacate', 'Burger with fresh avocado', 'Hamburguesa con aguacate fresco', 8.99, 1, 0, 7),
(10, 'Chili Cheeseburger', 'Hamburguesa con Chile y Queso', 'Burger with chili and cheese', 'Hamburguesa con chile y queso', 9.99, 1, 0, 8),
(10, 'French Fries', 'Papas Fritas', 'Crispy french fries', 'Papas fritas crujientes', 5.50, 1, 0, 9),
(10, 'Chili Cheese Fries', 'Papas con Chile y Queso', 'Fries with chili and cheese', 'Papas con chile y queso', 9.00, 1, 1, 10),
(10, 'Asada Fries', 'Papas Asadas', 'Fries with carne asada', 'Papas con carne asada', 13.99, 1, 1, 11),
(10, 'Torta', 'Torta', 'Mexican sandwich', 'Sandwich mexicano', 13.99, 1, 0, 12),
(10, 'Agua Fresca (R/L)', 'Agua Fresca (R/L)', 'Fresh fruit water', 'Agua de fruta fresca', 3.50, 1, 0, 13),
(10, 'Fountain Drink (R/L)', 'Bebida de Fuente (R/L)', 'Soft drink', 'Refresco', 3.50, 1, 0, 14),
(10, 'Mexican Coke/Fanta', 'Coca/Fanta Mexicana', 'Mexican soft drinks', 'Refrescos mexicanos', 4.50, 1, 1, 15),
(10, 'Monster', 'Monster', 'Energy drink', 'Bebida energética', 3.50, 1, 0, 16),
(10, 'Juice', 'Jugo', 'Fresh fruit juice', 'Jugo de fruta fresca', 3.00, 1, 0, 17),
(10, 'Water Bottle', 'Botella de Agua', 'Bottled water', 'Agua embotellada', 1.50, 1, 0, 18),
(10, 'Coffee', 'Café', 'Fresh brewed coffee', 'Café recién preparado', 2.00, 1, 0, 19);

-- =============================================
-- 12. CREAR USUARIO ADMIN
-- =============================================

INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `role`, `is_active`) VALUES
('admin', 'admin@horchatamexicanfood.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1);

-- =============================================
-- 13. CONFIGURACIONES BÁSICAS
-- =============================================

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'Horchata Mexican Food', 'Nombre del sitio'),
('site_description', 'Authentic Mexican Food Restaurant', 'Descripción del sitio'),
('currency', 'USD', 'Moneda del sistema'),
('tax_rate', '8.25', 'Tasa de impuestos'),
('timezone', 'America/Los_Angeles', 'Zona horaria'),
('language', 'en', 'Idioma por defecto'),
('email_notifications', '1', 'Notificaciones por email'),
('order_notifications', '1', 'Notificaciones de pedidos');

-- =============================================
-- FINALIZAR
-- =============================================

-- Mostrar resumen
SELECT 
    c.name_en as 'Categoría',
    COUNT(p.id) as 'Productos',
    MIN(p.price) as 'Precio Mín',
    MAX(p.price) as 'Precio Máx',
    ROUND(AVG(p.price), 2) as 'Precio Promedio'
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
GROUP BY c.id, c.name_en
ORDER BY c.sort_order;
