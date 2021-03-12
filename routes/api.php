<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


$app->group('/api', function () use ($app) {

    $app->get('/usuarios/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->users($request, $response));

    $app->get('/instituicoes/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->institutions($request, $response));

    $app->get('/bairros/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->neighborhoods($request, $response));

    $app->get('/reunioes/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->meetings($request, $response));

    $app->get('/contatos/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->contacts($request, $response));

    $app->get('/projetos/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->projects($request, $response));

    $app->get('/atendimentos/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->attendance($request, $response));

    $app->get('/tipo_documento/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->documentType($request, $response));

    $app->get('/tipo_atendimento/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->typeOfService($request, $response));

    $app->get('/tipo_projeto/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->typeOfProject($request, $response));

    $app->get('/documentos/[{id}/]', fn(Request $request, Response $response) => $this->ApiController->docs($request, $response));

});



