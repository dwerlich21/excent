<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/eventos', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->EventsController->events($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->EventsController->eventsRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->EventsController->saveEvents($request, $response);
    });

    $app->get('/visualizar/{id}/', function (Request $request, Response $response) {
        return $this->EventsController->viewEvents($request, $response);
    });

    $app->get('/pdf/{id}/', function (Request $request, Response $response) {
        return $this->EventsController->pdf($request, $response);
    });
});