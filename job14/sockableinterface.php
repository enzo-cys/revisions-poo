<?php

// Interface pour la gestion des stocks
interface SockableInterface {
    // Ajoute des unités au stock et retourne l'instance courante
    public function addStocks(int $stock): self;

    // Retire des unités du stock et retourne l'instance courante
    public function removeStocks(int $stock): self;
}