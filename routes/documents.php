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

    $app->get('/delete/api/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->documentDelete($request, $response);
    });

    $app->get('/update/api/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->updateStatus($request, $response);
    });

    $app->get('/category/delete/api/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->categoryDelete($request, $response);
    });

    $app->get('/sent/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->documentsSentTable($request, $response);
    });

    $app->get('/received/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->documentsReceivedTable($request, $response);
    });

    $app->get('/category/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->documentsCategoryTable($request, $response);
    });
});