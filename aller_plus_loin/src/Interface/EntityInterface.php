<?php

namespace App\Interface;

/**
 * Interface de base pour toutes les entités de l'application.
 * Garantit que chaque entité possède un ID et les méthodes associées.
 */
interface EntityInterface
{
    /**
     * Récupère l'ID de l'entité
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Définit l'ID de l'entité
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void;
}
