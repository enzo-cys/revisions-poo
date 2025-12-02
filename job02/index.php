<?php

require_once 'category.php';
require_once 'product.php';

$createdAt = new DateTime('2025-12-01 10:00:00');
$updatedAt = new DateTime('2025-12-01 12:00:00');

// Exemple : création d une catégorie
$category = new Category(
    1,
    'Meubles',
    'Catégorie pour les meubles',
    $createdAt,
    $updatedAt
);

// Exemple: création d'un produit lié à la catégorie ci-dessus via category_id
$product = new Product(
    1,
    'Chaise design',
    ['image1.jpg', 'image2.jpg'],
    4999,
    'Belle chaise en bois',
    10,
    $createdAt,
    $updatedAt,
    $category->getId()
);

// Tests avec var_dump des getters
var_dump($category->getId());
var_dump($category->getName());
var_dump($product->getCategoryId());
var_dump($product->getName());