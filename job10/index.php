<?php

require_once 'category.php';
require_once 'product.php';

// 1) Instanciation sans argument
$productEmpty = new Product();
$categoryEmpty = new Category();

echo "=== Instance vide Product ===\n";
var_dump($productEmpty);

echo "\n=== Instance vide Category ===\n";
var_dump($categoryEmpty);

// 2) Instanciation avec tous les arguments
$createdAt = new DateTime('2025-11-11 10:00:00');
$updatedAt = new DateTime('2025-11-11 12:00:00');

$productFull = new Product(
    7,
    'Chaise design',
    ['image1.jpg', 'image2.jpg'],
    4999,
    'Belle chaise en bois',
    10,
    $createdAt,
    $updatedAt,
    1
);

$categoryFull = new Category(
    1,
    'Meubles',
    'Catégorie pour les meubles',
    $createdAt,
    $updatedAt
);

echo "\n=== Instance complète Product ===\n";
var_dump($productFull);

echo "\n=== Instance complète Category ===\n";
var_dump($categoryFull);

// Exemple : modification via setters 
$productEmpty->setName('Produit créé plus tard');
$productEmpty->setPrice(1234);

echo "\n=== Après modification via setters (instance vide transformée) ===\n";
var_dump($productEmpty->getName());
var_dump($productEmpty->getPrice());