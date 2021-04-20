<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/documents', function () use ($app) {

    $app->get('/my-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->myFolder($request, $response);
    });

    $app->get('/company-files/', function (Request $request, Response $response) {
        return $this->DocumentController->companyFiles($request, $response);
    });

    $app->post('/register-folder/', function (Request $request, Response $response) {
        return $this->DocumentController->registerFolder($request, $response);
    });

    $app->post('/register-document/', function (Request $request, Response $response) {
        return $this->DocumentController->saveDocument($request, $response);
    });

    $app->delete('/delete/', function (Request $request, Response $response) {
        return $this->DocumentController->deleteFolder($request, $response);
    });
});