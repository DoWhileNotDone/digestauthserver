<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;


// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
//
$checkRoute = function (Request $request, Response $response, callable $next) {
    $route = $request->getAttribute('route');

    //FIXME: Check route is valid.
    if (!$route) {
        die('Not A Valid Route');
    }

    $routeName = $route->getName();

    if (!$routeName) {
        die('Not A Valid Route Name');
    }

    $response = $next($request, $response);
    return $response;
};

$app->add($checkRoute);
