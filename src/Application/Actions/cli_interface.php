<?php
declare(strict_type=1);

namespace App\Application\Actions;
use App\Domain\Scheduler\Scheduler;

$app = new Scheduler();

echo 'Running application';
$app->runEngine();

echo 'Beginning Item Pipeline phase';
$app->itemPipelinePhase();

echo 'Returning results from database';

$items = $app->getEngine()->getDatabaseItems();
$requests = $app->getEngine()->getDatabaseRequests();

echo 'Items: ';
foreach($items as $item) {
    echo json_encode($item);
}

echo 'Requests: ';
foreach($requests as $request) {
    echo $request;
}

?>