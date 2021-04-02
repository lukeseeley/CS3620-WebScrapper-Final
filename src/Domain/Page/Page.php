<?php
declare(strict_types=1);

namespace App\Domain\Page;

use JsonSerializable;
use DOMDocument;

class Page implements JsonSerializable {
    /**
     * @var string
     */
    private $url;

    /**
     * @var DOMDocument
     */
    private $doc;

    /**
     * @param string    $url
     * @param string    $body
     */
    public function __construct($url, $body) {
        $this->url = $url; //This will store the associated url for this page
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(TRUE);

        if($body) { //Got a valid body
            $this->doc->loadHTML($body);
        }
    }

    /**
     * @param string    $body
     * This function will load the html from an html body return from a get request
     */
    public function runHTML($body): void {
        if($body) { //Got a valid body
            $this->doc->loadHTML($body);
        }
    }

    /**
     * @param string    $url
     */
    public function setUrl($url): void {
        if($url) {
            $this->url = $url;
        }
    }
    
    /**
     * @return DOMDocument
     */
    public function getDoc(): DOMDocument {
        return $this->doc;
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'url' => $this->url,
            'doc' => $this->doc
        ];
    }
}