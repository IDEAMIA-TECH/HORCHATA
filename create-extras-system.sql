-- =============================================
-- Horchata Mexican Food - Sistema de Extras Dinámico
-- Crear tabla para manejar extras de productos
-- =============================================

-- Crear tabla de extras
CREATE TABLE IF NOT EXISTS `product_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(100) NOT NULL,
  `name_es` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de relación entre productos y extras
CREATE TABLE IF NOT EXISTS `product_extra_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_extra` (`product_id`, `extra_id`),
  KEY `product_id` (`product_id`),
  KEY `extra_id` (`extra_id`),
  CONSTRAINT `product_extra_relations_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_extra_relations_extra_fk` FOREIGN KEY (`extra_id`) REFERENCES `product_extras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar extras básicos
INSERT INTO `product_extras` (`name_en`, `name_es`, `price`, `sort_order`, `is_active`) VALUES
('Guacamole', 'Guacamole', 2.50, 1, 1),
('Sour Cream', 'Crema', 2.50, 2, 1),
('Cheese', 'Queso', 2.50, 3, 1),
('Asada', 'Asada', 2.50, 4, 1),
('Grilled Chicken', 'Pollo a la Parrilla', 2.50, 5, 1),
('Shrimp', 'Camarón', 2.00, 6, 1),
('Fish', 'Pescado', 2.00, 7, 1),
('Extra Guacamole (Tacos)', 'Guacamole Extra (Tacos)', 1.00, 8, 1);

-- Insertar relaciones para productos específicos que mencionan estos extras
-- Primero necesitamos encontrar los productos que tienen estos extras en su descripción
-- Esto se hará dinámicamente desde el panel de administración

-- Crear tabla de categorías de extras para organizarlos mejor
CREATE TABLE IF NOT EXISTS `extra_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(100) NOT NULL,
  `name_es` varchar(100) NOT NULL,
  `description_en` text,
  `description_es` text,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías de extras
INSERT INTO `extra_categories` (`name_en`, `name_es`, `description_en`, `description_es`, `sort_order`, `is_active`) VALUES
('Protein Add-ons', 'Agregados de Proteína', 'Additional protein options for your meal', 'Opciones adicionales de proteína para tu comida', 1, 1),
('Sauces & Toppings', 'Salsas y Aderezos', 'Extra sauces and toppings to enhance your meal', 'Salsas y aderezos extra para realzar tu comida', 2, 1),
('Special Additions', 'Agregados Especiales', 'Special additions for specific dishes', 'Agregados especiales para platos específicos', 3, 1);

-- Agregar columna de categoría a la tabla de extras
ALTER TABLE `product_extras` ADD COLUMN `category_id` int(11) DEFAULT NULL AFTER `name_es`;
ALTER TABLE `product_extras` ADD KEY `category_id` (`category_id`);
ALTER TABLE `product_extras` ADD CONSTRAINT `product_extras_category_fk` FOREIGN KEY (`category_id`) REFERENCES `extra_categories` (`id`) ON DELETE SET NULL;

-- Actualizar extras existentes con categorías
UPDATE `product_extras` SET `category_id` = 1 WHERE `name_en` IN ('Asada', 'Grilled Chicken', 'Shrimp', 'Fish');
UPDATE `product_extras` SET `category_id` = 2 WHERE `name_en` IN ('Guacamole', 'Sour Cream', 'Cheese');
UPDATE `product_extras` SET `category_id` = 3 WHERE `name_en` = 'Extra Guacamole (Tacos)';
