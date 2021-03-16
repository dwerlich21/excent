<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/clients', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ClientController->client($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->ClientController->saveClient($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->clientsTable($request, $response);
    });
});