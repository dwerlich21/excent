<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documentos', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->DocsController->docs($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->DocsController->docsRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->DocsController->saveDocs($request, $response);
    });

    $app->get('/pdf/{id}/', function (Request $request, Response $response) {
        return $this->DocsController->pdf($request, $response);
    });
});