<?php

require_once 'product.php';
require_once 'category.php';

// === CONFIGURE ===
$host = 'localhost';
$port = '3306';
$db   = 'draft-shop';
$user = 'root';     
$pass = '';          
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "Connexion DB échouée : " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Récupérer le produit id = 7 
$id = 7;
$stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    echo "Produit avec id = $id non trouvé." . PHP_EOL;
    exit(0);
}

// Hydrater une instance Product avec les données retournées
$product = new Product(); // constructeur optionnel utilisé
$product->setId((int)$row['id']);
$product->setName($row['name'] ?? null);

// photos stockées en JSON dans la base -> décoder en tableau
$photos = [];
if (!empty($row['photos'])) {
    $decoded = json_decode($row['photos'], true);
    if (is_array($decoded)) {
        $photos = $decoded;
    }
}
$product->setPhotos($photos);

$product->setPrice(isset($row['price']) ? (int)$row['price'] : null);
$product->setDescription($row['description'] ?? null);
$product->setQuantity(isset($row['quantity']) ? (int)$row['quantity'] : null);

$product->setCreatedAt(!empty($row['createdAt']) ? new DateTime($row['createdAt']) : null);
$product->setUpdatedAt(!empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null);

$product->setCategoryId(isset($row['category_id']) ? (int)$row['category_id'] : null);

// Afficher l'instance hydratée
var_dump($product);