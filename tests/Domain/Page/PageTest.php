<?php
declare(strict_types=1);

namespace Tests\Domain\Page;

use App\Domain\Page\Page;
use Tests\TestCase;

class PageTest extends TestCase {
    public function setup() {
        $html = file_get_contents('http://example.com');
        $this->harness = new Page('http://example.com', $html);
    }

    public function testCanary() {
        $this->assertTrue($this->harness instanceof Page);
    }

    public function testGetters() {
        $this->assertEquals($this->harness->getUrl(), 'http://example.com');

        $actual = $this->harness->getDoc();
        $this->assertEquals($actual->getElementsByTagName('title')[0]->textContent, 'Example Domain');
    }

    public function testJsonSerialize() {
        $expectedPayload = json_encode([
            'url' => 'http://example.com',
            'doc' => $this->harness->getDoc()
        ]);

        $this->assertEquals($expectedPayload, json_encode($this->harness));
    }

    public function testNewHTML() {
        $this->harness->setUrl('https://webscraper.io/test-sites/e-commerce/allinone');
        $html = file_get_contents('https://webscraper.io/test-sites/e-commerce/allinone');
        $this->harness->runHTML($html);

        $actual = $this->harness->getDoc();

        $this->assertEquals($this->harness->getUrl(), 'https://webscraper.io/test-sites/e-commerce/allinone');
        $this->assertEquals($actual->getElementsByTagName('title')[0]->textContent, 'Web Scraper Test Sites');
    }
}