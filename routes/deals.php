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

    $app->post('/activity/register/', function (Request $request, Response $response) {
        return $this->DealController->saveActivityDeal($request, $response);
    });

    $app->post('/activity/{id}/', function (Request $request, Response $response) {
        return $this->DealController->updateActivityStatus($request, $response);
    });

    $app->get('/view/{id}/', function (Request $request, Response $response) {
        return $this->DealController->viewDeal($request, $response);
    });
});