<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/atendimentos', function () use ($app) {

    $app->get('/', function (Request $request, Response $response) {
        return $this->AttendanceController->attendance($request, $response);
    });

    $app->get('/cadastro/[{id}/]', function (Request $request, Response $response) {
        return $this->AttendanceController->attendanceRegister($request, $response);
    });

    $app->post('/cadastro/', function (Request $request, Response $response) {
        return $this->AttendanceController->saveAttendance($request, $response);
    });

    $app->get('/visualizar/{id}/', function (Request $request, Response $response) {
        return $this->AttendanceController->viewAttendance($request, $response);
    });

    $app->get('/pdf/[{id}/]', function (Request $request, Response $response) {
        return $this->AttendanceController->attendancePdf($request, $response);
    });

    $app->get('/csv/[{id}/]', function (Request $request, Response $response) {
        return $this->AttendanceController->attendanceCsv($request, $response);
    });
});