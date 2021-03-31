<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response) {
    return $this->AdminController->index($request, $response);
});

$app->get('/dashboard/messages/api/[{id}/]', function (Request $request, Response $response) {
    return $this->ApiController->messagesDashboard($request, $response);
});

$app->get('/dashboard/tasks/api/[{id}/]', function (Request $request, Response $response) {
    return $this->ApiController->tasksDashboard($request, $response);
});

$app->get('/dashboard/tasks/status/{id}/', function (Request $request, Response $response) {
    return $this->AdminController->taskStatus($request, $response);
});

$app->get('/dashboard/transations/advisors/api/[{id}/]', function (Request $request, Response $response) {
    return $this->ApiController->rankingAdvisors($request, $response);
});

$app->get('/dashboard/transations/managers/api/[{id}/]', function (Request $request, Response $response) {
    return $this->ApiController->rankingManagers($request, $response);
});

$app->get('/dashboard/transations/managers/group/api/[{id}/]', function (Request $request, Response $response) {
    return $this->ApiController->rankingManagersGroup($request, $response);
});
