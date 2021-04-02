<?php
declare(strict_types=1);

namespace Tests\Domain\GuzzleDownloader;

use App\Domain\Downloader\GuzzleDownloader;
use App\Domain\Page\Page;
use Tests\TestCase;

class GuzzleTest extends TestCase {
    public function setUp(): void {
        $this->harness = new GuzzleDownloader();
    }

    public function testCanary(): void {
        $this->assertTrue($this->harness instanceof GuzzleDownloader);
    }

    public function testGetStatusCode(): void {
        $this->harness->request('https://webscraper.io/test-sites/e-commerce/allinone');
        $actual = $this->harness->getStatusCode();
        $this->assertEquals($actual, 200);
    }

    public function testRequest(): void {
        $actual = $this->harness->request('https://webscraper.io/test-sites/e-commerce/allinone');
        $doc = $actual->getDoc();
        $this->assertEquals($actual->getUrl(), 'https://webscraper.io/test-sites/e-commerce/allinone');
        $this->assertEquals($doc->getElementsByTagName('title')[0]->textContent, 'Web Scraper Test Sites');
    }
}