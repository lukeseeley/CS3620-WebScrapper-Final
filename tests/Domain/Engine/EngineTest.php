<?php
declare(strict_types=1);

namespace Tests\Domain\Engine;

use App\Domain\Engine\Engine;
use App\Domain\Item\ItemNotFoundException;
use App\Domain\Item\Item;
use Tests\TestCase;

class EngineTest extends TestCase {
    public function setup() {
        $this->harness = new Engine();
    }

    public function testCanary() {
        $this->assertTrue($this->harness instanceof Engine);
    }

    public function testInitRequest() {
        $this->harness->sendInitRequest();

        $actual = $this->harness->getPages();
        $this->assertEquals('https://webscraper.io/test-sites/e-commerce/allinone', $actual[0]->getUrl());
        $this->assertEquals($actual[0]->getDoc()->getElementsByTagName('title')[0]->textContent, 'Web Scraper Test Sites');
    }

    public function testSendPage() {
        $this->harness->sendInitRequest();
        $this->harness->sendPage(); //Now sends the initial request to the Spider

        $actual = $this->harness->getSentPages();
        $this->assertEquals('https://webscraper.io/test-sites/e-commerce/allinone', $actual[0]->getUrl());
        $this->assertEquals($actual[0]->getDoc()->getElementsByTagName('title')[0]->textContent, 'Web Scraper Test Sites');
    }

    public function testStoreRequests() {
        $this->harness->sendInitRequest();
        $this->harness->sendPage();

        $this->harness->takeRequests();
        $expected = [
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones"
        ];

        $actual = $this->harness->getRequests();
        
        foreach($expected as $link) {
            $this->assertContains($link, $actual);
        }

        $this->assertNotEquals('https://webscraper.io/test-sites/e-commerce/allinone', $actual[0]);

        $this->harness->takeItem(); //No Item should be returned from the main page
        $actual = $this->harness->getItems();
        $this->assertEquals(0, count($actual));
    }

    public function testStoreItem() {
        $engine = new Engine('https://webscraper.io/test-sites/e-commerce/allinone/product/625'); //So we can just test grabbing an item
        $engine->sendInitRequest();
        $engine->sendPage();

        $engine->takeItem();
        $actual = $engine->getItems()[0]; //This is an Item
        $expected = new Item(625, 'Dell Latitude 5480', 1338.37, 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home');

        $this->assertEquals(json_encode($expected), json_encode($actual));
    }

    public function testSendNextRequest() {
        $this->harness->sendInitRequest();
        $this->harness->sendPage();
        $this->harness->takeRequests();

        $this->harness->sendRequest();
        $actual = $this->harness->getPages();
        $this->assertEquals('https://webscraper.io/test-sites/e-commerce/allinone/computers', $actual[0]->getUrl());
        $this->assertEquals($actual[0]->getDoc()->getElementsByTagName('title')[0]->textContent, 'Web Scraper Test Sites');

        $actual = $this->harness->getSentRequests()[1];
        $this->assertEquals('https://webscraper.io/test-sites/e-commerce/allinone/computers', $actual);
    }

    public function testPushItems() {
        $engine = new Engine('https://webscraper.io/test-sites/e-commerce/allinone/product/625'); //So we can just test grabbing an item
        $engine->sendInitRequest();
        $engine->sendPage();
        $engine->takeItem();

        $expected = $engine->getItems()[0];
        //So we can ensure item is not already in database
        $engine->removeDatabaseItem($expected);
        
        $engine->pushItems();
        $actual = $engine->getDatabaseItem($expected);

        $this->assertEquals(json_encode($expected), json_encode($actual));

        $engine->removeDatabaseItem($expected);

        $this->expectException(ItemNotFoundException::class);
        $actual = $engine->getDatabaseItem($expected);
    }

    public function testPushRequests() {
        $init_request = 'https://webscraper.io/test-sites/e-commerce/allinone/product/625';
        $engine = new Engine($init_request); //So we can test only having to send a requst
        //Removes the test request if it is already in the database
        $engine->removeDatabaseRequest($init_request);
        $engine->sendInitRequest();

        $engine->pushRequests();
        $actual = $engine->getDatabaseRequests();

        $this->assertContains($init_request, $actual);

        $engine->removeDatabaseRequest($init_request);
        $actual = $engine->getDatabaseRequests();
        $this->assertNotContains($init_request, $actual);
    }
}