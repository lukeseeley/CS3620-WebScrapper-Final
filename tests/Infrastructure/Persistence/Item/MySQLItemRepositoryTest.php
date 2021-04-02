<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Item;

use App\Domain\Item\Item;
use App\Domain\Item\ItemNotFoundException;
use Exception;
use App\Infrastructure\Persistence\Item\MySQLItemRepository;
use Tests\TestCase;

class MySQLItemRepositoryTest extends TestCase {
    public function itemProvider() {
        return [
            0 => new Item(547, 'Prestigio SmartBook 133S Gold', 299.00, 'Prestigio SmartBook 133S Gold, 13.3" FHD IPS, Celeron N3350 1.1GHz, 4GB, 32GB, Windows 10 Pro + Office 365 1 gadam'),
            1 => new Item(568, 'Acer Aspire A315-51-33TG', 457.38, 'Acer Aspire A315-51-33TG, Black 15.6" HD, Core i3-7100U, 4GB DDR4, 128GB SSD, Windows 10 Home, ENG'),
            2 => new Item(492, 'Iphone', 899.99, 'White'),
            3 => new Item(488, 'Samsung Galaxy', 93.99, '5 mpx. Android 5.0'),
            4 => new Item(548, 'Lenovo V110-15IAP', 321.94, 'Lenovo V110-15IAP, 15.6" HD, Celeron N3350 1.1GHz, 4GB, 128GB SSD, Windows 10 Home')
        ];
    }
    
    public function setup() {
        $this->harness = new MySQLItemRepository('lukeseeley','142.93.114.73:3306','lukeseeley','letmein');

        //We need to make sure that our test items do not remain in the database, to avoid insert collisions
        foreach($this->itemProvider() as $item) {
            $this->harness->removeById($item->getId());
        }
    }


    public function testAddItem() {
        foreach($this->itemProvider() as $item) {
            $this->harness->addItem($item);
            $this->assertEquals(json_encode($item), json_encode($this->harness->findItemOfId($item->getId())));
        }
    }


    public function testUpdateItem() {
        foreach($this->itemProvider() as $item) {
            $this->harness->addItem($item);
            $updatedItem = new Item($item->getId(), $item->getName(), $item->getPrice() / 2.0, $item->getDescription());
            $this->harness->updateById($updatedItem);
            
            $this->assertEquals(json_encode($updatedItem), json_encode($this->harness->findItemOfId($updatedItem->getId())));
        }
    }
   

    public function testRemoveItem() {
        foreach($this->itemProvider() as $item) {
            $this->harness->addItem($item);
            $this->harness->removeById($item->getId());
            
            $this->expectException(Exception::class);
            $this->harness->findItemOfId($item->getId());
        }
    }
    
    public function testFindAll() {
        $expected = [];
        foreach($this->itemProvider() as $item) {
            $this->harness->addItem($item);
            $expected[] = $item->getId();
        }
        
        $actual = [];
        foreach($this->harness->findAll() as $item) {
            $actual[] = $item->getId();
        }

        foreach($expected as $id) {
            $this->assertContains($id, $actual);
        }
 
    }

    public function testFindItemFail() { 
        foreach($this->itemProvider() as $item) {
            $this->expectException(Exception::class);
            $this->harness->findItemOfId($item->getId());
        }
    }
}