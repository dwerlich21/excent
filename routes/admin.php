<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response) {
    return $this->AdminController->index($request, $response);
});

$app->post('/task/register/', function (Request $request, Response $response) {
    return $this->AdminController->saveTask($request, $response);
});

$app->group('/users', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->AdminController->user($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->AdminController->saveUser($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->usersTable($request, $response);
    });
});

$app->group('/clients', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->AdminController->client($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->AdminController->saveClient($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->clientsTable($request, $response);
    });
});

$app->group('/documents', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->AdminController->document($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->AdminController->saveDocument($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->documentsTable($request, $response);
    });
});
