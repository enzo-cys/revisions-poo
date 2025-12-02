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
    public function getBrand(): ?string { return $this->brand; }
    public function setBrand(?string $brand): void { $this->brand = $brand; }

    public function getWarantyFee(): ?int { return $this->waranty_fee; }
    public function setWarantyFee(?int $waranty_fee): void { $this->waranty_fee = $waranty_fee; }

    // Trouve une ligne product + electronic par id et retourne une instance Electronic ou false
    public function findOneById(int $id) {
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

        $createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
        $updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;

        $electronic = new Electronic(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['name'] ?? null,
            !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
            isset($row['price']) ? (int)$row['price'] : null,
            $row['description'] ?? null,
            isset($row['quantity']) ? (int)$row['quantity'] : null,
            $createdAt,
            $updatedAt,
            isset($row['category_id']) ? (int)$row['category_id'] : null,
            $row['e_brand'] ?? null,
            isset($row['e_waranty_fee']) ? (int)$row['e_waranty_fee'] : null
        );

        return $electronic;
    }

    // Récupère toutes les lignes product + electronic et renvoie un tableau d'instances Electronic
    public function findAll(): array {
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
            $createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
            $updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;

            $items[] = new Electronic(
                isset($row['id']) ? (int)$row['id'] : null,
                $row['name'] ?? null,
                !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null,
                isset($row['price']) ? (int)$row['price'] : null,
                $row['description'] ?? null,
                isset($row['quantity']) ? (int)$row['quantity'] : null,
                $createdAt,
                $updatedAt,
                isset($row['category_id']) ? (int)$row['category_id'] : null,
                $row['e_brand'] ?? null,
                isset($row['e_waranty_fee']) ? (int)$row['e_waranty_fee'] : null
            );
        }

        return $items;
    }

    // Crée la ligne product via parent::create() puis la ligne electronic ; retourne $this ou false
    public function create() {
        $res = parent::create();
        if ($res === false) {
            return false;
        }

        $productId = $this->getId();
        if ($productId === null) {
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

        $sql = 'INSERT INTO electronic (product_id, brand, waranty_fee)
                VALUES (:pid, :brand, :waranty_fee)';
        try {
            $stmt = $pdo->prepare($sql);
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

    // Met à jour product via parent::update() puis la table electronic (insert ou update) ; retourne $this ou false
    public function update() {
        if ($this->getId() === null) {
            return false;
        }

        $res = parent::update();
        if ($res === false) {
            return false;
        }

        $productId = $this->getId();
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