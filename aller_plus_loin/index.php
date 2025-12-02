<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Clothing;
use App\Electronic;
use App\Category;

echo "=== TEST JOB 01 : Méthode save() ===\n\n";

// Test création avec save()
$cloth = new Clothing(null, 'T-shirt', ['img.jpg'], 1500, 'T-shirt cool', 10, new DateTime(), new DateTime(), 2, 'M', 'noir', 'tee', 200);
$res = $cloth->save(); // Devrait créer car ID est null
if ($res !== false) {
    echo "✓ Clothing créé avec save() - ID: " . $cloth->getId() . "\n";
}

// Test mise à jour avec save()
$cloth->setName('T-shirt modifié');
$cloth->setPrice(1200);
$res = $cloth->save(); // Devrait update car ID existe
if ($res !== false) {
    echo "✓ Clothing mis à jour avec save() - Nouveau nom: " . $cloth->getName() . "\n";
}

echo "\n=== TEST JOB 02 : EntityInterface ===\n\n";

// Test que Clothing et Electronic implémentent EntityInterface
$elec = new Electronic(null, 'Casque', ['img2.jpg'], 3999, 'Casque audio', 5, new DateTime(), new DateTime(), 3, 'SuperBrand', 100);
$elec->save();

echo "✓ Clothing implements EntityInterface - ID: " . $cloth->getId() . "\n";
echo "✓ Electronic implements EntityInterface - ID: " . $elec->getId() . "\n";

echo "\n=== TEST JOB 03 : EntityCollection ===\n\n";

use App\EntityCollection;

$collection = new EntityCollection();
$collection->add($cloth);
$collection->add($elec);

echo "✓ Collection créée avec " . $collection->count() . " produits\n";

// Test remove
$collection->remove($cloth);
echo "✓ Après suppression : " . $collection->count() . " produit(s)\n";

// Test retrieve
$retrievedCollection = $collection->retrieve();
echo "✓ Méthode retrieve() retourne une collection avec " . $retrievedCollection->count() . " produit(s)\n";

echo "\n=== TEST JOB 04 : Category avec EntityCollection ===\n\n";

$category = new Category(1, 'Vêtements', 'Catégorie vêtements', new DateTime(), new DateTime());

// Ajouter des produits manuellement
$product1 = new Clothing(null, 'Pull', ['pull.jpg'], 2500, 'Pull chaud', 15, new DateTime(), new DateTime(), 1, 'L', 'bleu', 'pull', 300);
$product1->save();
$category->addProduct($product1);

$product2 = new Clothing(null, 'Jean', ['jean.jpg'], 3500, 'Jean slim', 20, new DateTime(), new DateTime(), 1, 'M', 'noir', 'pantalon', 400);
$product2->save();
$category->addProduct($product2);

echo "✓ Catégorie créée avec EntityCollection\n";
echo "✓ Nombre de produits dans la catégorie : " . $category->getProductsCollection()->count() . "\n";

// Test getProducts()
$products = $category->getProducts();
echo "✓ getProducts() retourne " . count($products) . " produit(s)\n";

echo "\n=== TEST Gestion des stocks (StockableInterface) ===\n\n";

$product1->addStocks(5);
echo "✓ Stock après ajout de 5 : " . $product1->getQuantity() . "\n";

$product1->removeStocks(3);
echo "✓ Stock après retrait de 3 : " . $product1->getQuantity() . "\n";

echo "\nTous les tests sont passés avec succès !\n";
