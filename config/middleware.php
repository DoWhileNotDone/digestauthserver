<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

use DigestAuthServer\Utility\Digest;

// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
//
$checkRoute = function (Request $request, Response $response, callable $next) {
    $route = $request->getAttribute('route');
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

$digestAuthentication = function (Request $request, Response $response, callable $next) {

    $route = $request->getAttribute('route');
    $routeName = $route->getName();

    # Define routes that require digest authentication to be performed
    # needs to be logged in with. FIXME: Could be inverted.
    $digestRequiredRoutesArray = array(
      'resource',
    );

    if (in_array($routeName, $digestRequiredRoutesArray)) {
        if (!Digest::getDigest() || !Digest::valid()) {
            // Response with 401 Unauthorized, and our credentials
            $response = Digest::setDigestDetails($response);
            return $response;
        }
    }
    // Proceed as normal, we are either authenticated, or it is not required
    // for this route
    $response = $next($request, $response);
    return $response;
};

$app->add($digestAuthentication);
$app->add($checkRoute);
