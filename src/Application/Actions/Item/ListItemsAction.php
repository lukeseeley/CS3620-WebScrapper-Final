<?php
declare(strict_types=1);

namespace App\Application\Actions\Item;

use Psr\Http\Message\ResponseInterface as Response;

class ListItemsAction extends ItemAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $items = $this->itemRepository->findAll();

        $this->logger->info("Item list was viewed.");

        return $this->respondWithData($items);
    }
}
