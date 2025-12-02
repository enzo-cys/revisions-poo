<?php

namespace App;

use App\Abstract\AbstractProduct;
use DateTime;

/**
 * Implémentation générique de Product (optionnelle).
 * Utile pour récupérer/afficher products "neutres".
 */
class Product extends AbstractProduct
{
    public function findOneById(int $id)
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $this->hydrateFromProductRow($row);
        return $this;
    }

    public function findAll(): array
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return [];
        }

        $stmt = $pdo->query('SELECT * FROM product');
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            $p = new Product();
            $p->hydrateFromProductRow($row);
            $items[] = $p;
        }
        return $items;
    }

    public function create()
    {
        $lastId = $this->insertProductRow();
        if ($lastId === false) {
            return false;
        }
        $this->setId($lastId);
        return $this;
    }

    public function update()
    {
        $ok = $this->updateProductRow();
        return $ok ? $this : false;
    }
}