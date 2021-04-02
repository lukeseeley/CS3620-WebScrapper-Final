<?php
declare(strict_types=1);

namespace App\Domain\Downloader;

use App\Domain\DomainException\DomainException;

class DownloadHTTPErrorException extends DomainException {
    public $message = "The requestested url has had an HTTP error.";
}