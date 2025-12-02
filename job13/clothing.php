<?php

require_once 'product.php';

class Clothing extends AbstractProduct {
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
    public function getSize(): ?string { return $this->size; }
    public function setSize(?string $size): void { $this->size = $size; }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): void { $this->color = $color; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): void { $this->type = $type; }

    public function getMaterialFee(): ?int { return $this->material_fee; }
    public function setMaterialFee(?int $material_fee): void { $this->material_fee = $material_fee; }

    // Trouve une ligne product + clothing par id et retourne une instance Clothing ou false
    public function findOneById(int $id) {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $sql = 'SELECT p.*, c.size AS c_size, c.color AS c_color, c.type AS c_type, c.material_fee AS c_material_fee
                FROM product p
                LEFT JOIN clothing c ON c.product_id = p.id
                WHERE p.id = :id
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        $createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
        $updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;

        return new Clothing(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['name'] ?? null,
            !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
            isset($row['price']) ? (int)$row['price'] : null,
            $row['description'] ?? null,
            isset($row['quantity']) ? (int)$row['quantity'] : null,
            $createdAt,
            $updatedAt,
            isset($row['category_id']) ? (int)$row['category_id'] : null,
            $row['c_size'] ?? null,
            $row['c_color'] ?? null,
            $row['c_type'] ?? null,
            isset($row['c_material_fee']) ? (int)$row['c_material_fee'] : null
        );
    }

    // Récupère toutes les lignes product + clothing et renvoie un tableau d'instances Clothing
    public function findAll(): array {
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return [];
        }

        $sql = 'SELECT p.*, c.size AS c_size, c.color AS c_color, c.type AS c_type, c.material_fee AS c_material_fee
                FROM product p
                LEFT JOIN clothing c ON c.product_id = p.id';
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            $createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
            $updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;

            $items[] = new Clothing(
                isset($row['id']) ? (int)$row['id'] : null,
                $row['name'] ?? null,
                !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
                isset($row['price']) ? (int)$row['price'] : null,
                $row['description'] ?? null,
                isset($row['quantity']) ? (int)$row['quantity'] : null,
                $createdAt,
                $updatedAt,
                isset($row['category_id']) ? (int)$row['category_id'] : null,
                $row['c_size'] ?? null,
                $row['c_color'] ?? null,
                $row['c_type'] ?? null,
                isset($row['c_material_fee']) ? (int)$row['c_material_fee'] : null
            );
        }

        return $items;
    }

    // Crée la partie product via le helper parent puis la table clothing ; retourne $this ou false
    public function create() {
        $lastId = $this->insertProductRow();
        if ($lastId === false) {
            return false;
        }
        $this->setId($lastId);

        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $sql = 'INSERT INTO clothing (product_id, size, color, type, material_fee)
                VALUES (:pid, :size, :color, :type, :material_fee)';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':pid', $lastId, PDO::PARAM_INT);
            $stmt->bindValue(':size', $this->size, $this->size !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':color', $this->color, $this->color !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':type', $this->type, $this->type !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':material_fee', $this->material_fee !== null ? (int)$this->material_fee : null, $this->material_fee !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $executed = $stmt->execute();
            if ($executed === false) {
                return false;
            }
            return $this;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Met à jour product via le helper parent puis insert/update dans clothing ; retourne $this ou false
    public function update() {
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
            $stmt->bindValue(':size', $this->size, $this->size !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':color', $this->color, $this->color !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':type', $this->type, $this->type !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':material_fee', $this->material_fee !== null ? (int)$this->material_fee : null, $this->material_fee !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

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