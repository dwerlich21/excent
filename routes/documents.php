<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documents', function () use ($app) {

    $app->get('/my-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->myFolder($request, $response, 0);
    });

    $app->get('/company-files/', function (Request $request, Response $response) {
        return $this->DocumentController->companyFiles($request, $response);
    });

    $app->post('/company-files/register-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->saveFolder($request, $response);
    });

    $app->get('/company-files/filectime/', function (Request $request, Response $response) {
        return $this->DocumentController->filectime($request, $response);
    });

    $app->get('/folders/', function (Request $request, Response $response) {
        return $this->DocumentController->folders($request, $response, 1);
    });

    $app->post('/register-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->registerFolder($request, $response);
    });

    $app->post('/register-document/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocument($request, $response);
    });

    $app->get('/delete/', function (Request $request, Response $response) {
        return $this->DocumentController->deleteFolder($request, $response);
    });

    $app->get('/company-files/delete/', function (Request $request, Response $response) {
        return $this->DocumentController->deleteFolderCompanyFiles($request, $response);
    });

    $app->get('/company-files/{id}/', function (Request $request, Response $response) {
        return $this->DocumentController->viewCompanyFiles($request, $response);
    });
});