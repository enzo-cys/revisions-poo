<?php

namespace App;

use DateTime;
use PDO;
use PDOException;
use App\Interface\EntityInterface;

/**
 * JOB 04 : Classe Category refactorisée avec EntityCollection.
 * Utilise EntityCollection pour gérer la liste des produits.
 */
class Category implements EntityInterface
{
    private ?int $id;
    private ?string $name;
    private ?string $description;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;
    private EntityCollection $products;

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
        $this->products = new EntityCollection();
    }

    // Implémentation EntityInterface
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    // Getters / setters
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

    /**
     * Retourne l'EntityCollection des produits
     * @return EntityCollection
     */
    public function getProductsCollection(): EntityCollection
    {
        return $this->products;
    }

    /**
     * Récupère les produits liés à cette catégorie depuis la BDD
     * et retourne un tableau d'instances Product (compatibilité)
     * @return array
     */
    public function getProducts(): array
    {
        if ($this->id === null) {
            return [];
        }

        // Si la collection n'a pas encore été remplie, on la charge
        if ($this->products->isEmpty()) {
            $this->loadProductsFromDatabase();
        }

        return $this->products->getAll();
    }

    /**
     * Charge les produits depuis la base de données et les ajoute à la collection
     * @return void
     */
    public function loadProductsFromDatabase(): void
    {
        if ($this->id === null) {
            return;
        }

        try {
            $pdo = new PDO('mysql:host=localhost;port=3306;dbname=draft-shop;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            return;
        }

        $stmt = $pdo->prepare('SELECT * FROM product WHERE category_id = :id');
        $stmt->execute(['id' => $this->id]);
        $rows = $stmt->fetchAll();

        if (!$rows) {
            return;
        }

        foreach ($rows as $row) {
            $prod = new Product();
            $prod->hydrateFromProductRow($row);
            $this->products->add($prod);
        }
    }

    /**
     * Ajoute un produit à la collection
     * @param EntityInterface $product
     * @return self
     */
    public function addProduct(EntityInterface $product): self
    {
        $this->products->add($product);
        return $this;
    }

    /**
     * Retire un produit de la collection
     * @param EntityInterface $product
     * @return self
     */
    public function removeProduct(EntityInterface $product): self
    {
        $this->products->remove($product);
        return $this;
    }
}
