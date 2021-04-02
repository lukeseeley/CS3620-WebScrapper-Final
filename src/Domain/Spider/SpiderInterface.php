<?php
declare(strict_types=1);

namespace App\Domain\Spider;

use App\Domain\Page\Page;
use App\Domain\Item\Item;

interface SpiderInterface {
    /**
     * @param Page  $pg
     * Takes in a page, and processes it
     */
    public function loadPage(Page $pg): void;

    /**
     * Processes and stores links on a page into buffer
     */
    public function processLinks(): void;

    /**
     * @param string    $query
     * @param string    $prefix
     * Filters links given reg expression
     * Prefix is a handler for relative links
     */
    public function filterLinks(string $query, string $prefix): void;

    /**
     * Process item on a page
     */
    public function processItem(): void;

    /**
     * Get processed links
     */
    public function getLinks();

    /**
     * Get processed item
     */
    public function getItem();
}