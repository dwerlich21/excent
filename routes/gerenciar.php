<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->group('/gerenciar/bairros', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->neighborhoods($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveNeighborhoods($request, $response);
    });
});

$app->group('/gerenciar/tipo_documento', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->documentType($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveDocumentType($request, $response);
    });
});

$app->group('/gerenciar/tipo_atendimento', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->attendanceType($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveAttendanceType($request, $response);
    });
});

$app->group('/gerenciar/tipo_projeto', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->typeOfProject($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveTypeOfProject($request, $response);
    });
});

$app->group('/gerenciar/detalhes_gabinete', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->cabinetDetails($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveCabinetDetails($request, $response);
    });
});

$app->group('/gerenciar/usuarios', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->users($request, $response);
    });

    $app->post('/cadastro/[{id/]', function (Request $request, Response $response) {
        return $this->ManagementController->saveUser($request, $response);
    });
});

$app->group('/gerenciar/instituicoes', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ManagementController->institution($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->ManagementController->institutionsRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->ManagementController->saveInstitutions($request, $response);
    });
});
