<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documents', function () use ($app) {

    $app->get('/folders/', function (Request $request, Response $response) {
        return $this->DocumentController->companyFiles($request, $response);
    });

    $app->get('/my-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->myFolder($request, $response);
    });

    $app->get('/company-files/', function (Request $request, Response $response) {
        return $this->DocumentController->companyFiles($request, $response);
    });

    $app->post('/my-folder/register/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocumentMyFolder($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocument($request, $response);
    });
});