<?php
declare(strict_types=1);

namespace App\Application\Actions\Item;

use Psr\Http\Message\ResponseInterface as Response;

class ViewItemAction extends ItemAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $itemId = (int) $this->resolveArg('id');
        $item = $this->itemRepository->findItemOfId($itemId);

        $this->logger->info("Item of id `${itemId}` was viewed.");

        return $this->respondWithData($item);
    }
}
