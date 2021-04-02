<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Request;

use App\Infrastructure\Persistence\Request\MySQLRequestRepository;
use Tests\TestCase;

class MySQLRequestRepositoryTest extends TestCase {
    public function requestProvider() {
        return [
            'https://webscraper.io/test-sites/e-commerce/allinone/product/547',
            'https://webscraper.io/test-sites/e-commerce/allinone/product/568',
            'https://webscraper.io/test-sites/e-commerce/allinone/product/492',
            'https://webscraper.io/test-sites/e-commerce/allinone/product/488',
            'https://webscraper.io/test-sites/e-commerce/allinone/product/548',
        ];
    }
    
    public function setup() {
        $this->harness = new MySQLRequestRepository('lukeseeley','142.93.114.73:3306','lukeseeley','letmein');

        //We need to make sure that our test requests do not remain in the database, to avoid insert collisions
        foreach($this->requestProvider() as $request) {
            $this->harness->removeRequest($request);
        }
    }

    public function testAddRequest() {
        foreach($this->requestProvider() as $request) {
            $this->harness->addRequest($request);
    
            $this->assertContains($request, $this->harness->findAll());
        }
    }

    public function testRemoveRequest() {
        foreach($this->requestProvider() as $request) {
            $this->harness->addRequest($request);
            $this->harness->removeRequest($request);
    
            $this->assertNotContains($request, $this->harness->findAll());
        }
    }

    public function testFindAll() {
        $expected = $this->requestProvider();

        foreach($expected as $request) {
            $this->harness->addRequest($request);
        }

        foreach($expected as $request) {
            $this->assertContains($request, $this->harness->findAll());
        }
    }
    
}