<?php declare(strict_types=1);

use DigestAuthServer\Controllers\ResourceController;

$container = $app->getContainer();
$container['ResourceController'] = function ($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new ResourceController($view);
};

$app->get('/resource', \ResourceController::class . ':index')->setName('resource');
