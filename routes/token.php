<?php declare(strict_types=1);

use JWTServer\Utility\Token;

use Slim\Http\Request;
use Slim\Http\Response;

$app->get("/token", function (Request $request, Response $response, array $arguments): Response {

    //TODO: Check Token Request
    $payload = [
      'loggedInAs' => $_SESSION['user_email'],
      'iat' => strtotime('now'),
    ];

    $token = Token::create($payload);

    //Display Token
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($token, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
})->setName('token');

$app->get("/decode/{token}", function (Request $request, Response $response, array $arguments): Response {

    $token_parts = Token::decode($arguments['token']);

    return $response->withStatus(200)
        ->withHeader("Content-Type", "text/plain")
        ->write(var_export($token_parts, true));
})->setName('decode');
