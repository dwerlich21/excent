<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/deals', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->DealsController->client($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->DealsController->saveClient($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->clientsTable($request, $response);
    });
});