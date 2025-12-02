<?php

require_once 'product.php';

class Clothing extends Product {
    private ?string $size;
    private ?string $color;
    private ?string $type;
    private ?int $material_fee;

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
        ?string $size = null,
        ?string $color = null,
        ?string $type = null,
        ?int $material_fee = null
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $createdAt, $updatedAt, $category_id);
        $this->size = $size;
        $this->color = $color;
        $this->type = $type;
        $this->material_fee = $material_fee;
    }

    // Getters / setters
    public function getSize(): ?string {
        return $this->size;
    }

    public function setSize(?string $size): void {
        $this->size = $size;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): void {
        $this->color = $color;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): void {
        $this->type = $type;
    }

    public function getMaterialFee(): ?int {
        return $this->material_fee;
    }

    public function setMaterialFee(?int $material_fee): void {
        $this->material_fee = $material_fee;
    }

    // Persiste ou met à jour les détails spécifiques vêtement dans la table clothing
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
            $check = $pdo->prepare('SELECT 1 FROM clothing WHERE product_id = :pid');
            $check->execute(['pid' => $productId]);
            $exists = (bool)$check->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }

        $sqlInsert = 'INSERT INTO clothing (product_id, size, color, type, material_fee) VALUES (:pid, :size, :color, :type, :material_fee)';
        $sqlUpdate = 'UPDATE clothing SET size = :size, color = :color, type = :type, material_fee = :material_fee WHERE product_id = :pid';

        try {
            $stmt = $exists ? $pdo->prepare($sqlUpdate) : $pdo->prepare($sqlInsert);

            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':size', $this->size, PDO::PARAM_STR);
            $stmt->bindValue(':color', $this->color, PDO::PARAM_STR);
            $stmt->bindValue(':type', $this->type, PDO::PARAM_STR);
            $stmt->bindValue(':material_fee', $this->material_fee !== null ? (int)$this->material_fee : null, PDO::PARAM_INT);

            return $stmt->execute() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
}