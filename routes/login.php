<?php declare(strict_types=1);

use JWTServer\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get("/login", function (Request $request, Response $response, array $arguments): Response {
    $response = $this->view->render($response, 'login/form.html', $arguments);
    return $response;
})->setName('login');

$app->post("/login", function (Request $request, Response $response, array $arguments): Response {
    //TODO: Get Form Data
    $parsedBody = $request->getParsedBody();

    $user = User::where('email', $parsedBody['email'])->first();
    if (!$user) {
        echo 'Invalid User';
        return $response->withStatus(404);
    }

    //FIXME: Verify Password...
    if (password_verify($parsedBody['password'], $user->password)) {
        $_SESSION['user_email'] = $user->email;
    } else {
        echo 'Invalid password.';
        die();
    }

    $location = "/";

    //Redirect to location
    return $response->withStatus(302)->withHeader('Location', $location);
})->setName('login');
