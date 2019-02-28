<?php declare(strict_types=1);

namespace DigestAuthServer\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class ResourceController
{
    protected $view;

    public function __construct(\Slim\Views\Twig $view)
    {
        $this->view = $view;
    }

    public function index(Request $request, Response $response, array $arguments): Response
    {
        $response->getBody()->write("Authenticated");
        return $response;
    }
}
