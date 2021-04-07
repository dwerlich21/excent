<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/messages', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->MessageController->message($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->MessageController->saveMessage($request, $response);
    });

    $app->get('/received/{id}/', function (Request $request, Response $response) {
        return $this->MessageController->viewReceived($request, $response);
    });
});