<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->group('/api/dashboard', function () use ($app) {

    $app->get('/numero_documentos/', fn(Request $request, Response $response) => $this->DashboardController->graphicNumberDocs($request, $response));

    $app->get('/numero_projeto_tipo/', fn(Request $request, Response $response) => $this->DashboardController->graphicProjectByType($request, $response));

    $app->get('/numero_projeto_status/', fn(Request $request, Response $response) => $this->DashboardController->graphicProjectByStatus($request, $response));

    $app->get('/numero_atendimentos/', fn(Request $request, Response $response) => $this->DashboardController->graphicAttendance($request, $response));

    $app->get('/numero_atendimentos_instituicao/', fn(Request $request, Response $response) => $this->DashboardController->graphicAttendanceByInstitution($request, $response));

    $app->get('/numero_atendimentos_bairro/', fn(Request $request, Response $response) => $this->DashboardController->graphicAttendanceByNeighborhood($request, $response));

    $app->get('/numero_atendimentos_status/', fn(Request $request, Response $response) => $this->DashboardController->graphicAttendanceByStatus($request, $response));
});


