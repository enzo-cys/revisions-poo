<?php

namespace App;

use DateTime;

/**
 * Classe Category simple.
 * getProducts() retourne des instances App\Product hydratées.
 */
class Category
{
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

    // Getters / setters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Récupère les products liés à cette catégorie et retourne des instances Product
    public function getProducts(): array
    {
        if ($this->id === null) {
            return [];
        }

        try {
            $pdo = new \PDO('mysql:host=localhost;port=3306;dbname=draft-shop;charset=utf8mb4', 'root', '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) {
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
            $prod = new \App\Product();
            $prod->hydrateFromProductRow($row);
            $products[] = $prod;
        }

        return $products;
    }
}
