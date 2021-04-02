<?php
declare(strict_types=1);

namespace Tests\Domain\Scheduler;

use App\Domain\Scheduler\Scheduler;
use App\Domain\Item\Item;
use Tests\TestCase;

class SchedulerTest extends TestCase {
    public function setup() {
        $this->harness = new Scheduler();
    }

    public function testCanary() {
        $this->assertTrue($this->harness instanceof Scheduler);
    }
    
    public function testStartEngine() {
        $this->harness->startEngine();

        $actual = $this->harness->getEngine();
        $this->assertEquals('https://webscraper.io/test-sites/e-commerce/allinone', $actual->getPages()[0]->getUrl());
    }

    public function testSpiderPhase() {
        $this->harness->startEngine();
        $this->harness->spiderPhase();

        $expected = [
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones"
        ];
        $actual = $this->harness->getEngine()->getRequests();

        foreach($expected as $link) {
            $this->assertContains($link, $actual);
        }
    }

    public function testDownloaderPhase() {
        $this->harness->startEngine();
        $this->harness->spiderPhase();
        $this->harness->downloaderPhase();

        $expected = [
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones"
        ];

        $pages = $this->harness->getEngine()->getPages();
        $actual = [];
        foreach($pages as $page) {
            $actual[] = $page->getUrl();
        }

        foreach($expected as $link) {
            $this->assertContains($link, $actual);
        }
    }

    public function testRunEngine() {
        $this->harness->runEngine(3); //Theotrically, it would scrape the entire page at 4 cycles
        $expected = [
            'https://webscraper.io/test-sites/e-commerce/allinone',
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones",
            "https://webscraper.io/test-sites/e-commerce/allinone/computers/laptops",
            "https://webscraper.io/test-sites/e-commerce/allinone/computers/tablets",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones/touch"
        ];

        $actual = $this->harness->getEngine()->getSentRequests();

        foreach($expected as $link) {
            $this->assertContains($link, $actual);
        }
    }

    public function testInitializeEngine() {
        $this->harness->initializeEngine('https://webscraper.io/test-sites/e-commerce/allinone/product/495');
        $this->harness->runEngine(1); //This should only run the spider and downloader phases once

        $expected = new Item(495, 'Lenovo IdeaTab', 69.99, '7" screen, Android');

        $actual = $this->harness->getEngine()->getItems()[0];
        $this->assertEquals(json_encode($expected), json_encode($actual));

        $expected = [
            'https://webscraper.io/test-sites/e-commerce/allinone',
            "https://webscraper.io/test-sites/e-commerce/allinone/computers",
            "https://webscraper.io/test-sites/e-commerce/allinone/phones",
        ];

        $actual = $this->harness->getEngine()->getRequests();

        foreach($expected as $link) {
            $this->assertContains($link, $actual);
        }
    }

    public function testItemPipelinePhase() {
        $init_request = 'https://webscraper.io/test-sites/e-commerce/allinone/product/495';
        $expected = new Item(495, 'Lenovo IdeaTab', 69.99, '7" screen, Android');
        //Remove test data if they exist already in database
        $this->harness->getEngine()->removeDatabaseRequest($init_request);
        $this->harness->getEngine()->removeDatabaseItem($expected);

        $this->harness->initializeEngine($init_request);
        $this->harness->startEngine();
        $this->harness->spiderPhase();

        $this->harness->itemPipelinePhase();
        $actual = $this->harness->getEngine()->getDatabaseRequests();
        $this->assertContains($init_request, $actual);
        $this->harness->getEngine()->removeDatabaseRequest($init_request);

        
        $actual = $this->harness->getEngine()->getDatabaseItem($expected);
        $this->assertEquals(json_encode($expected), json_encode($actual));
        $this->harness->getEngine()->removeDatabaseItem($expected);
    }
}