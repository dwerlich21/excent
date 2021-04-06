<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/login/', fn(Request $request, Response $response) => $this->LoginController->login($request, $response));

$app->post('/login/', fn(Request $request, Response $response) => $this->LoginController->autentication($request, $response));

$app->get('/recover/', fn(Request $request, Response $response) => $this->LoginController->recover($request, $response));

$app->post('/recover/', fn(Request $request, Response $response) => $this->LoginController->saveRecover($request, $response));

$app->put('/recover/', fn(Request $request, Response $response) => $this->LoginController->savePassword($request, $response));

$app->get('/recover/{id}/', fn(Request $request, Response $response) => $this->LoginController->changePassword($request, $response));

$app->get('/logout/', fn(Request $request, Response $response) => $this->LoginController->logout($request, $response));

