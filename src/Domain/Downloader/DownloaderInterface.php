<?php
declare(strict_types=1);

namespace App\Domain\Downloader;

use App\Domain\Page\Page;

interface DownloaderInterface {
   /**
    * @return int
    */
    public function getStatusCode(): int;

    /**
     * @param string    $url
     * @return Page
     */
    public function request(string $url): Page;

}
