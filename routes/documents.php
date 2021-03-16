<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documents', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->DocumentController->document($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocument($request, $response);
    });

    $app->get('/delete/api/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->documentDelete($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->documentsTable($request, $response);
    });
});