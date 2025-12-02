-- Création de la base et des tables pour le job-03 (draft-shop)
CREATE DATABASE IF NOT EXISTS `draft-shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `draft-shop`;

-- Table category
CREATE TABLE IF NOT EXISTS `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `updatedAt` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table product
CREATE TABLE IF NOT EXISTS `product` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `photos` JSON NOT NULL,
  `price` INT UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `updatedAt` DATETIME NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertions d'exemple pour avoir plusieurs éléments
INSERT INTO `category` (`id`, `name`, `description`, `createdAt`, `updatedAt`) VALUES
(1, 'Meubles', 'Catégorie pour les meubles', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(2, 'Électronique', 'Catégorie pour produits électroniques', '2025-11-02 11:00:00', '2025-11-02 11:00:00'),
(3, 'Vêtements', 'Catégorie pour vêtements', '2025-11-03 12:00:00', '2025-11-03 12:00:00');

INSERT INTO `product` (`id`, `name`, `photos`, `price`, `description`, `quantity`, `createdAt`, `updatedAt`, `category_id`) VALUES
(1, 'Table en chêne', JSON_ARRAY('table1.jpg','table2.jpg'), 12999, 'Table robuste en chêne.', 5, '2025-11-05 09:00:00', '2025-11-05 09:00:00', 1),
(2, 'Lampe de chevet', JSON_ARRAY('lampe1.jpg'), 2999, 'Lampe moderne.', 15, '2025-11-06 10:00:00', '2025-11-06 10:00:00', 1),
(3, 'Smartphone X', JSON_ARRAY('phone1.jpg','phone2.jpg'), 69999, 'Dernier smartphone.', 20, '2025-11-07 08:30:00', '2025-11-07 08:30:00', 2),
(4, 'Casque Audio', JSON_ARRAY('casque1.jpg'), 19999, 'Casque bluetooth.', 12, '2025-11-08 14:00:00', '2025-11-08 14:00:00', 2),
(5, 'T-shirt coton', JSON_ARRAY('tshirt1.jpg'), 1999, 'T-shirt unisexe.', 50, '2025-11-09 16:00:00', '2025-11-09 16:00:00', 3),
(6, 'Jean slim', JSON_ARRAY('jean1.jpg'), 4999, 'Jean slim confortable.', 30, '2025-11-10 10:10:00', '2025-11-10 10:10:00', 3),
(7, 'Chaise design', JSON_ARRAY('image1.jpg','image2.jpg'), 4999, 'Belle chaise en bois', 10, '2025-11-11 10:00:00', '2025-11-11 12:00:00', 1),
(8, 'Table basse', JSON_ARRAY('tb1.jpg'), 7999, 'Table basse contemporaine.', 8, '2025-11-12 09:20:00', '2025-11-12 09:20:00', 1);