<?php declare(strict_types=1);

use JWTServer\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

// Define named route
$app->get('/', function (Request $request, Response $response, array $arguments): Response {
    $arguments['user'] = User::where('email', $_SESSION['user_email'])->first();
    return $this->view->render($response, 'index.html', $arguments);
})->setName('home');
