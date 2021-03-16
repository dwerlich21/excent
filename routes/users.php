<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/users', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->UserController->user($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->UserController->saveUser($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->usersTable($request, $response);
    });
});