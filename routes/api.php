<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/api/documents', function () use ($app) {

    $app->get('/my-folder/', function (Request $request, Response $response) {
        return $this->ApiController->myFoldersTable($request, $response);
    });

    $app->get('/company-files/', function (Request $request, Response $response) {
        return $this->ApiController->companyFilesTable($request, $response);
    });

    $app->get('/my-folder/delete/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->myFolderDeleteDoc($request, $response);
    });
});

$app->group('/api/messages', function () use ($app) {

    $app->get('/delete/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->messageDelete($request, $response);
    });

    $app->get('/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->messagesTable($request, $response);
    });
});

$app->group('/api/deals', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->ApiController->dealsTable($request, $response);
    });

    $app->get('/update/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->statusUpdate($request, $response);
    });

    $app->get('/activities/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->ActivityDeal($request, $response);
    });
});

$app->group('/api/leads', function () use ($app) {

    $app->get('/delete/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->LeadDelete($request, $response);
    });

    $app->get('/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->leadsTable($request, $response);
    });
});

$app->group('/api/transactions', function () use ($app) {

    $app->get('/delete/{id}/', function (Request $request, Response $response) {
        return $this->ApiController->transactionsDelete($request, $response);
    });

    $app->get('/', function (Request $request, Response $response) {
        return $this->ApiController->transactionssTable($request, $response);
    });
});

$app->group('/api/dashboard', function () use ($app) {

    $app->get('/tasks/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->tasksDashboard($request, $response);
    });

    $app->get('/transations/advisors/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->rankingAdvisors($request, $response);
    });

    $app->get('/transations/managers/group/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->rankingManagersGroup($request, $response);
    });

    $app->get('/transations/managers/[{id}/]', function (Request $request, Response $response) {
        return $this->ApiController->rankingManagers($request, $response);
    });
});