<?php
declare(strict_types=1);

namespace App\Domain\Item;

interface ItemRepository
{
    /**
     * @param Item  $item
     */
    public function addItem($item): void;

    /**
     * @param Item   $item
     * @throws ItemNotFoundException
     */
    public function updateById($item): void;

    /**
     * @param int   $id
     */
    public function removeById($id): void;

    /**
     * @return Item[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Item
     * @throws ItemNotFoundException
     */
    public function findItemOfId(int $id): Item;
}
