<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json');

include_once '../models/Database.php';
include_once '../models/Api.php';
include_once '../models/Helper.php';
include_once '../models/Product.php';

try {

    
    $connection = new Database();
    $connection = $connection->getConnection();
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    
    $api = new Api($connection);
} catch (\Throwable $th) {
    echo Helper::createErrorMessage(false, 'bad_request', $th->getMessage(), 500);
    return;
}

try {
    $requestData = json_decode(file_get_contents('php://input'), true);
} catch (\Throwable $th) {
    echo Helper::createErrorMessage(false, 'bad_request', 'Bad request, incoming data is not int JSON format', 400);
    return;
}

try {

    switch (strtoupper($requestMethod)) {
        case 'GET':
            echo Helper::createErrorMessage(false, 'bad_request', 'Bad request. Method is not allowed', 405);
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
    echo json_encode(array(
        'message' => $th->getMessage()
    ));
}
