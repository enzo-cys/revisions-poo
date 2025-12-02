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
}