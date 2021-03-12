<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response) {
    return $this->AdminController->index($request, $response);
});

$app->get('/agenda/', function (Request $request, Response $response) {
    return $this->AdminController->schedule($request, $response);
});

$app->get('/agenda/visualizar/', function (Request $request, Response $response) {
    return $this->AdminController->getSchedule($request, $response);
});


$app->group('/galeria', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->AdminController->gallery($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->AdminController->saveImage($request, $response);
    });

});