<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Item;

use App\Domain\Item\Item;
use App\Domain\Item\ItemNotFoundException;
use App\Domain\Item\ItemRepository;

class InMemoryItemRepository implements ItemRepository {
    /**
     * @var Item[]
     */
    private $items;

    /**
     * LocalItemRepository constructor.
     *
     * @param array|null    $items
     */
    public function __construct($items = null)
    {
        $items == null ? $this->items = [] : $this->items = $items; 
    }

    /**
     * @param Item  $item
     */
    public function addItem($item): void {
        if($this->findIndex($item->getId()) === -1) {
            $this->items[] = $item;
        }
    }

    /**
     * @param Item   $item
     * @throws ItemNotFoundException
     */
    public function updateById($item): void {
        $id = $item->getId();
        $ind = $this->findIndex($id);
        if($ind === -1) {
            throw new ItemNotFoundException();
        }
        $this->items[$ind] = $item;
    }

    /**
     * @param int   $id
     */
    public function removeById($id): void {
        $ind = $this->findIndex($id);
        if($ind !== -1) {
            array_splice($this->items, $ind, 1);
        }
    }

    /**
     * @return Item[]
     */
    public function findAll(): array
    {
        return array_values($this->items);
    }

    /**
     * @param int $id
     * @return Item
     * @throws ItemNotFoundException
     */
    public function findItemOfId(int $id): Item
    {
        $ids = [];
        foreach($this->items as $item) {
            $ids[] = $item->getId();
        }

        $ind = array_search($id, $ids);
        if($ind === false) {
            throw new ItemNotFoundException();
        }

        return $this->items[$ind];
    }

    /**
     * Searches in items for specific item by it's id
     * @param int   $id
     * @return int
     */
    private function findIndex(int $id): int {
        $ids = [];
        foreach($this->items as $item) {
            $ids[] = $item->getId();
        }
        $ind = array_search($id, $ids);
        if($ind === false) {
            return -1; //Not found;
        }
        else return $ind;
    }
}