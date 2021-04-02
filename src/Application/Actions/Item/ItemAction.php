<?php
declare(strict_types=1);

namespace App\Application\Actions\Item;

use App\Application\Actions\Action;
use App\Domain\Item\ItemRepository;
use Psr\Log\LoggerInterface;

abstract class ItemAction extends Action
{
    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * @param LoggerInterface $logger
     * @param ItemRepository  $itemRepository
     */
    public function __construct(LoggerInterface $logger, ItemRepository $itemRepository)
    {
        parent::__construct($logger);
        $this->itemRepository = $itemRepository;
    }
}
