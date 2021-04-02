<?php
declare(strict_types=1);

namespace Tests\Application\Actions\Item;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Item\Item;
use App\Domain\Item\ItemNotFoundException;
use App\Domain\Item\ItemRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewItemActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $item = new Item(625, 'Dell Latitude 5480', 1338.37, 'Dell Latitude 5480, 14" FHD, Core i7-7600U, 8GB, 256GB SSD, Linux + Windows 10 Home');

        $itemRepositoryProphecy = $this->prophesize(ItemRepository::class);
        $itemRepositoryProphecy
            ->findItemOfId(625)
            ->willReturn($item)
            ->shouldBeCalledOnce();

        $container->set(ItemRepository::class, $itemRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/items/625');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $item);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsItemNotFoundException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false ,false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();

        $itemRepositoryProphecy = $this->prophesize(ItemRepository::class);
        $itemRepositoryProphecy
            ->findItemOfId(625)
            ->willThrow(new ItemNotFoundException())
            ->shouldBeCalledOnce();

        $container->set(ItemRepository::class, $itemRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/items/625');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The item you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
