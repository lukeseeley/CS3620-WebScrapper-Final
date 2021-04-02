<?php
declare(strict_types=1);

namespace App\Domain\Engine;

use App\Domain\Downloader\GuzzleDownloader;
use App\Domain\Page\Page;
use App\Domain\Item\Item;
use App\Domain\Spider\Spider;
use App\Infrastructure\Persistence\Item\InMemoryItemRepository;
use App\Infrastructure\Persistence\Item\MySQLItemRepository;
use App\Infrastructure\Persistence\Request\MySQLRequestRepository;

class Engine implements EngineInterface {
    /**
     * @var GuzzleDownloader
     */
    private $downloader;

    /**
     * @var Spider
     */
    private $spider;

    /**
     * @var InMemoryItemRepository
     */
    private $local_item_repository;

    /**
     * @var MySQLItemRepository
     */
    private $database_item_repository;

    /**
     * @var MySQLRequestRepository
     */
    private $database_request_repository;

    /**
     * @var string[]
     * Requests are the strings of url's
     */
    private $requests;

    /**
     * @var string[]
     * A list of all previously sent requests
     */
    private $sentRequests;

    /**
     * @var Page[]
     * Pages contain both the associated link with a page, and it's body from a get request
     */
    private $pages;

    /**
     * @var Page[]
     * Pages that have already been sent to a spider for processing
     */
    private $sentPages;


    /**
     * @var string
     * Is actually storing a url string
     */
    private $init_request;

    /**
     * @var bool
     * Has the $initial_request been sent?
     */
    private $is_init_sent;

    /**
     * Engine Constructor
     * @param string    $init_request (url)
     */
    public function __construct($init_request = 'https://webscraper.io/test-sites/e-commerce/allinone') {
        $this->init_request = $init_request;
        $this->downloader = new GuzzleDownloader();
        $this->spider = new Spider();
        $this->local_item_repository = new InMemoryItemRepository();
        $this->database_item_repository = new MySQLItemRepository('lukeseeley','142.93.114.73:3306','lukeseeley','letmein');
        $this->database_request_repository = new MySQLRequestRepository('lukeseeley','142.93.114.73:3306','lukeseeley','letmein');

        //Initialize arrays
        $this->pages = [];
        $this->sentPages = [];
        $this->requests = [];
        $this->sentRequests = [];
        $this->items = [];

        $this->is_init_sent = false;
    }

    /**
     * Sends Inital Request to Downloader
     * @return void
     */
    public function sendInitRequest(): void {
        if(!$this->is_init_sent) {
            $this->pages[] = $this->downloader->request($this->init_request);
            $this->sentRequests[] = $this->init_request;
            $this->is_init_sent = true;
        }
    }

    /**
     * Sets new Inital Request
     * @param string    $request
     * @return void
     */
    public function setInitRequest(string $request): void {
        $this->init_request = $request;
        $this->is_init_sent = false;
    }
    
    /**
     * Sends next request to Downloader (Treating Requests as a Que, it sends the first ones in line first)
     * The downloader then returns a Page object, which is then stored in pages for processing by the Spider
     * The request is then moved to the sentRequests[] array, to keep track of requsts that have already been sent
     * @return void
     */
    public function sendRequest(): void {
        if(count($this->requests) > 0) {
            $this->pages[] = $this->downloader->request($this->requests[0]);
            $this->sentRequests[] = $this->requests[0];
            array_splice($this->requests, 0, 1); //Cut off the now sent request
        }
    }

    /**
     * Takes the cached requests from the spider, and then stores them for future requests
     * Only takes in new requests, will ignore repeat requests
     * @return void
     */
    public function takeRequests(): void {
        $requests = $this->spider->getLinks();
        foreach($requests as $request) {
            if(in_array($request, $this->requests) == false && in_array($request, $this->sentRequests) == false) { 
                $this->requests[] = $request; //Check if this is a repeat request, if not, then add it
            }
        }

    }

    /**
     * Takes the cached item from the spider, and inserts it into a repository
     * The Spider will not be storing an Item, if there was not an Item to be processed on the page
     * @return void
     */
    public function takeItem(): void {
        $item = $this->spider->getItem();
        if($item) { //Check for a valid item
            $this->local_item_repository->addItem($item);
        }
    }

    /**
     * Sends next page to Spider for processing
     * The Pages[] are treated like a Que, and will send the page at the front of the line first
     * The Page is then transfered to sentPages to keep track of already sent pages
     * @return void
     */
    public function sendPage(): void {
        if(count($this->pages) > 0) {
            $this->spider->loadPage($this->pages[0]);
            $this->sentPages[] = $this->pages[0];
            array_splice($this->pages, 0, 1); //Cut off the now sent page
        }
    }

    /**
     * Sends items stored in memory to the database
     * @return void
     */
    public function pushItems(): void {
        $items = $this->local_item_repository->findAll();
    
        foreach($items as $item) {
            $this->database_item_repository->addItem($item);
        }
    }

    /**
     * Sends stored sent requests to the database (Intended for after site has been scrapped)
     * @returns void
     */
    public function pushRequests(): void {
        foreach($this->sentRequests as $request) {
            $this->database_request_repository->addRequest($request);
        }
    }

    /**
     * @return Page[]
     */
    public function getPages() {
        return $this->pages;
    }
    
    /**
     * @return Page[]
     */
    public function getSentPages() {
        return $this->sentPages;
    }
    /**
     * @return string[]
     */
    public function getRequests() {
        return $this->requests;
    }
    /**
     * @return string[]
     */
    public function getSentRequests() {
        return $this->sentRequests;
    }

    /**
     * @return Item[]
     */
    public function getItems() {
        return $this->local_item_repository->findAll();
    }

    /**
     * @return Item[]
     */
    public function getDatabaseItems() {
        return $this->database_item_repository->findAll();
    }

    /**
     * Primaryly for debugging purposes, but removes an item
     * @param Item   $item
     */
    public function getDatabaseItem(Item $item) {
        return $this->database_item_repository->findItemOfId($item->getId());
    }

    /**
     * Primaryly for debugging purposes, but removes an item
     * @param Item   $item
     */
    public function removeDatabaseItem(Item $item) {
        $this->database_item_repository->removeById($item->getId());
    }

    /**
     * @return string[]
     */
    public function getDatabaseRequests() {
        return $this->database_request_repository->findAll();
    }

    /**
     * Primaryly for debugging purposes, but removes an request
     * @param string   $request
     */
    public function removeDatabaseRequest(string $request) {
        $this->database_request_repository->removeRequest($request);
    }

}