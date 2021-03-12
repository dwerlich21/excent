<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->group('/contatos', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ContactsController->contacts($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->ContactsController->contactsRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->ContactsController->saveContacts($request, $response);
    });

});