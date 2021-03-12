<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/projetos', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ProjectsController->projects($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->ProjectsController->projectsRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->ProjectsController->saveProjects($request, $response);
    });

    $app->get('/visualizar/{id}/', function (Request $request, Response $response) {
        return $this->ProjectsController->viewProjects($request, $response);
    });

    $app->get('/pdf/[{id}/]', function (Request $request, Response $response) {
        return $this->ProjectsController->projectPdf($request, $response);
    });

    $app->get('/csv/[{id}/]', function (Request $request, Response $response) {
        return $this->ProjectsController->projectCsv($request, $response);
    });
});