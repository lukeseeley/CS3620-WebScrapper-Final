<?php
declare(strict_types=1);

namespace App\Domain\Item;

use App\Domain\DomainException\DomainRecordNotFoundException;

class ItemNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The item you requested does not exist.';
}
