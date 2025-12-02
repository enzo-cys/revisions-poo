<?php

namespace App;

use App\Abstract\AbstractProduct;
use App\Interface\StockableInterface;
use DateTime;
use PDO;
use PDOException;

/**
 * Classe Electronic étend AbstractProduct et gère les détails "electronic".
 */
class Electronic extends AbstractProduct implements StockableInterface
{
    private ?string $brand;
    private ?int $waranty_fee;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?array $photos = null,
        ?int $price = null,
        ?string $description = null,
        ?int $quantity = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?int $category_id = null,
        ?string $brand = null,
        ?int $waranty_fee = null
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $createdAt, $updatedAt, $category_id);
        $this->brand = $brand;
        $this->waranty_fee = $waranty_fee;
    }

    // Getters / setters
    public function getBrand(): ?string { return $this->brand; }
    public function setBrand(?string $brand): void { $this->brand = $brand; }

    public function getWarantyFee(): ?int { return $this->waranty_fee; }
    public function setWarantyFee(?int $waranty_fee): void { $this->waranty_fee = $waranty_fee; }

    // Implémentation de StockableInterface -------------------------------

    public function addStocks(int $stock): self
    {
        if ($stock <= 0) {
            return $this;
        }
        $current = $this->quantity ?? 0;
        $this->quantity = $current + $stock;
        return $this;
    }

    public function removeStocks(int $stock): self
    {
        if ($stock <= 0) {
            return $this;
        }
        $current = $this->quantity ?? 0;
        $this->quantity = max(0, $current - $stock);
        return $this;
    }

    // Méthodes CRUD spécifiques aux Electronic ---------------------------

    public function findOneById(int $id)
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $sql = 'SELECT p.*, e.brand AS e_brand, e.waranty_fee AS e_waranty_fee
                FROM product p
                LEFT JOIN electronic e ON e.product_id = p.id
                WHERE p.id = :id
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        return new Electronic(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['name'] ?? null,
            !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
            isset($row['price']) ? (int)$row['price'] : null,
            $row['description'] ?? null,
            isset($row['quantity']) ? (int)$row['quantity'] : null,
            !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null,
            !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null,
            isset($row['category_id']) ? (int)$row['category_id'] : null,
            $row['e_brand'] ?? null,
            isset($row['e_waranty_fee']) ? (int)$row['e_waranty_fee'] : null
        );
    }

    public function findAll(): array
    {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return [];
        }

        $sql = 'SELECT p.*, e.brand AS e_brand, e.waranty_fee AS e_waranty_fee
                FROM product p
                LEFT JOIN electronic e ON e.product_id = p.id';
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            $items[] = new Electronic(
                isset($row['id']) ? (int)$row['id'] : null,
                $row['name'] ?? null,
                !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
                isset($row['price']) ? (int)$row['price'] : null,
                $row['description'] ?? null,
                isset($row['quantity']) ? (int)$row['quantity'] : null,
                !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null,
                !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null,
                isset($row['category_id']) ? (int)$row['category_id'] : null,
                $row['e_brand'] ?? null,
                isset($row['e_waranty_fee']) ? (int)$row['e_waranty_fee'] : null
            );
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

        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $sql = 'INSERT INTO electronic (product_id, brand, waranty_fee)
                VALUES (:pid, :brand, :waranty_fee)';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':pid', $lastId, PDO::PARAM_INT);
            $stmt->bindValue(':brand', $this->brand, $this->brand !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':waranty_fee', $this->waranty_fee !== null ? (int)$this->waranty_fee : null, $this->waranty_fee !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $executed = $stmt->execute();
            if ($executed === false) {
                return false;
            }
            return $this;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update()
    {
        if ($this->getId() === null) {
            return false;
        }

        $ok = $this->updateProductRow();
        if ($ok === false) {
            return false;
        }

        $productId = $this->getId();
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $check = $pdo->prepare('SELECT 1 FROM electronic WHERE product_id = :pid');
            $check->execute(['pid' => $productId]);
            $exists = (bool)$check->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }

        $sqlInsert = 'INSERT INTO electronic (product_id, brand, waranty_fee) VALUES (:pid, :brand, :waranty_fee)';
        $sqlUpdate = 'UPDATE electronic SET brand = :brand, waranty_fee = :waranty_fee WHERE product_id = :pid';

        try {
            $stmt = $exists ? $pdo->prepare($sqlUpdate) : $pdo->prepare($sqlInsert);
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':brand', $this->brand, $this->brand !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':waranty_fee', $this->waranty_fee !== null ? (int)$this->waranty_fee : null, $this->waranty_fee !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $executed = $stmt->execute();
            if ($executed === false) {
                return false;
            }
            return $this;
        } catch (PDOException $e) {
            return false;
        }
    }
}