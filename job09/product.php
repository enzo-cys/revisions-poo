<?php

require_once 'category.php';

class Product {
    private ?int $id;
    private ?string $name;
    private ?array $photos;
    private ?int $price;
    private ?string $description;
    private ?int $quantity;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;
    private ?int $category_id;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?array $photos = null,
        ?int $price = null,
        ?string $description = null,
        ?int $quantity = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?int $category_id = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->category_id = $category_id;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getPhotos(): ?array {
        return $this->photos;
    }

    public function setPhotos(?array $photos): void {
        $this->photos = $photos;
    }

    public function getPrice(): ?int {
        return $this->price;
    }

    public function setPrice(?int $price): void {
        $this->price = $price;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getQuantity(): ?int {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void {
        $this->quantity = $quantity;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function getCategoryId(): ?int {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): void {
        $this->category_id = $category_id;
    }

    // Job 05 : retourne une instance de Category liée via category_id, ou false si non trouvée
    public function getCategory() {
        if ($this->category_id === null) {
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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            return false;
        }

        $stmt = $pdo->prepare('SELECT * FROM category WHERE id = :id');
        $stmt->execute(['id' => $this->category_id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
        $updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;

        return new Category(
            isset($row['id']) ? (int)$row['id'] : null,
            $row['name'] ?? null,
            $row['description'] ?? null,
            $createdAt,
            $updatedAt
        );
    }

    // Job 07 : trouve une ligne product par id et hydrate l'instance courante, ou retourne false si introuvable
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

        $stmt = $pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        // Hydrater l'instance courante
        $this->setId(isset($row['id']) ? (int)$row['id'] : null);
        $this->setName($row['name'] ?? null);

        $photos = [];
        if (!empty($row['photos'])) {
            $decoded = json_decode($row['photos'], true);
            if (is_array($decoded)) {
                $photos = $decoded;
            }
        }
        $this->setPhotos($photos);

        $this->setPrice(isset($row['price']) ? (int)$row['price'] : null);
        $this->setDescription($row['description'] ?? null);
        $this->setQuantity(isset($row['quantity']) ? (int)$row['quantity'] : null);
        $this->setCreatedAt(!empty($row['createdAt']) ? new DateTime($row['createdAt']) : null);
        $this->setUpdatedAt(!empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null);
        $this->setCategoryId(isset($row['category_id']) ? (int)$row['category_id'] : null);

        return $this;
    }

    // Job 08 : récupère toutes les lignes product et retourne un tableau d'instances Product
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

        $stmt = $pdo->query('SELECT * FROM product');
        $rows = $stmt->fetchAll();

        if (!$rows) {
            return [];
        }

        $products = [];
        foreach ($rows as $row) {
            $p = new Product();
            $p->setId(isset($row['id']) ? (int)$row['id'] : null);
            $p->setName($row['name'] ?? null);

            $photos = [];
            if (!empty($row['photos'])) {
                $decoded = json_decode($row['photos'], true);
                if (is_array($decoded)) {
                    $photos = $decoded;
                }
            }
            $p->setPhotos($photos);

            $p->setPrice(isset($row['price']) ? (int)$row['price'] : null);
            $p->setDescription($row['description'] ?? null);
            $p->setQuantity(isset($row['quantity']) ? (int)$row['quantity'] : null);
            $p->setCreatedAt(!empty($row['createdAt']) ? new DateTime($row['createdAt']) : null);
            $p->setUpdatedAt(!empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null);
            $p->setCategoryId(isset($row['category_id']) ? (int)$row['category_id'] : null);

            $products[] = $p;
        }

        return $products;
    }

    // Job 09 : insère l'instance courante en base et retourne $this avec l'id si succès, sinon false
    public function create() {
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

        $sql = 'INSERT INTO product (name, photos, price, description, quantity, createdAt, updatedAt, category_id)
                VALUES (:name, :photos, :price, :description, :quantity, :createdAt, :updatedAt, :category_id)';

        try {
            $stmt = $pdo->prepare($sql);

            $photosJson = '[]';
            if (is_array($this->photos)) {
                $encoded = json_encode($this->photos);
                $photosJson = $encoded === false ? '[]' : $encoded;
            }

            $createdAt = $this->createdAt instanceof DateTime ? $this->createdAt->format('Y-m-d H:i:s') : null;
            $updatedAt = $this->updatedAt instanceof DateTime ? $this->updatedAt->format('Y-m-d H:i:s') : null;

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':photos', $photosJson, PDO::PARAM_STR);
            $stmt->bindValue(':price', $this->price !== null ? (int)$this->price : null, PDO::PARAM_INT);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':quantity', $this->quantity !== null ? (int)$this->quantity : null, PDO::PARAM_INT);
            $stmt->bindValue(':createdAt', $createdAt, PDO::PARAM_STR);
            $stmt->bindValue(':updatedAt', $updatedAt, PDO::PARAM_STR);
            $stmt->bindValue(':category_id', $this->category_id !== null ? (int)$this->category_id : null, PDO::PARAM_INT);

            $executed = $stmt->execute();
            if ($executed === false) {
                return false;
            }

            $lastId = (int)$pdo->lastInsertId();
            if ($lastId > 0) {
                $this->setId($lastId);
                return $this;
            }

            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
}