<?php
declare(strict_types=1);

namespace Tests\Application\Actions\Item;

use App\Application\Actions\ActionPayload;
use App\Domain\Item\ItemRepository;
use App\Domain\Item\Item;
use DI\Container;
use Tests\TestCase;

class ListItemsActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $item = new Item(625, 'Dell Latitude 5480', 1338.37, 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home');

        $itemRepositoryProphecy = $this->prophesize(ItemRepository::class);
        $itemRepositoryProphecy
            ->findAll()
            ->willReturn([$item])
            ->shouldBeCalledOnce();

        $container->set(ItemRepository::class, $itemRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/items');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$item]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
