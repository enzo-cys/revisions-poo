<?php

namespace App;

use App\Interface\EntityInterface;

/**
 * Classe permettant de gérer une collection d'entités.
 * Facilite la manipulation des relations entre entités.
 */
class EntityCollection
{
    /**
     * @var EntityInterface[] Tableau stockant les entités de la collection
     */
    private array $entities = [];

    /**
     * Ajoute une nouvelle entité à la collection
     * @param EntityInterface $entity
     * @return self
     */
    public function add(EntityInterface $entity): self
    {
        $this->entities[] = $entity;
        return $this;
    }

    /**
     * Retire une entité présente dans la collection (par ID)
     * @param EntityInterface $entity
     * @return self
     */
    public function remove(EntityInterface $entity): self
    {
        $idToRemove = $entity->getId();
        if ($idToRemove === null) {
            return $this;
        }

        $this->entities = array_filter($this->entities, function (EntityInterface $item) use ($idToRemove) {
            return $item->getId() !== $idToRemove;
        });

        // Réindexer le tableau
        $this->entities = array_values($this->entities);

        return $this;
    }

    /**
     * Récupère et retourne la collection courante.
     * Permet de chaîner les opérations et de récupérer toutes les entités.
     * @return self
     */
    public function retrieve(): self
    {
        return $this;
    }

    /**
     * Récupère toutes les entités de la collection
     * @return EntityInterface[]
     */
    public function getAll(): array
    {
        return $this->entities;
    }

    /**
     * Retourne le nombre d'entités dans la collection
     * @return int
     */
    public function count(): int
    {
        return count($this->entities);
    }

    /**
     * Vérifie si la collection est vide
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->entities);
    }
}
