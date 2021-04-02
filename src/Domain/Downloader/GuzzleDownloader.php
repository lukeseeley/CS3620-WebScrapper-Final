<?php
declare(strict_types=1);

namespace App\Domain\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Domain\Page\Page;

class GuzzleDownloader implements DownloaderInterface {
    /**
     * @var string
     */
    private $body;

    /**
     * @var string[]
     */
    private $header;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var
     */
    private $response;

    /**
     * @var int
     */
    private $statusCode;

    public function __construct() {
        $this->client = new Client();
    }

    /**
    * @return int
    */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * @param string    $url
     * @return Page
     */
    public function request(string $url): Page {
        $this->response = $this->client->get($url);

        //Process Response
        $this->statusCode = $this->response->getStatusCode();
        if($this->statusCode >= 400) {
            throw new DownloadHTTPErrorException();
        }
        $this->body = (string)$this->response->getBody();
        $this->header = $this->response->getHeaders();

        return new Page($url, $this->body);
    }
}