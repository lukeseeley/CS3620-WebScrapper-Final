<?php
declare(strict_types=1);

namespace Tests\Domain\Item;

use App\Domain\Item\Item;
use Tests\TestCase;

class ItemTest extends TestCase
{
    public function itemProvider()
    {
        return [
            [547, 'Prestigio SmartBook 133S Gold', 299.00, 'Prestigio SmartBook 133S Gold, 13.3" FHD IPS, Celeron N3350 1.1GHz, 4GB, 32GB, Windows 10 Pro + Office 365 1 gadam'],
            [568, 'Acer Aspire A315-51-33TG', 457.38, 'Acer Aspire A315-51-33TG, Black 15.6" HD, Core i3-7100U, 4GB DDR4, 128GB SSD, Windows 10 Home, ENG'],
            [492, 'Iphone', 899.99, 'White'],
            [488, 'Samsung Galaxy', 93.99, '5 mpx. Android 5.0'],
            [548, 'Lenovo V110-15IAP', 321.94, 'Lenovo V110-15IAP, 15.6" HD, Celeron N3350 1.1GHz, 4GB, 128GB SSD, Windows 10 Home'],
        ];
    }

    /**
     * @dataProvider itemProvider
     * @param int    $id
     * @param string $name
     * @param float $price
     * @param string $description
     */
    public function testGetters(int $id, string $name, float $price, string $description)
    {
        $item = new Item($id, $name, $price, $description);

        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($price, $item->getPrice());
        $this->assertEquals($description, $item->getDescription());
    }

    /**
     * @dataProvider itemProvider
     * @param int    $id
     * @param string $name
     * @param float $price
     * @param string $description
     */
    public function testJsonSerialize(int $id, string $name, float $price, string $description)
    {
        $item = new Item($id, $name, $price, $description);

        $expectedPayload = json_encode([
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'description' => $description,
        ]);

        $this->assertEquals($expectedPayload, json_encode($item));
    }
}
