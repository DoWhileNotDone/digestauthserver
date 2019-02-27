<?php declare(strict_types=1);

use JWTServer\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get("/user/new", function (Request $request, Response $response, array $arguments): Response {
      $response = $this->view->render($response, 'user/new.html', $arguments);
      return $response;
});

$app->post("/user/new", function (Request $request, Response $response, array $arguments): Response {

    $parsedBody = $request->getParsedBody();

    $user = new User();

    //TODO: Sanitize the input
    $user->name = $parsedBody['name'];
    $user->email = $parsedBody['email'];
    //Hash the supplied password
    $user->password = password_hash($parsedBody['password'], PASSWORD_DEFAULT);

    //TODO Validate the request...
    // $validation = $this->validator->validate($application->toArray(), $application->getRules());
    //TODO: Return Form with errors
    // if($validation->fails()) {
    //   $this->logger->warning("Invalid POST data sent, not creating", $album->toArray());
    //   return $response->withStatus(400);
    // }

    //https://www.oauth.com/oauth2-servers/client-registration/client-id-secret/

    $user->save();

    $location = "{$user->id}";

    //Redirect to location
    return $response->withStatus(302)->withHeader('Location', $location);
});


$app->get("/user/{id:[0-9]+}", function (Request $request, Response $response, array $arguments): Response {

    $user = User::find($arguments['id']);

    if (!$user) {
        return $response->withStatus(404);
    }

    $arguments['user'] = $user;

    $response = $this->view->render($response, 'user/view.html', $arguments);

    return $response;
});
