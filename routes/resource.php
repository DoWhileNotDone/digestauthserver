<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

// Define named route
$app->get('/resource', function (Request $request, Response $response, array $arguments): Response {
    $response->getBody()->write("Authenticated");
    return $response;
})->setName('resource');
