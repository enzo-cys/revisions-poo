<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Clothing;
use App\Electronic;

// Exemple d'utilisation minimal
$cloth = new Clothing(null, 'T-shirt', ['img.jpg'], 1500, 'T-shirt cool', 10, new DateTime(), new DateTime(), 2, 'M', 'noir', 'tee', 200);
$res = $cloth->create();
if ($res !== false) {
    echo "Clothing créé avec id: " . $cloth->getId() . PHP_EOL;
    $cloth->addStocks(5);
    echo "Stock après ajout: " . $cloth->getQuantity() . PHP_EOL;
}

$elec = new Electronic(null, 'Casque', ['img2.jpg'], 3999, 'Casque audio', 5, new DateTime(), new DateTime(), 3, 'SuperBrand', 100);
$res2 = $elec->create();
if ($res2 !== false) {
    echo "Electronic créé avec id: " . $elec->getId() . PHP_EOL;
    $elec->removeStocks(2);
    echo "Stock après retrait: " . $elec->getQuantity() . PHP_EOL;
}
