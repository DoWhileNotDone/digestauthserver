<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

use JWTServer\Utility\Token;

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

// Check the user is logged in when necessary.
$loggedInMiddleware = function (Request $request, Response $response, callable $next) {

    $route = $request->getAttribute('route');
    $routeName = $route->getName();

    # Define routes that user does not have to be logged in with. All other routes, the user
    # needs to be logged in with.
    $publicRoutesArray = array(
        'login',
        'logout',
    );

    if (!isset($_SESSION['user_email']) && !in_array($routeName, $publicRoutesArray)) {
        // redirect the user to the login page and do not proceed.
        $response = $response->withRedirect('/login');
    } else {
        // Proceed as normal...
        $response = $next($request, $response);
    }

    return $response;
};

// Check that the user has provided a JWT for protected resources
$jwtMiddleware = function (Request $request, Response $response, callable $next) {

    $route = $request->getAttribute('route');
    $routeName = $route->getName();

    # Define routes that user requires a jwt to be supplied.
    $protectedRoutesArray = array(
        'resource',
    );

    // FIXME: Use Middleware options to error successfully.
    if (in_array($routeName, $protectedRoutesArray)) {
        if (!$request->hasHeader('authorization')) {
            die('token not valid!');
        }
        $authorizationArray = $request->getHeader('authorization');
        //FIXME: Check there is only one
        $authorization = array_pop($authorizationArray);

        list($token) = sscanf($authorization, 'Bearer %s');

        if (!Token::validate($token)) {
            die('token not valid!');
        }
    }

    // Proceed as normal...
    $response = $next($request, $response);

    return $response;
};

$app->add($loggedInMiddleware);
$app->add($jwtMiddleware);
$app->add($checkRoute);
