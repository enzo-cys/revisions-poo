<?php

require_once 'product.php';

class Category {
    private ?int $id;
    private ?string $name;
    private ?string $description;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $description = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
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

    // Job 06 : retourne un tableau d'instances Product liées à cette catégorie
    public function getProducts(): array {
        // si pas d'id, aucun produit lié
        if ($this->id === null) {
            return [];
        }

        // Connexion PDO minimale
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
            // retourne tableau vide en cas d'erreur de connexion
            return [];
        }

        $stmt = $pdo->prepare('SELECT * FROM product WHERE category_id = :id');
        $stmt->execute(['id' => $this->id]);
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
}