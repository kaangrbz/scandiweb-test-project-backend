<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

include_once '../models/Database.php';

$db = new Database();
$connection = $db->getConnection();

if (!isset($_POST['type'])) {
    echo json_encode(array(
        'error' => true,
        'code' => 'bad_request',
        'message' => 'Bad request. type parameter not found'
    ));
    return;
}

$type = $_POST['type'];

switch ($type) {
    case 'dvd':
        $products = $db->getDVDs();
        echo json_encode($products);
        break;
    case 'book':
        $products = $db->getBooks();
        echo json_encode($products);
        break;
    case 'furniture':
        $products = $db->getFurnitures();
        echo json_encode($products);
        break;
    default:
        echo json_encode(array(
            'error' => true,
            'code' => 'null_type',
            'message' => 'Null type parameter'
        ));
        break;
}
