<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Item;

use App\Domain\Item\Item;
use App\Domain\Item\ItemNotFoundException;
use App\Infrastructure\Persistence\Item\InMemoryItemRepository;
use Tests\TestCase;

class InMemoryItemRepositoryTest extends TestCase {
    public function setup() {
        $array = [
            0 => new Item(547, 'Prestigio SmartBook 133S Gold', 299.00, 'Prestigio SmartBook 133S Gold, 13.3" FHD IPS, Celeron N3350 1.1GHz, 4GB, 32GB, Windows 10 Pro + Office 365 1 gadam'),
            1 => new Item(568, 'Acer Aspire A315-51-33TG', 457.38, 'Acer Aspire A315-51-33TG, Black 15.6" HD, Core i3-7100U, 4GB DDR4, 128GB SSD, Windows 10 Home, ENG'),
            2 => new Item(492, 'Iphone', 899.99, 'White'),
            3 => new Item(488, 'Samsung Galaxy', 93.99, '5 mpx. Android 5.0'),
            4 => new Item(548, 'Lenovo V110-15IAP', 321.94, 'Lenovo V110-15IAP, 15.6" HD, Celeron N3350 1.1GHz, 4GB, 128GB SSD, Windows 10 Home')
        ];
        
        $this->harness = new InMemoryItemRepository($array);
    }
    
    public function testCanary() {
        $this->assertTrue($this->harness instanceof InMemoryItemRepository);
    }

    public function testAddItem() {  
        $this->harness->addItem(new Item(625, 'Dell Latitude 5480', 1338.37, 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home'));

        $actual = $this->harness->findItemOfId(625);
        $expected = new Item(625, 'Dell Latitude 5480', 1338.37, 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home');
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateItem() {
        $item = new Item(492, 'Iphone 11', 899.99, 'White');
        $this->harness->updateById($item);

        $this->assertEquals($item, $this->harness->findItemOfId(492));
    }

    public function testRemoveItem() {
        $this->harness->removeById(625);
        $this->expectException(ItemNotFoundException::class);
        $this->harness->findItemOfId(625);
    }

    public function testFailFindItemOfId() {
        $this->expectException(ItemNotFoundException::class);
        $this->harness->findItemOfId(620);
    }
    
    public function testFindAll() {
        $array = [
            0 => new Item(547, 'Prestigio SmartBook 133S Gold', 299.00, 'Prestigio SmartBook 133S Gold, 13.3" FHD IPS, Celeron N3350 1.1GHz, 4GB, 32GB, Windows 10 Pro + Office 365 1 gadam'),
            1 => new Item(568, 'Acer Aspire A315-51-33TG', 457.38, 'Acer Aspire A315-51-33TG, Black 15.6" HD, Core i3-7100U, 4GB DDR4, 128GB SSD, Windows 10 Home, ENG'),
            2 => new Item(492, 'Iphone', 899.99, 'White'),
            3 => new Item(488, 'Samsung Galaxy', 93.99, '5 mpx. Android 5.0'),
            4 => new Item(548, 'Lenovo V110-15IAP', 321.94, 'Lenovo V110-15IAP, 15.6" HD, Celeron N3350 1.1GHz, 4GB, 128GB SSD, Windows 10 Home')
        ];

        $this->assertEquals($array, $this->harness->findAll());
    }
} 