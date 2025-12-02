<?php

namespace App\Interface;

/**
 * Interface pour la gestion des stocks.
 */
interface StockableInterface
{
    /**
     * Ajoute des unités au stock et retourne l'instance courante
     * @param int $stock
     * @return self
     */
    public function addStocks(int $stock): self;

    /**
     * Retire des unités du stock et retourne l'instance courante
     * @param int $stock
     * @return self
     */
    public function removeStocks(int $stock): self;
}
