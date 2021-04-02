<?php
declare(strict_types=1);

namespace App\Domain\Engine;

interface EngineInterface {
    /**
     * Sends next request to Downloader
     */
    public function sendRequest(): void;

    /**
     * Takes the cached requests from the spider, and then processes them for future requests
     */
    public function takeRequests(): void;

    /**
     * Takes the cached item from the spider, and inserts it into a repository
     */
    public function takeItem(): void;

    /**
     * Sends next page to Spider for processing
     */
    public function sendPage(): void;

    /**
     * Sends Inital Request
     */
    public function sendInitRequest(): void;

    /**
     * Sends items stored in memory to the database
     * @return void
     */
    public function pushItems(): void;
}