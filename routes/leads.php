<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/leads', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->LeadController->lead($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->LeadController->saveLead($request, $response);
    });
});