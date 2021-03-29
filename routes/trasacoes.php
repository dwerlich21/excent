<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/transactions', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->TransactionsController->transactions($request, $response);
    });

    $app->post('/register/', function (Request $request, Response $response) {
        return $this->TransactionsController->saveTransactions($request, $response);
    });

    $app->get('/delete/api/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->transactionsDelete($request, $response);
    });

    $app->get('/api/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->transactionssTable($request, $response);
    });
});