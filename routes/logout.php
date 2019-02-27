<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

$app->any("/logout", function (Request $request, Response $response, array $arguments): Response {
    unset($_SESSION['user_email']);
    $location = "/";
    //Redirect to location
    return $response->withStatus(302)->withHeader('Location', $location);
})->setName('logout');
