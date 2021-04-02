<?php
declare(strict_types=1);

namespace App\Domain\Spider;

use App\Domain\Page\Page;
use App\Domain\Item\Item;
use DOMDocument;
use DOMXPath;

class Spider implements SpiderInterface {
    /**
     * @var Page
     */
    private $page;

    /**
     * @var DOMDocument
     */
    private $doc;

    /**
     * @var DOMXPath
     */
    private $doc_xpath;

    /**
     * @var string[]
     * Unprocessed links
     */
    private $linkBuffer;

    /**
     * @var string[]
     * These links have been processed already
     */
    private $links;

    /**
     * @var Item
     */
    private $item;

    /**
     * Constructor
     */
    public function __construct() {
        $this->linkBuffer = [];
        $this->links = [];
    }

    /**
     * Takes in a page, and processes it
     * This being the primary function that the Engine uses to send the Spider pages.
     * This function starts by processing and sorting out the incomming page for further processing
     * Before running other processing functions.
     * @param Page  $pg
     * @return void
     */
    public function loadPage(Page $pg): void {
        //Reset varibles for new page
        $this->linkBuffer = [];
        $this->links = [];
        $this->item = null;

        $this->page = $pg;
        $this->doc_xpath = new DOMXPath($this->page->getDoc());

        //Begin processing of page
        $this->processLinks(); //Simply grabs every link from the page and puts them in the buffer
        $this->filterLinks('/test-sites/e-commerce/allinone*', 'https://webscraper.io');

        $query = 'https://webscraper.io/test-sites/e-commerce/allinone/product/*';
        if(fnmatch($query, $this->page->getUrl())) { //Determine if this is a product page
            $this->processItem();
        }
    }

    /**
     * Processes and stores all links on a page into buffer
     * @return void
     */
    public function processLinks(): void {
        $nodeList = $this->doc_xpath->query('//a[@href]'); //Get all anchor tags with an href
        
        foreach($nodeList as $node) {
            $link = $node->getAttribute('href'); //Get the href
            $this->linkBuffer[] = $link;
        }
    }

    /**
     * Filters links given reg expression
     * Prefix is a handler for relative links
     * @param string    $query
     * @param string    $prefix
     * @return void
     */
    public function filterLinks(string $query, string $prefix): void {
        foreach($this->linkBuffer as $link) {
            if(fnmatch($query, $link)) { //Use query to limit links to desired ones
                $this->links[] = $prefix . $link;
            }
        }
    }

    /**
     * Process item on a page
     * @return void
     */
    public function processItem(): void {
        $url = $this->page->getUrl();
        $id = (int)substr($url, strpos($url, 'product/') + 8); //The product ID is part of the url

        $x_query = $this->doc_xpath->query('//div[@class="caption"]/h4'); //Get all h4's as part of the item caption
        $name = $x_query[1]->textContent;
        $price = (float)(substr($x_query[0]->textContent, 1));

        $x_query = $this->doc_xpath->query('//div[@class="caption"]/p'); //Grab the paragraph for the description
        $description = $x_query[0]->textContent;

        $this->item = new Item($id, $name, $price, $description);
    }

    /**
     * Get processed links
     */
    public function getLinks() {
        return  $this->links;
    }

    /**
     * Get processed item
     */
    public function getItem() {
        return $this->item;
    }
}