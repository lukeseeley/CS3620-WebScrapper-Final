<?php
declare(strict_types=1);

use App\Application\Actions\Item\ListItemsAction;
use App\Application\Actions\Item\ViewItemAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/items', function (Group $group) {
        $group->get('', ListItemsAction::class);
        $group->get('/{id}', ViewItemAction::class);
    });
};
