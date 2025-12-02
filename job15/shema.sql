-- création de la base et des tables pour job-15
CREATE DATABASE IF NOT EXISTS `draft-shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `draft-shop`;

-- table category
CREATE TABLE IF NOT EXISTS `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `updatedAt` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- table product
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

-- table clothing (héritage : product_id = PK et FK)
CREATE TABLE IF NOT EXISTS `clothing` (
  `product_id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `size` VARCHAR(50),
  `color` VARCHAR(50),
  `type` VARCHAR(50),
  `material_fee` INT UNSIGNED,
  CONSTRAINT `fk_clothing_product` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- table electronic (héritage : product_id = PK et FK)
CREATE TABLE IF NOT EXISTS `electronic` (
  `product_id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `brand` VARCHAR(100),
  `waranty_fee` INT UNSIGNED,
  CONSTRAINT `fk_electronic_product` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;