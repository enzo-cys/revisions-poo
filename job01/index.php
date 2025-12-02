<?php

require_once 'product.php';

$createdAt = new DateTime('2025-12-01 10:00:00');
$updatedAt = new DateTime('2025-12-01 12:00:00');

$product = new Product(
    1,
    'Chaise design',
    ['image1.jpg', 'image2.jpg'],
    4999,
    'Belle chaise en bois',
    10,
    $createdAt,
    $updatedAt
);

// Récupérer et afficher les propriétés via getters
var_dump($product->getId());
var_dump($product->getName());
var_dump($product->getPhotos());
var_dump($product->getPrice());
var_dump($product->getDescription());
var_dump($product->getQuantity());
var_dump($product->getCreatedAt());
var_dump($product->getUpdatedAt());

// Modifier quelques valeurs via setters puis afficher à nouveau
$product->setName('Chaise modifiée');
$product->setPrice(4599);

var_dump($product->getName());
var_dump($product->getPrice());