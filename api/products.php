<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json; charset=utf-8');

include_once '../models/Database.php';
include_once '../models/Api.php';
include_once '../models/Helper.php';
include_once '../models/Product.php';
include_once '../models/HttpStatusCodes.php';

try {

    $connection = new Database();
    $connection = $connection->getConnection();
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    
    $api = new Api($connection);

} catch (\Throwable $th) {
    echo Helper::createErrorMessage(false, 'interval_server_error', $th->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
    return;
}

try {
    $requestData = json_decode(file_get_contents('php://input'), true);
} catch (\Throwable $th) {
    echo Helper::createErrorMessage(false, 'bad_request', 'Bad request, incoming data is not int JSON format', HttpStatusCodes::BAD_REQUEST);
    return;
}

try {

    switch (strtoupper($requestMethod)) {
        case 'GET':
            echo Helper::createErrorMessage(false, 'bad_request', 'Bad request. Method is not allowed', HttpStatusCodes::METHOD_NOT_ALLOWED);
            return;
        case 'POST':
            echo $api->getProducts();
            return;
        case 'PUT':
            echo $api->addProduct($requestData);
            return;
        case 'DELETE':
            echo $api->deleteProducts($requestData['skus']);
            return;
        default:
            break;
    }
} catch (\Throwable $th) {
    echo Helper::createErrorMessage(false, 'interval_server_error',  $th->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
}
