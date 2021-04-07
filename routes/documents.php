<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documents', function () use ($app) {

    $app->get('/sent/', function (Request $request, Response $response) {
        return $this->DocumentController->document($request, $response);
    });

    $app->get('/received/', function (Request $request, Response $response) {
        return $this->DocumentController->received($request, $response);
    });

    $app->get('/category/', function (Request $request, Response $response) {
        return $this->DocumentController->category($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocument($request, $response);
    });

    $app->post('/register/category/', function (Request $request, Response $response) {
        return $this->DocumentController->saveCategory($request, $response);
    });

    $app->get('/received/{id}/', function (Request $request, Response $response) {
        return $this->DocumentController->viewReceived($request, $response);
    });
});