<?php

require_once 'product.php';

class Electronic extends Product {
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
    public function getBrand(): ?string {
        return $this->brand;
    }

    public function setBrand(?string $brand): void {
        $this->brand = $brand;
    }

    public function getWarantyFee(): ?int {
        return $this->waranty_fee;
    }

    public function setWarantyFee(?int $waranty_fee): void {
        $this->waranty_fee = $waranty_fee;
    }

    // Persiste ou met à jour les détails spécifiques électronique dans la table electronic
    public function saveDetails(): bool {
        $productId = $this->getId();
        if ($productId === null) {
            // besoin d'un id produit existant
            return false;
        }

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
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
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
            $stmt->bindValue(':brand', $this->brand, PDO::PARAM_STR);
            $stmt->bindValue(':waranty_fee', $this->waranty_fee !== null ? (int)$this->waranty_fee : null, PDO::PARAM_INT);

            return $stmt->execute() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
}