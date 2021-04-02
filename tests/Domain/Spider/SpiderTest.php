<?php
declare(strict_types=1);

namespace Tests\Domain\Spider;

use App\Domain\Spider\Spider;
use App\Domain\Page\Page;
use Tests\TestCase;

class SpiderTest extends TestCase {
    public function setup() {
        $this->harness = new Spider();
    }

    public function testCanary() {
        $this->assertTrue($this->harness instanceof Spider);
    }

    public function testLoadPage() {
        $html = file_get_contents('https://webscraper.io/test-sites/e-commerce/allinone');
        $page = new Page('https://webscraper.io/test-sites/e-commerce/allinone', $html);

        $this->harness->loadPage($page);
        $expected_links = [
            "https://webscraper.io/test-sites/e-commerce/allinone",
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones",
        ]; //I would test the other links, however, the main links are dynamic, and cannot be predicted.

        $actual = $this->harness->getLinks();

        foreach($expected_links as $link) {
            $this->assertContains($link, $actual);
        }
    }

    public function testItemPage() {
        $html = file_get_contents('https://webscraper.io/test-sites/e-commerce/allinone/product/625');
        $page = new Page('https://webscraper.io/test-sites/e-commerce/allinone/product/625', $html);

        $this->harness->loadPage($page);

        $expected = json_encode([
            'id' => 625,
            'name' => 'Dell Latitude 5480',
            'price' => 1338.37,
            'description' => 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home',
        ]);

        $actual = $this->harness->getItem();

        $this->assertEquals($expected, json_encode($actual));
    }
}