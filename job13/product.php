<?php

// Classe de base abstraite contenant les propriétés et utilitaires partagés.
// Certaines méthodes (findOneById, findAll, create, update) sont abstraites
// afin que les classes enfants puissent fournir leur propre comportement.
abstract class AbstractProduct {
    protected ?int $id;
    protected ?string $name;
    protected ?array $photos;
    protected ?int $price;
    protected ?string $description;
    protected ?int $quantity;
    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;
    protected ?int $category_id;

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

    // Getters / setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->name = $name; }

    public function getPhotos(): ?array { return $this->photos; }
    public function setPhotos(?array $photos): void { $this->photos = $photos; }

    public function getPrice(): ?int { return $this->price; }
    public function setPrice(?int $price): void { $this->price = $price; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(?int $quantity): void { $this->quantity = $quantity; }

    public function getCreatedAt(): ?DateTime { return $this->createdAt; }
    public function setCreatedAt(?DateTime $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): ?DateTime { return $this->updatedAt; }
    public function setUpdatedAt(?DateTime $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function getCategoryId(): ?int { return $this->category_id; }
    public function setCategoryId(?int $category_id): void { $this->category_id = $category_id; }

    // Méthode concrète partagée : récupère la Category liée si possible
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

        // on require Category à la demande pour éviter les inclusions circulaires
        require_once __DIR__ . '/category.php';

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

    // Méthodes utilitaires protégées pour la manipulation de la table product.
    // Ces helpers sont utilisés par la classe Product concrète par défaut,
    // et peuvent aussi être utilisés par les classes enfants si nécessaire.

    protected function dbDsn(): string {
        $host = 'localhost';
        $port = '3306';
        $db   = 'draft-shop';
        $charset = 'utf8mb4';
        return "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    }

    protected function getPdo(): ?PDO {
        try {
            return new PDO($this->dbDsn(), 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Insert générique dans la table product ; retourne l'id inséré ou false
    protected function insertProductRow() {
        $pdo = $this->getPdo();
        if ($pdo === null) {
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

            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Update générique de la ligne product ; retourne true/false
    protected function updateProductRow(): bool {
        if ($this->id === null) {
            return false;
        }

        $pdo = $this->getPdo();
        if ($pdo === null) {
            return false;
        }

        $sql = 'UPDATE product SET
                    name = :name,
                    photos = :photos,
                    price = :price,
                    description = :description,
                    quantity = :quantity,
                    createdAt = :createdAt,
                    updatedAt = :updatedAt,
                    category_id = :category_id
                WHERE id = :id';

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
            $stmt->bindValue(':id', (int)$this->id, PDO::PARAM_INT);

            $executed = $stmt->execute();
            return $executed !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Hydrate l'instance courante depuis une ligne de la table product
    protected function hydrateFromProductRow(array $row): void {
        $this->id = isset($row['id']) ? (int)$row['id'] : null;
        $this->name = $row['name'] ?? null;
        $this->photos = !empty($row['photos']) && is_string($row['photos']) ? json_decode($row['photos'], true) : null;
        $this->price = isset($row['price']) ? (int)$row['price'] : null;
        $this->description = $row['description'] ?? null;
        $this->quantity = isset($row['quantity']) ? (int)$row['quantity'] : null;
        $this->createdAt = !empty($row['createdAt']) ? new DateTime($row['createdAt']) : null;
        $this->updatedAt = !empty($row['updatedAt']) ? new DateTime($row['updatedAt']) : null;
        $this->category_id = isset($row['category_id']) ? (int)$row['category_id'] : null;
    }

    // Méthodes publiques abstraites que les classes concrètes doivent implémenter
    abstract public function findOneById(int $id);
    abstract public function findAll(): array;
    abstract public function create();
    abstract public function update();
}

// Classe Product concrète fournie pour comportement générique.
// Elle implémente les méthodes abstraites en s'appuyant sur les helpers protégés.
// Les classes enfants peuvent préférer redéfinir ces méthodes.
class Product extends AbstractProduct {
    public function findOneById(int $id) {
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

    public function findAll(): array {
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

    public function create() {
        $lastId = $this->insertProductRow();
        if ($lastId === false) {
            return false;
        }
        $this->setId($lastId);
        return $this;
    }

    public function update() {
        $ok = $this->updateProductRow();
        return $ok ? $this : false;
    }
}