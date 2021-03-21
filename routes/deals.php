<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/deals', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->DealController->deal($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->DealController->saveDeal($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->dealsTable($request, $response);
    });

    $app->get('/{id}/', function (Request $request, Response $response) {
        return $this->DealController->viewDeal($request, $response);
    });
});