<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/calendar', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->CalendarController->calendar($request, $response);
    });

    $app->get('/view/', function (Request $request, Response $response) {
        return $this->CalendarController->getTasks($request, $response);
    });
});